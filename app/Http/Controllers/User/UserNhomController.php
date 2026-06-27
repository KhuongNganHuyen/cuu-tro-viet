<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\DiaDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserNhomController extends Controller
{
    public function index(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        /*
        * Các nhóm người dùng đang tham gia.
        */
        $nhomThamGias = ThanhVienNhom::with([
                'nhom.diaDiem',
                'nhom.nhomTruong',
            ])
            ->where('idNguoiDung', $idNguoiDung)
            ->where('vaiTro', '!=', 'Đã rời nhóm')
            ->get();

        /*
        * Các nhóm do người dùng đăng ký đang chờ duyệt.
        */
        $nhomChoDuyets = NhomTinhNguyen::with([
                'diaDiem',
                'nhomTruong',
            ])
            ->where('idNhomTruong', $idNguoiDung)
            ->where('trangThai', 'Chờ duyệt')
            ->get();

        /*
        * Các nhóm do người dùng đăng ký nhưng bị từ chối.
        */
        $nhomTuChois = NhomTinhNguyen::with([
                'diaDiem',
                'nhomTruong',
            ])
            ->where('idNhomTruong', $idNguoiDung)
            ->where('trangThai', 'Từ chối')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaThuong = mb_strtolower($tuKhoa, 'UTF-8');
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $nhomThamGias = $nhomThamGias
                ->filter(function ($thanhVien) use (
                    $tuKhoaThuong,
                    $tuKhoaKhongDau
                ) {
                    $nhom = $thanhVien->nhom;

                    if (!$nhom) {
                        return false;
                    }

                    $diaDiem = $nhom->diaDiem;

                    $noiDung = implode(' ', [
                        $nhom->idNhom,
                        $nhom->tenNhom,
                        $nhom->moTa,
                        $nhom->trangThai,
                        $thanhVien->vaiTro,
                        $nhom->nhomTruong->hoTen ?? '',
                        $nhom->nhomTruong->tenDangNhap ?? '',
                        $diaDiem->chiTietDiaDiem ?? '',
                        $diaDiem->phuongXa ?? '',
                        $diaDiem->tinhThanh ?? '',
                    ]);

                    return str_contains(
                        mb_strtolower($noiDung, 'UTF-8'),
                        $tuKhoaThuong
                    ) || str_contains(
                        $this->boDauTiengViet($noiDung),
                        $tuKhoaKhongDau
                    );
                });

            $locDanhSachNhom = function ($nhom) use (
                $tuKhoaThuong,
                $tuKhoaKhongDau
            ) {
                $diaDiem = $nhom->diaDiem;

                $noiDung = implode(' ', [
                    $nhom->idNhom,
                    $nhom->tenNhom,
                    $nhom->moTa,
                    $nhom->trangThai,
                    $nhom->ngayTao,
                    $nhom->nhomTruong->hoTen ?? '',
                    $nhom->nhomTruong->tenDangNhap ?? '',
                    $diaDiem->chiTietDiaDiem ?? '',
                    $diaDiem->phuongXa ?? '',
                    $diaDiem->tinhThanh ?? '',
                ]);

                return str_contains(
                    mb_strtolower($noiDung, 'UTF-8'),
                    $tuKhoaThuong
                ) || str_contains(
                    $this->boDauTiengViet($noiDung),
                    $tuKhoaKhongDau
                );
            };

            $nhomChoDuyets = $nhomChoDuyets
                ->filter($locDanhSachNhom);

            $nhomTuChois = $nhomTuChois
                ->filter($locDanhSachNhom);
        }

        /*
        * Sắp xếp mã nhóm từ bé đến lớn.
        */
        $nhomThamGias = $nhomThamGias
            ->filter(fn ($thanhVien) => $thanhVien->nhom !== null)
            ->sortBy(fn ($thanhVien) => (int) $thanhVien->nhom->idNhom)
            ->values();

        $nhomChoDuyets = $nhomChoDuyets
            ->sortBy(fn ($nhom) => (int) $nhom->idNhom)
            ->values();

        $nhomTuChois = $nhomTuChois
            ->sortBy(fn ($nhom) => (int) $nhom->idNhom)
            ->values();

        return view('user.nhom_cua_toi.index', compact(
            'nhomThamGias',
            'nhomChoDuyets',
            'nhomTuChois'
        ));
    }

    public function show(int $id)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhom = NhomTinhNguyen::with(['diaDiem', 'nhomTruong'])
            ->where('idNhom', $id)
            ->where('idNhomTruong', $idNguoiDung)
            ->where('trangThai', 'Chờ duyệt')
            ->firstOrFail();

        return view('user.nhom_cua_toi.show', compact('nhom'));
    }

    public function create()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->get();

        $diaDiemJson = $diaDiems->map(function ($diaDiem) {
            return [
                'idDiaDiem' => $diaDiem->idDiaDiem,
                'tinhThanh' => $diaDiem->tinhThanh,
                'phuongXa' => $diaDiem->phuongXa,
                'chiTietDiaDiem' => $diaDiem->chiTietDiaDiem,
                'viDo' => $diaDiem->viDo,
                'kinhDo' => $diaDiem->kinhDo,
                'label' => trim(
                    ($diaDiem->chiTietDiaDiem ? $diaDiem->chiTietDiaDiem . ', ' : '') .
                    ($diaDiem->phuongXa ? $diaDiem->phuongXa . ', ' : '') .
                    $diaDiem->tinhThanh
                ),
            ];
        })->values()->toJson();

        return view('user.nhom_cua_toi.create', compact(
            'diaDiems',
            'diaDiemJson'
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
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'anhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm tình nguyện.',
            'tenNhom.max' => 'Tên nhóm không được vượt quá 255 ký tự.',
            'moTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'anhDaiDien.image' => 'Ảnh đại diện nhóm phải là file hình ảnh.',
            'anhDaiDien.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, png hoặc webp.',
            'anhDaiDien.max' => 'Ảnh đại diện không được vượt quá 2 MB.',
            'idDiaDiemCoSan.exists' => 'Địa điểm được chọn không tồn tại.',
            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
        ]);

        $tenNhom = trim($request->tenNhom);
        $moTa = $request->filled('moTa') ? trim($request->moTa) : null;
        $tinhThanh = trim($request->tinhThanh);
        $phuongXa = trim($request->phuongXa);
        $chiTietDiaDiem = trim($request->chiTietDiaDiem);

        $duongDanAnh = 'nhom-tinh-nguyen/group.jpg';

        if ($request->hasFile('anhDaiDien')) {
            $duongDanAnh = $request
                ->file('anhDaiDien')
                ->store('nhom-tinh-nguyen', 'public');
        }

        DB::transaction(function () use (
            $request,
            $idNguoiDung,
            $tenNhom,
            $moTa,
            $duongDanAnh,
            $tinhThanh,
            $phuongXa,
            $chiTietDiaDiem
        ) {
            if ($request->filled('idDiaDiemCoSan')) {
                $diaDiem = DiaDiem::findOrFail($request->idDiaDiemCoSan);
            } else {
                $diaDiem = DiaDiem::where('tinhThanh', $tinhThanh)
                    ->where('phuongXa', $phuongXa)
                    ->where('chiTietDiaDiem', $chiTietDiaDiem)
                    ->first();

                if (!$diaDiem) {
                    $diaDiem = DiaDiem::create([
                        'tinhThanh' => $tinhThanh,
                        'phuongXa' => $phuongXa,
                        'chiTietDiaDiem' => $chiTietDiaDiem,
                        'viDo' => $request->viDo,
                        'kinhDo' => $request->kinhDo,
                    ]);
                }
            }

            NhomTinhNguyen::create([
                'tenNhom' => $tenNhom,
                'moTa' => $moTa,
                'anhDaiDien' => $duongDanAnh,
                'idNhomTruong' => $idNguoiDung,
                'idDiaDiem' => $diaDiem->idDiaDiem,
                'trangThai' => 'Chờ duyệt',
                'ngayTao' => now(),
            ]);
        });

        return redirect('/user/nhom-cua-toi')
            ->with(
                'success',
                'Đăng ký tạo nhóm thành công. Vui lòng chờ quản trị viên duyệt.'
            );
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($coDau, $khongDau, $chuoi);
    }

}