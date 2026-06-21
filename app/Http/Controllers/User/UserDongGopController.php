<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\DongGop;
use App\Models\ChiTietDongGop;
use App\Models\ChienDichCuuTro;
use App\Models\HangHoa;
use App\Models\NguonLucChienDich;
use Illuminate\Http\Request;

class UserDongGopController extends Controller
{
    public function index(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $dongGops = DongGop::with([
                'chienDich',
                'chienDich.diaDiem',
                'chienDich.nhom',
                'chienDich.nhom.thanhViens.nguoiDung',
                'chiTietDongGops.hangHoa',
                'thanhVienTiepNhan.nguoiDung',
            ])
            ->where('idNguoiUngHo', $idNguoiDung)
            ->orderBy('idDongGop', 'desc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $dongGops = $dongGops
                ->filter(function ($dongGop) use ($tuKhoa, $tuKhoaKhongDau) {
                    $chienDich = $dongGop->chienDich;
                    $nhom = $chienDich->nhom ?? null;
                    $diaDiem = $chienDich->diaDiem ?? null;

                    $nhomTruong = $nhom?->thanhViens
                        ?->firstWhere('vaiTro', 'Nhóm trưởng');

                    $nguoiDungNhomTruong = $nhomTruong?->nguoiDung;
                    $nguoiTiepNhan = $dongGop->thanhVienTiepNhan?->nguoiDung;

                    $hangHoaDongGop = $dongGop->chiTietDongGops
                        ->map(function ($chiTiet) {
                            return implode(' ', [
                                $chiTiet->hangHoa->tenHangHoa ?? '',
                                $chiTiet->soLuong ?? '',
                                $chiTiet->hangHoa->donViTinh ?? '',
                                $chiTiet->trangThai ?? '',
                            ]);
                        })
                        ->implode(' ');

                    $noiDungTimKiem = implode(' ', [
                        $dongGop->idDongGop,
                        $chienDich->tenChienDich ?? '',
                        $nhom->tenNhom ?? '',
                        $nguoiDungNhomTruong->hoTen ?? '',
                        $nguoiDungNhomTruong->tenDangNhap ?? '',
                        $nguoiTiepNhan->hoTen ?? '',
                        $nguoiTiepNhan->tenDangNhap ?? '',
                        $dongGop->thoiGianDongGop ?? '',
                        $diaDiem->chiTietDiaDiem ?? '',
                        $diaDiem->phuongXa ?? '',
                        $diaDiem->tinhThanh ?? '',
                        $hangHoaDongGop,
                    ]);

                    return str_contains(
                        mb_strtolower($noiDungTimKiem, 'UTF-8'),
                        mb_strtolower($tuKhoa, 'UTF-8')
                    ) || str_contains(
                        $this->boDauTiengViet($noiDungTimKiem),
                        $tuKhoaKhongDau
                    );
                })
                ->values();
        }

        return view('user.dong_gop.index', compact(
            'dongGops',
            'tuKhoa'
        ));
    }

    public function create()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $chienDichs = ChienDichCuuTro::with([
                'nhom.nhomTruong',
                'suKien',
                'diaDiem',
            ])
            ->whereNotIn('trangThai', [
                'Hoàn thành',
                'Đã hoàn thành',
                'Đã hủy',
                'Đã ẩn',
            ])
            ->whereHas('nguonLucs', function ($query) {
                $query->where('trangThai', 'Đang kêu gọi');
            })
            ->orderBy('idChienDich', 'desc')
            ->get();

        $nguonLucs = NguonLucChienDich::with([
                'hangHoa.danhMucHang',
            ])
            ->whereIn('idChienDich', $chienDichs->pluck('idChienDich'))
            ->where('trangThai', 'Đang kêu gọi')
            ->orderBy('idNguonLuc', 'asc')
            ->get()
            ->map(function ($nguonLuc) {
                $soLuongCanKeuGoi = (float) ($nguonLuc->soLuongCanKeuGoi ?? 0);
                $soLuongDaNhan = (float) ($nguonLuc->soLuongDaNhan ?? 0);

                $nguonLuc->soLuongConCan = max(
                    $soLuongCanKeuGoi - $soLuongDaNhan,
                    0
                );

                return $nguonLuc;
            })
            ->filter(function ($nguonLuc) {
                return $nguonLuc->soLuongConCan > 0;
            })
            ->values();

