<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\ChienDichCuuTro;
use App\Models\NhomTinhNguyen;
use App\Models\SuKienCuuTro;
use App\Models\ThanhVienNhom;
use App\Models\TiepNhanYeuCau;
use App\Models\YeuCauCuuTro;
use App\Models\HangHoa;
use App\Models\NguonLucChienDich;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhomYeuCauCuuTroController extends Controller
{
    private function kiemTraThanhVien(int $idNhom): array
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')
                    ->with('error', 'Vui lòng đăng nhập.'),
            ];
        }

        $nhom = NhomTinhNguyen::findOrFail($idNhom);

        if ($nhom->trangThai !== 'Đang hoạt động') {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with(
                        'error',
                        'Nhóm này chưa hoạt động, đã ngừng hoạt động hoặc đã bị khóa.'
                    ),
            ];
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->first();

        if (!$thanhVien) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with('error', 'Bạn không thuộc nhóm này.'),
            ];
        }

        return [
            'hopLe' => true,
            'nhom' => $nhom,
            'thanhVien' => $thanhVien,
            'laNhomTruong' => $thanhVien->vaiTro === 'Nhóm trưởng',
        ];
    }

    public function index(Request $request, int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $tuKhoa = trim((string) $request->input('tuKhoa'));
        $mucDoDangChon = trim((string) $request->input('mucDoKhanCap'));
        $trangThaiDangChon = trim((string) $request->input('trangThai'));
        $tinhThanhDangChon = trim((string) $request->input('tinhThanh'));

        $yeuCausChoTiepNhan = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
            ->whereIn('trangThai', [
                'Chờ tiếp nhận',
                'Cần thêm hỗ trợ',
            ])
            ->whereDoesntHave('tiepNhans', function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom)
                    ->where('trangThai', '!=', 'Hoàn thành');
            })
            ->orderByRaw("
                CASE
                    WHEN trangThai = 'Cần thêm hỗ trợ' THEN 1
                    WHEN mucDoKhanCap = 'Khẩn cấp' THEN 2
                    WHEN mucDoKhanCap = 'Cao' THEN 3
                    WHEN mucDoKhanCap = 'Trung bình' THEN 4
                    WHEN mucDoKhanCap = 'Thấp' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('idYeuCau', 'desc')
            ->get();

        $yeuCausDaTiepNhan = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.chienDich',
                'tiepNhans.nhom',
            ])
            ->whereHas('tiepNhans', function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom);
            })
            ->orderByRaw("
                CASE
                    WHEN trangThai = 'Hoàn thành' THEN 1
                    ELSE 0
                END
            ")
            ->orderBy('idYeuCau', 'desc')
            ->get();

        $tatCaYeuCauTruocLoc = $yeuCausChoTiepNhan
            ->merge($yeuCausDaTiepNhan);

        $danhSachTinhThanh = $tatCaYeuCauTruocLoc
            ->pluck('diaDiem.tinhThanh')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $boLocYeuCau = function ($yeuCau) use (
            $tuKhoa,
            $mucDoDangChon,
            $trangThaiDangChon,
            $tinhThanhDangChon
        ) {
            if (
                $mucDoDangChon !== ''
                && $yeuCau->mucDoKhanCap !== $mucDoDangChon
            ) {
                return false;
            }

            if (
                $trangThaiDangChon !== ''
                && $yeuCau->trangThai !== $trangThaiDangChon
            ) {
                return false;
            }

            if (
                $tinhThanhDangChon !== ''
                && ($yeuCau->diaDiem->tinhThanh ?? '') !== $tinhThanhDangChon
            ) {
                return false;
            }

            if ($tuKhoa !== '') {
                $diaDiem = $yeuCau->diaDiem;

                $noiDungTimKiem = implode(' ', [
                    $yeuCau->idYeuCau,
                    $yeuCau->tieuDeYeuCau,
                    $yeuCau->moTa,
                    $yeuCau->nguoiGui->hoTen ?? '',
                    $yeuCau->soNguoi,
                    $yeuCau->mucDoKhanCap,
                    $yeuCau->trangThai,
                    $yeuCau->thoiGianGui,
                    $diaDiem->chiTietDiaDiem ?? '',
                    $diaDiem->phuongXa ?? '',
                    $diaDiem->tinhThanh ?? '',
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);
                $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

                return str_contains(
                    mb_strtolower($noiDungTimKiem, 'UTF-8'),
                    mb_strtolower($tuKhoa, 'UTF-8')
                ) || str_contains(
                    mb_strtolower($noiDungKhongDau, 'UTF-8'),
                    mb_strtolower($tuKhoaKhongDau, 'UTF-8')
                );
            }

            return true;
        };

        $yeuCausChoTiepNhan = $yeuCausChoTiepNhan
            ->filter($boLocYeuCau)
            ->values();

        $yeuCausDaTiepNhan = $yeuCausDaTiepNhan
            ->filter($boLocYeuCau)
            ->values();

        return view('nhom.yeu_cau_cuu_tro.index', compact(
            'nhom',
            'laNhomTruong',
            'yeuCausChoTiepNhan',
            'yeuCausDaTiepNhan',
            'danhSachTinhThanh',
            'mucDoDangChon',
            'trangThaiDangChon',
            'tinhThanhDangChon'
        ));
    }

    public function show(int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.chienDich',
                'tiepNhans.nhom',
            ])
            ->findOrFail($idYeuCau);

        $tiepNhanDangXuLyCuaNhom = TiepNhanYeuCau::with('chienDich')
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->where('trangThai', '!=', 'Hoàn thành')
            ->orderBy('idTiepNhan', 'desc')
            ->first();

        $tiepNhanGanNhatCuaNhom = TiepNhanYeuCau::with('chienDich')
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->orderBy('idTiepNhan', 'desc')
            ->first();

        $tiepNhanCuaNhom = $tiepNhanDangXuLyCuaNhom
            ?: $tiepNhanGanNhatCuaNhom;

        $daDuocNhomTiepNhan = $tiepNhanDangXuLyCuaNhom !== null;

        $tiepNhanDangCanThem = TiepNhanYeuCau::with('nhom')
            ->where('idYeuCau', $idYeuCau)
            ->where('trangThai', 'Cần thêm hỗ trợ')
            ->where('idNhom', '!=', $idNhom)
            ->first();

        $cacTiepNhanKhacDangCanThem = TiepNhanYeuCau::with([
                'nhom',
                'chienDich',
            ])
            ->where('idYeuCau', $idYeuCau)
            ->where('trangThai', 'Cần thêm hỗ trợ')
            ->where('idNhom', '!=', $idNhom)
            ->orderBy('idTiepNhan', 'asc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.show', compact(
            'nhom',
            'laNhomTruong',
            'yeuCau',
            'daDuocNhomTiepNhan',
            'tiepNhanCuaNhom',
            'tiepNhanDangCanThem',
            'cacTiepNhanKhacDangCanThem'
        ));
    }

    public function createTiepNhan(int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
            ])
            ->findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDangXuLyYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đang tiếp nhận yêu cầu này nên không thể tiếp nhận thêm lượt mới.'
            );
        }

        $chienDichs = ChienDichCuuTro::where('idNhom', $idNhom)
            ->whereNotIn('trangThai', [
                'Hoàn thành',
                'Đã hoàn thành',
                'Đã hủy',
                'Đã ẩn',
            ])
            ->orderBy('idChienDich', 'desc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.tiep_nhan', compact(
            'nhom',
            'yeuCau',
            'chienDichs'
        ));
    }

    public function storeTiepNhan(
        Request $request,
        int $idNhom,
        int $idYeuCau
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $request->validate([
            'idChienDich' => 'required|exists:ChienDichCuuTro,idChienDich',
            'thoiGianDuKienHoTro' => 'nullable|date|after_or_equal:today',
            'noiDungDamNhan' => 'required|string',
        ], [
            'idChienDich.required' => 'Vui lòng chọn chiến dịch tiếp nhận yêu cầu.',
            'idChienDich.exists' => 'Chiến dịch không hợp lệ.',
            'thoiGianDuKienHoTro.date' => 'Ngày dự kiến hỗ trợ không hợp lệ.',
            'thoiGianDuKienHoTro.after_or_equal' => 'Ngày dự kiến hỗ trợ phải từ hôm nay trở đi.',
            'noiDungDamNhan.required' => 'Vui lòng nhập nội dung nhóm sẽ đảm nhận.',
        ]);

        $yeuCau = YeuCauCuuTro::findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDangXuLyYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đang tiếp nhận yêu cầu này nên không thể tiếp nhận thêm lượt mới.'
            );
        }

        $chienDich = ChienDichCuuTro::where('idChienDich', $request->idChienDich)
            ->where('idNhom', $idNhom)
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
                ->with('error', 'Chiến dịch không thuộc nhóm hoặc không còn hoạt động.');
        }

        $this->taoLuotTiepNhan(
            $yeuCau,
            $chienDich,
            $idNhom,
            $request->thoiGianDuKienHoTro,
            $request->noiDungDamNhan
        );

        $nhom = $kiemTra['nhom'];

        ThongBao::create([
            'tieuDe' => $nhom->tenNhom . ' tiếp nhận yêu cầu ' . $yeuCau->tieuDeYeuCau,
            'noiDung' => implode("\n", [
                'Dự kiến hỗ trợ: ' . (
                    $request->thoiGianDuKienHoTro
                        ? \Carbon\Carbon::parse($request->thoiGianDuKienHoTro)->format('d/m/Y')
                        : 'Chưa xác định'
                ),
                $request->noiDungDamNhan,
            ]),
            'doiTuong' => 'Cá nhân',
            'nguoiTao' => $nhom->tenNhom ?? 'Nhóm tình nguyện',
            'idNguoiNhan' => $yeuCau->idNguoiGui,
            'anhDaiDien' => $nhom->anhDaiDien ?? null,
            'hinhAnh' => null,
            'duongDan' => '/user/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau,
            'thoiGianTao' => now(),
            'trangThai' => 'Hiển thị',
        ]);

        return redirect(
            '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
        )->with('success', 'Tiếp nhận yêu cầu cứu trợ thành công.');
    }

    public function createChienDichTuYeuCau(
        int $idNhom,
        int $idYeuCau
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền tạo chiến dịch từ yêu cầu cứu trợ.'
            );
        }

        $nhom = $kiemTra['nhom'];

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
            ])
            ->findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDangXuLyYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đang tiếp nhận yêu cầu này nên không thể tạo chiến dịch tiếp nhận mới.'
            );
        }

        $suKiens = SuKienCuuTro::where('trangThai', '!=', 'Đã ẩn')
            ->orderBy('idSuKien', 'desc')
            ->get();

        $hangHoas = HangHoa::with('danhMucHang')
            ->where('trangThai', 'Đang sử dụng')
            ->where(function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom)
                    ->orWhereNull('idNhom');
            })
            ->orderByRaw(
                'CASE WHEN idNhom = ? THEN 0 ELSE 1 END',
                [$idNhom]
            )
            ->orderBy('idHangHoa', 'asc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.tao_chien_dich', compact(
            'nhom',
            'yeuCau',
            'suKiens',
            'hangHoas'
        ));
    }

    public function storeChienDichTuYeuCau(
        Request $request,
        int $idNhom,
        int $idYeuCau
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền tạo chiến dịch từ yêu cầu cứu trợ.'
            );
        }

        $request->validate([
            'idSuKien' => 'required|exists:SuKienCuuTro,idSuKien',
            'tenChienDich' => 'required|string|max:255',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'required|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daXacNhanCuuTro' => 'nullable|in:0,1',
            'ghiChuXacNhan' => 'nullable|string',
            'trangThaiChienDich' => 'required|string|max:255',

            'thoiGianDuKienHoTro' => 'nullable|date',
            'noiDungDamNhan' => 'required|string',
        ], [
            'idSuKien.required' => 'Vui lòng chọn sự kiện cứu trợ.',
            'idSuKien.exists' => 'Sự kiện cứu trợ không hợp lệ.',
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'ngayBatDau.required' => 'Vui lòng chọn ngày bắt đầu.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThaiChienDich.required' => 'Vui lòng chọn trạng thái chiến dịch.',
            'thoiGianDuKienHoTro.date' => 'Thời gian dự kiến hỗ trợ không hợp lệ.',
            'noiDungDamNhan.required' => 'Vui lòng nhập nội dung nhóm sẽ đảm nhận.',
        ]);

        $yeuCau = YeuCauCuuTro::with('diaDiem')
            ->findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDangXuLyYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đang tiếp nhận yêu cầu này nên không thể tạo chiến dịch tiếp nhận mới.'
            );
        }

        $nguonLucInput = $request->input('nguonLuc', []);

        $nguonLucDuocChon = collect($nguonLucInput)
            ->filter(function ($duLieu) {
                return !empty($duLieu['chon']);
            });

        if ($nguonLucDuocChon->isEmpty()) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Vui lòng chọn ít nhất một mặt hàng cần kêu gọi cho chiến dịch.'
                );
        }

        $idHangHoaDuocChon = $nguonLucDuocChon
            ->keys()
            ->map(function ($idHangHoa) {
                return (int) $idHangHoa;
            })
            ->values();

        $idHangHoaHopLes = HangHoa::whereIn(
                'idHangHoa',
                $idHangHoaDuocChon
            )
            ->where('trangThai', 'Đang sử dụng')
            ->where(function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom)
                    ->orWhereNull('idNhom');
            })
            ->pluck('idHangHoa')
            ->map(function ($idHangHoa) {
                return (int) $idHangHoa;
            })
            ->toArray();

        foreach ($nguonLucDuocChon as $idHangHoa => $duLieu) {
            $idHangHoa = (int) $idHangHoa;

            if (!in_array($idHangHoa, $idHangHoaHopLes, true)) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Có mặt hàng không hợp lệ hoặc không thuộc phạm vi sử dụng của nhóm.'
                    );
            }

            $soLuongCanKeuGoi = $duLieu['soLuongCanKeuGoi'] ?? null;

            if (
                !is_numeric($soLuongCanKeuGoi)
                || (float) $soLuongCanKeuGoi <= 0
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Vui lòng nhập số lượng cần kêu gọi lớn hơn 0 cho các mặt hàng đã chọn.'
                    );
            }
        }

        $idChienDichMoi = null;

        DB::transaction(function () use (
            $request,
            $idNhom,
            $yeuCau,
            $nguonLucDuocChon,
            &$idChienDichMoi
        ) {
            $chienDich = ChienDichCuuTro::create([
                'idNhom' => $idNhom,
                'idSuKien' => $request->idSuKien,
                'idDiaDiem' => $yeuCau->idDiaDiem,
                'tenChienDich' => trim($request->tenChienDich),
                'moTa' => $request->moTa,
                'ngayTao' => now(),
                'ngayBatDau' => $request->ngayBatDau,
                'ngayKetThuc' => $request->ngayKetThuc,
                'daXacNhanCuuTro' => (int) ($request->daXacNhanCuuTro ?? 0),
                'ghiChuXacNhan' => $request->ghiChuXacNhan,
                'trangThai' => $request->trangThaiChienDich,
            ]);

            $idChienDichMoi = $chienDich->idChienDich;

            foreach ($nguonLucDuocChon as $idHangHoa => $duLieu) {
                NguonLucChienDich::create([
                    'idChienDich' => $chienDich->idChienDich,
                    'idHangHoa' => (int) $idHangHoa,
                    'soLuongCanKeuGoi' => (float) $duLieu['soLuongCanKeuGoi'],
                    'soLuongDaNhan' => 0,
                    'soLuongHienCo' => 0,
                    'hanSuDung' => null,
                    'trangThai' => 'Đang kêu gọi',
                    'ngayCapNhat' => now(),
                ]);
            }

            $this->taoLuotTiepNhan(
                $yeuCau,
                $chienDich,
                $idNhom,
                $request->thoiGianDuKienHoTro,
                $request->noiDungDamNhan
            );
        });

        return redirect(
            '/nhom/' . $idNhom . '/chien-dich/' . $idChienDichMoi
        )->with(
            'success',
            'Tạo chiến dịch từ yêu cầu cứu trợ thành công.'
        );
    }

    public function canThemHoTro(
        Request $request,
        int $idNhom,
        int $idYeuCau,
        int $idTiepNhan
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $request->validate([
            'noiDungDamNhan' => 'required|string',
        ], [
            'noiDungDamNhan.required' =>
                'Vui lòng ghi rõ phần đã hỗ trợ và phần còn thiếu.',
        ]);

        $tiepNhan = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        if ($tiepNhan->trangThai === 'Hoàn thành') {
            return back()->with(
                'error',
                'Lượt tiếp nhận đã hoàn thành nên không thể yêu cầu hỗ trợ thêm.'
            );
        }

        if ($tiepNhan->trangThai === 'Cần thêm hỗ trợ') {
            return back()->with(
                'error',
                'Lượt tiếp nhận này đã ở trạng thái cần thêm hỗ trợ.'
            );
        }

        $tiepNhan->update([
            'noiDungDamNhan' => $this->noiThemNoiDungDamNhan(
                $tiepNhan->noiDungDamNhan,
                'Cần thêm hỗ trợ: ' . trim($request->noiDungDamNhan)
            ),
            'trangThai' => 'Cần thêm hỗ trợ',
        ]);

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        return back()->with(
            'success',
            'Đã thông báo yêu cầu cần thêm hỗ trợ.'
        );
    }

    public function thuHoiCanThemHoTro(
        int $idNhom,
        int $idYeuCau,
        int $idTiepNhan
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $tiepNhan = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        if ($tiepNhan->trangThai !== 'Cần thêm hỗ trợ') {
            return back()->with(
                'error',
                'Lượt tiếp nhận này không ở trạng thái cần thêm hỗ trợ.'
            );
        }

        $tiepNhan->update([
            'trangThai' => 'Đã tiếp nhận',
        ]);

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        return back()->with(
            'success',
            'Đã thu hồi trạng thái cần thêm hỗ trợ.'
        );
    }

    public function hoTroNhomDangThieu(
        Request $request,
        int $idNhom,
        int $idYeuCau,
        int $idTiepNhan
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $request->validate([
            'loaiHoTro' => 'required|in:ho_tro_mot_phan,ho_tro_day_du,khong_the_ho_tro',
            'noiDungHoTro' => 'required_unless:loaiHoTro,khong_the_ho_tro|nullable|string',
        ], [
            'loaiHoTro.required' => 'Vui lòng chọn hình thức hỗ trợ.',
            'loaiHoTro.in' => 'Hình thức hỗ trợ không hợp lệ.',
            'noiDungHoTro.required_unless' => 'Vui lòng nhập nội dung hỗ trợ.',
        ]);

        DB::transaction(function () use (
            $request,
            $idNhom,
            $idYeuCau,
            $idTiepNhan
        ) {
            $tiepNhanCuaNhom = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
                ->where('idYeuCau', $idYeuCau)
                ->where('idNhom', $idNhom)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($tiepNhanCuaNhom->trangThai, ['Đã tiếp nhận', 'Cần thêm hỗ trợ'], true)) {
                abort(422, 'Chỉ lượt tiếp nhận chưa hoàn thành mới có thể hỗ trợ nhóm khác.');
            }

            $cacLuotDangCanThem = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)
                ->where('idNhom', '!=', $idNhom)
                ->where('trangThai', 'Cần thêm hỗ trợ')
                ->lockForUpdate()
                ->get();

            if ($cacLuotDangCanThem->isEmpty()) {
                abort(422, 'Không có nhóm khác đang cần thêm hỗ trợ.');
            }

            $loaiHoTro = $request->loaiHoTro;

            if ($loaiHoTro === 'khong_the_ho_tro') {
                $tiepNhanCuaNhom->update([
                    'trangThai' => 'Cần thêm hỗ trợ',
                ]);

                return;
            }

            if ($loaiHoTro === 'ho_tro_mot_phan') {
                $tiepNhanCuaNhom->update([
                    'noiDungDamNhan' => $this->noiThemNoiDungDamNhan(
                        $tiepNhanCuaNhom->noiDungDamNhan,
                        'Hỗ trợ 1 phần: ' . trim((string) $request->noiDungHoTro)
                    ),
                    'trangThai' => 'Cần thêm hỗ trợ',
                ]);

                return;
            }

            if ($loaiHoTro === 'ho_tro_day_du') {
                $tiepNhanCuaNhom->update([
                    'noiDungDamNhan' => $this->noiThemNoiDungDamNhan(
                        $tiepNhanCuaNhom->noiDungDamNhan,
                        'Hỗ trợ: ' . trim((string) $request->noiDungHoTro)
                    ),
                    'trangThai' => 'Đã tiếp nhận',
                ]);

                TiepNhanYeuCau::whereIn(
                        'idTiepNhan',
                        $cacLuotDangCanThem->pluck('idTiepNhan')
                    )
                    ->update([
                        'trangThai' => 'Đã tiếp nhận',
                    ]);
            }
        });

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        $thongBao = match ($request->loaiHoTro) {
            'ho_tro_mot_phan' => 'Đã ghi nhận nhóm hỗ trợ một phần. Yêu cầu sẽ công khai nếu tất cả nhóm đang xử lý đều cần thêm hỗ trợ.',
            'ho_tro_day_du' => 'Đã ghi nhận nhóm hỗ trợ đầy đủ và cập nhật các lượt đang thiếu.',
            'khong_the_ho_tro' => 'Đã ghi nhận nhóm không thể hỗ trợ thêm.',
            default => 'Đã cập nhật hỗ trợ.',
        };

        return back()->with('success', $thongBao);
    }

    public function hoanThanhTiepNhan(
        int $idNhom,
        int $idYeuCau,
        int $idTiepNhan
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $tiepNhan = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        if ($tiepNhan->trangThai === 'Hoàn thành') {
            return back()->with(
                'error',
                'Lượt tiếp nhận này đã hoàn thành trước đó.'
            );
        }

        $tiepNhan->update([
            'trangThai' => 'Hoàn thành',
        ]);

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        return back()->with(
            'success',
            'Đã hoàn thành phần hỗ trợ của nhóm.'
        );
    }

    private function yeuCauCoTheTiepNhan(
        YeuCauCuuTro $yeuCau
    ): bool {
        return in_array($yeuCau->trangThai, [
            'Chờ tiếp nhận',
            'Cần thêm hỗ trợ',
        ], true);
    }

    private function nhomDangXuLyYeuCau(
        int $idNhom,
        int $idYeuCau
    ): bool {
        return TiepNhanYeuCau::where('idNhom', $idNhom)
            ->where('idYeuCau', $idYeuCau)
            ->where('trangThai', '!=', 'Hoàn thành')
            ->exists();
    }

    private function taoLuotTiepNhan(
        YeuCauCuuTro $yeuCau,
        ChienDichCuuTro $chienDich,
        int $idNhom,
        ?string $thoiGianDuKienHoTro,
        ?string $noiDungDamNhan
    ): TiepNhanYeuCau {
        return DB::transaction(function () use (
            $yeuCau,
            $chienDich,
            $idNhom,
            $thoiGianDuKienHoTro,
            $noiDungDamNhan
        ) {
            $yeuCauKhoa = YeuCauCuuTro::where(
                    'idYeuCau',
                    $yeuCau->idYeuCau
                )
                ->lockForUpdate()
                ->firstOrFail();

            if (!$this->yeuCauCoTheTiepNhan($yeuCauKhoa)) {
                abort(422, 'Yêu cầu không còn được phép tiếp nhận.');
            }

            $dangXuLy = TiepNhanYeuCau::where(
                    'idYeuCau',
                    $yeuCauKhoa->idYeuCau
                )
                ->where('idNhom', $idNhom)
                ->where('trangThai', '!=', 'Hoàn thành')
                ->exists();

            if ($dangXuLy) {
                abort(422, 'Nhóm đang tiếp nhận yêu cầu này.');
            }

            TiepNhanYeuCau::where('idYeuCau', $yeuCauKhoa->idYeuCau)
                ->where('trangThai', 'Cần thêm hỗ trợ')
                ->where('idNhom', '!=', $idNhom)
                ->update([
                    'trangThai' => 'Đã tiếp nhận',
                ]);

            $tiepNhanMoi = TiepNhanYeuCau::create([
                'idYeuCau' => $yeuCauKhoa->idYeuCau,
                'idChienDich' => $chienDich->idChienDich,
                'idNhom' => $idNhom,
                'thoiGianTiepNhan' => now(),
                'thoiGianDuKienHoTro' => $thoiGianDuKienHoTro,
                'noiDungDamNhan' => $this->noiThemNoiDungDamNhan(
                    null,
                    $noiDungDamNhan
                ),
                'trangThai' => 'Đã tiếp nhận',
            ]);

            $yeuCauKhoa->update([
                'trangThai' => 'Đã tiếp nhận',
            ]);

            return $tiepNhanMoi;
        });
    }

    private function capNhatTrangThaiTongYeuCau(
        int $idYeuCau
    ): void {
        $yeuCau = YeuCauCuuTro::findOrFail($idYeuCau);

        if ($yeuCau->trangThai === 'Đã hủy') {
            return;
        }

        $tiepNhans = TiepNhanYeuCau::where(
            'idYeuCau',
            $idYeuCau
        )->get();

        if ($tiepNhans->isEmpty()) {
            $yeuCau->update([
                'trangThai' => 'Chờ tiếp nhận',
            ]);

            return;
        }

        $tatCaHoanThanh = $tiepNhans->every(function ($tiepNhan) {
            return $tiepNhan->trangThai === 'Hoàn thành';
        });

        if ($tatCaHoanThanh) {
            $yeuCau->update([
                'trangThai' => 'Hoàn thành',
            ]);

            return;
        }

        $tiepNhansChuaHoanThanh = $tiepNhans->filter(function ($tiepNhan) {
            return $tiepNhan->trangThai !== 'Hoàn thành';
        });

        $tatCaChuaHoanThanhDeuCanThem =
            $tiepNhansChuaHoanThanh->isNotEmpty()
            && $tiepNhansChuaHoanThanh->every(function ($tiepNhan) {
                return $tiepNhan->trangThai === 'Cần thêm hỗ trợ';
            });

        $yeuCau->update([
            'trangThai' => $tatCaChuaHoanThanhDeuCanThem
                ? 'Cần thêm hỗ trợ'
                : 'Đã tiếp nhận',
        ]);
    }

    private function noiThemNoiDungDamNhan(
        ?string $noiDungCu,
        ?string $noiDungMoi
    ): string {
        $chuanHoaDanhSach = function (?string $noiDung): array {
            $noiDung = trim((string) $noiDung);

            if ($noiDung === '') {
                return [];
            }

            $noiDung = str_replace(["\r\n", "\r"], "\n", $noiDung);
            $cacDong = explode("\n", $noiDung);

            $ketQua = [];

            foreach ($cacDong as $dong) {
                $dong = trim($dong);

                if ($dong === '') {
                    continue;
                }

                $dong = preg_replace('/^\s*-\s*/u', '', $dong);
                $dong = trim((string) $dong);

                if ($dong !== '') {
                    $ketQua[] = '- ' . $dong;
                }
            }

            return $ketQua;
        };

        $cacDongCu = $chuanHoaDanhSach($noiDungCu);
        $cacDongMoi = $chuanHoaDanhSach($noiDungMoi);

        return implode(PHP_EOL, array_merge($cacDongCu, $cacDongMoi));
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ',
            'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ',
            'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($coDau, $khongDau, $chuoi);
    }
}