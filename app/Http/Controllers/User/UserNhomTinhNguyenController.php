<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\TiepNhanYeuCau;
use Illuminate\Http\Request;

class UserNhomTinhNguyenController extends Controller
{
    public function index(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $nhomTinhNguyens = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->whereNotIn('trangThai', ['Chờ duyệt', 'Từ chối'])
            ->orderBy('idNhom', 'asc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $nhomTinhNguyens = $nhomTinhNguyens->filter(function ($nhom) use ($tuKhoa, $tuKhoaKhongDau) {
                $diaDiem = $nhom->diaDiem;

                $noiDungTimKiem = implode(' ', [
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

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem, 'UTF-8'), mb_strtolower($tuKhoa, 'UTF-8'))
                    || str_contains(mb_strtolower($noiDungKhongDau, 'UTF-8'), mb_strtolower($tuKhoaKhongDau, 'UTF-8'));
            })->values();
        }

        return view('user.nhom_tinh_nguyen.index', compact('nhomTinhNguyens'));
    }

    public function show(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhomTinhNguyen = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->whereNotIn('trangThai', ['Chờ duyệt', 'Từ chối'])
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        $thanhViens = ThanhVienNhom::with('nguoiDung')
            ->where('idNhom', $idNhom)
            ->orderByRaw("CASE WHEN vaiTro = 'Nhóm trưởng' THEN 0 ELSE 1 END")
            ->orderBy('idThanhVien', 'asc')
            ->get();

        $chienDichs = ChienDichCuuTro::with(['suKien', 'diaDiem'])
            ->where('idNhom', $idNhom)
            ->orderBy('idChienDich', 'asc')
            ->get();

        $yeuCauDaNhans = TiepNhanYeuCau::with(['yeuCau.diaDiem', 'yeuCau.nguoiGui'])
            ->where('idNhom', $idNhom)
            ->orderBy('idTiepNhan', 'asc')
            ->get();

        return view('user.nhom_tinh_nguyen.show', compact(
            'nhomTinhNguyen',
            'thanhViens',
            'chienDichs',
            'yeuCauDaNhans'
        ));
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