        return view('user.dong_gop.create', compact(
            'chienDichs',
            'nguonLucs'
        ));
    }

    public function store(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $request->validate([
            'idChienDich' => 'required|exists:ChienDichCuuTro,idChienDich',
            'ghiChu' => 'nullable|string|max:255',
            'hangHoas' => 'required|array|min:1',
        ], [
            'idChienDich.required' => 'Vui lòng chọn chiến dịch.',
            'idChienDich.exists' => 'Chiến dịch không hợp lệ.',
            'hangHoas.required' => 'Vui lòng chọn ít nhất một mặt hàng đóng góp.',
        ]);

        $chienDich = ChienDichCuuTro::where('idChienDich', $request->idChienDich)
            ->whereNotIn('trangThai', [
                'Hoàn thành',
                'Đã hoàn thành',
                'Đã hủy',
                'Đã ẩn',
            ])
            ->first();

        if (!$chienDich) {
            return back()
                ->withInput()
                ->with('error', 'Chỉ có thể đóng góp cho chiến dịch chưa hoàn thành.');
        }

        $hangHoasDuocChon = collect($request->input('hangHoas', []))
            ->filter(function ($duLieu) {
                return !empty($duLieu['chon']);
            });

        if ($hangHoasDuocChon->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Vui lòng chọn ít nhất một mặt hàng đóng góp.');
        }

        $idHangHoaDuocChon = $hangHoasDuocChon
            ->keys()
            ->map(fn ($idHangHoa) => (int) $idHangHoa)
            ->values();

        $idHangHoaHopLes = NguonLucChienDich::where('idChienDich', $request->idChienDich)
            ->whereIn('idHangHoa', $idHangHoaDuocChon)
            ->where('trangThai', 'Đang kêu gọi')
            ->pluck('idHangHoa')
            ->map(fn ($idHangHoa) => (int) $idHangHoa)
            ->toArray();

        foreach ($hangHoasDuocChon as $idHangHoa => $duLieu) {
            $idHangHoa = (int) $idHangHoa;

            if (!in_array($idHangHoa, $idHangHoaHopLes, true)) {
                return back()
                    ->withInput()
                    ->with('error', 'Có hàng hóa không thuộc danh sách nguồn lực đang kêu gọi của chiến dịch.');
            }

            $soLuong = $duLieu['soLuong'] ?? null;

            if (!is_numeric($soLuong) || (float) $soLuong <= 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Vui lòng nhập số lượng lớn hơn 0 cho các mặt hàng đã chọn.');
            }

            if (!empty($duLieu['hanSuDung'])) {
                $request->validate([
                    "hangHoas.$idHangHoa.hanSuDung" => 'date',
                ], [
                    "hangHoas.$idHangHoa.hanSuDung.date" => 'Hạn sử dụng không hợp lệ.',
                ]);
            }
        }

        $dongGop = DongGop::create([
            'idChienDich' => $request->idChienDich,
            'idNguoiUngHo' => $idNguoiDung,
            'idNguoiTiepNhan' => null,
            'ghiChu' => $request->ghiChu,
            'thoiGianDongGop' => now(),
        ]);

        foreach ($hangHoasDuocChon as $idHangHoa => $duLieu) {
            ChiTietDongGop::create([
                'idDongGop' => $dongGop->idDongGop,
                'idHangHoa' => (int) $idHangHoa,
                'soLuong' => (float) $duLieu['soLuong'],
                'hanSuDung' => $duLieu['hanSuDung'] ?? null,
                'trangThai' => 'Chờ xác nhận',
            ]);
        }

        $dongGop->load('nguoiUngHo');

        $chienDich = ChienDichCuuTro::with('nhom.thanhViens')
            ->find($dongGop->idChienDich);

        if ($chienDich && $chienDich->nhom) {
            foreach ($chienDich->nhom->thanhViens as $thanhVien) {
                ThongBao::create([
                    'tieuDe' => ($dongGop->nguoiUngHo->hoTen ?? 'Người dân')
                        . ' đóng góp cho chiến dịch '
                        . $chienDich->tenChienDich,
                    'noiDung' => implode("\n", [
                        'Ghi chú: ' . ($dongGop->ghiChu ?: 'Không có ghi chú'),
                    ]),
                    'doiTuong' => 'Cá nhân',
                    'nguoiTao' => $dongGop->nguoiUngHo->hoTen ?? 'Người đóng góp',
                    'idNguoiNhan' => $thanhVien->idNguoiDung,
                    'anhDaiDien' => $dongGop->nguoiUngHo->anhDaiDien ?? null,
                    'hinhAnh' => null,
                    'duongDan' => '/nhom/' . $chienDich->idNhom . '/chien-dich/' . $chienDich->idChienDich . '#dong-gop',
                    'thoiGianTao' => now(),
                    'trangThai' => 'Hiển thị',
                ]);
            }
        }

        return redirect('/user/dong-gop')
            ->with('success', 'Gửi đăng ký đóng góp thành công. Vui lòng chờ nhóm tình nguyện xác nhận.');
    }

    public function show(int $idDongGop)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $dongGop = DongGop::with([
                'chienDich',
                'chienDich.diaDiem',
                'chienDich.nhom',
                'chienDich.nhom.thanhViens.nguoiDung',
                'chiTietDongGops.hangHoa.danhMucHang',
                'thanhVienTiepNhan.nguoiDung',
            ])
            ->where('idDongGop', $idDongGop)
            ->where('idNguoiUngHo', $idNguoiDung)
            ->firstOrFail();

        return view('user.dong_gop.show', compact('dongGop'));
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã',
            'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ',
            'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ',
            'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ',
            'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ',
            'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ',
            'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e',
            'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u',
            'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace(
            $coDau,
            $khongDau,
            $chuoi
        );
    }
}