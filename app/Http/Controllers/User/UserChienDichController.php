<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChienDichCuuTro;
use App\Models\CapNhatChienDich;
use App\Models\DongGop;
use App\Models\NguonLucChienDich;
use App\Models\TiepNhanYeuCau;
use App\Models\DotPhanPhoi;
use Illuminate\Http\Request;

class UserChienDichController extends Controller
{
    public function index(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));
        $nhomDangChon = trim((string) $request->input('idNhom'));
        $suKienDangChon = trim((string) $request->input('idSuKien'));
        $xacNhanDangChon = trim((string) $request->input('xacNhan'));
        $trangThaiDangChon = trim((string) $request->input('trangThai'));

        $chienDichs = ChienDichCuuTro::with(['suKien', 'diaDiem', 'nhom.nhomTruong'])
            ->whereHas('nhom', function ($query) {
                $query->whereNotIn('trangThai', ['Chờ duyệt', 'Từ chối', 'Bị khóa']);
            })
            ->orderBy('idChienDich', 'desc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaThuong = mb_strtolower($tuKhoa, 'UTF-8');
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $chienDichs = $chienDichs
                ->filter(function ($chienDich) use ($tuKhoaThuong, $tuKhoaKhongDau) {
                    $diaDiem = $chienDich->diaDiem;

                    $diaChi = implode(' ', [
                        $diaDiem->chiTietDiaDiem ?? '',
                        $diaDiem->phuongXa ?? '',
                        $diaDiem->tinhThanh ?? '',
                    ]);

                    $xacNhan = $chienDich->daXacNhanCuuTro
                        ? 'Đã xác nhận'
                        : 'Chưa xác nhận';

                    $noiDungTimKiem = implode(' ', [
                        $chienDich->idChienDich,
                        $chienDich->tenChienDich,
                        $chienDich->moTa,
                        $chienDich->ngayTao,
                        $chienDich->ngayBatDau,
                        $chienDich->ngayKetThuc,
                        $chienDich->ghiChuXacNhan,
                        $chienDich->trangThai,
                        $xacNhan,
                        $chienDich->nhom->tenNhom ?? '',
                        $chienDich->nhom->nhomTruong->hoTen ?? '',
                        $chienDich->suKien->tenSuKien ?? '',
                        $chienDich->suKien->loaiSuKien ?? '',
                        $diaChi,
                    ]);

                    $noiDungThuong = mb_strtolower($noiDungTimKiem, 'UTF-8');
                    $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                    return str_contains($noiDungThuong, $tuKhoaThuong)
                        || str_contains($noiDungKhongDau, $tuKhoaKhongDau);
                })
                ->values();
        }
            
        if ($nhomDangChon !== '') {
            $chienDichs = $chienDichs->filter(function ($chienDich) use ($nhomDangChon) {
                return (string) $chienDich->idNhom === $nhomDangChon;
            })->values();
        }

        if ($suKienDangChon !== '') {
            $chienDichs = $chienDichs->filter(function ($chienDich) use ($suKienDangChon) {
                return (string) $chienDich->idSuKien === $suKienDangChon;
            })->values();
        }

        if ($xacNhanDangChon !== '') {
            $chienDichs = $chienDichs->filter(function ($chienDich) use ($xacNhanDangChon) {
                return (string) (int) $chienDich->daXacNhanCuuTro === $xacNhanDangChon;
            })->values();
        }

        if ($trangThaiDangChon !== '') {
            $chienDichs = $chienDichs->filter(function ($chienDich) use ($trangThaiDangChon) {
                return $chienDich->trangThai === $trangThaiDangChon;
            })->values();
        }

        $danhSachNhom = ChienDichCuuTro::with('nhom')
            ->whereHas('nhom', function ($query) {
                $query->whereNotIn('trangThai', ['Chờ duyệt', 'Từ chối', 'Bị khóa']);
            })
            ->get()
            ->pluck('nhom')
            ->filter()
            ->unique('idNhom')
            ->sortBy('tenNhom')
            ->values();

        $danhSachSuKien = ChienDichCuuTro::with('suKien')
            ->whereHas('nhom', function ($query) {
                $query->whereNotIn('trangThai', ['Chờ duyệt', 'Từ chối', 'Bị khóa']);
            })
            ->get()
            ->pluck('suKien')
            ->filter()
            ->unique('idSuKien')
            ->sortBy('tenSuKien')
            ->values();
            
        return view('user.chien_dich.index', compact(
            'chienDichs',
            'danhSachNhom',
            'danhSachSuKien',
            'nhomDangChon',
            'suKienDangChon',
            'xacNhanDangChon',
            'trangThaiDangChon'
        ));
    }

    public function show(int $idChienDich)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $chienDich = ChienDichCuuTro::with(['suKien', 'diaDiem', 'nhom.nhomTruong'])
            ->whereHas('nhom', function ($query) {
                $query->whereNotIn('trangThai', ['Chờ duyệt', 'Từ chối', 'Bị khóa']);
            })
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $nhom = $chienDich->nhom;
        $laNhomTruong = false;

        $capNhats = CapNhatChienDich::with('thanhVien.nguoiDung')
            ->where('idChienDich', $idChienDich)
            ->orderBy('idCapNhat', 'desc')
            ->get();

        $dongGops = DongGop::with([
                'nguoiUngHo',
                'thanhVienTiepNhan.nguoiDung',
                'chiTietDongGops.hangHoa.danhMucHang',
            ])
            ->where('idChienDich', $idChienDich)
            ->whereHas('chiTietDongGops', function ($query) {
                $query->where('trangThai', 'Đã xác nhận');
            })
            ->orderBy('idDongGop', 'desc')
            ->get()
            ->map(function ($dongGop) {
                $chiTietDaXacNhan = $dongGop->chiTietDongGops
                    ->filter(function ($chiTiet) {
                        return $chiTiet->trangThai === 'Đã xác nhận';
                    })
                    ->values();

                $dongGop->setRelation('chiTietDongGops', $chiTietDaXacNhan);

                return $dongGop;
            })
            ->filter(function ($dongGop) {
                return $dongGop->chiTietDongGops->isNotEmpty();
            })
            ->values();

        $nguonLucs = NguonLucChienDich::with('hangHoa.danhMucHang')
            ->where('idChienDich', $idChienDich)
            ->orderBy('idNguonLuc', 'desc')
            ->get();

        $tiepNhanYeuCaus = TiepNhanYeuCau::with([
                'yeuCau.nguoiGui',
                'yeuCau.diaDiem',
                'nhom',
            ])
            ->where('idChienDich', $idChienDich)
            ->orderByRaw("CASE WHEN trangThai = 'Hoàn thành' THEN 1 ELSE 0 END")
            ->orderBy('idYeuCau', 'desc')
            ->get();

        $dotPhanPhois = DotPhanPhoi::with([
                'chiTietPhanPhois.nguonLuc.hangHoa',
                'chiTietPhanPhois.diaDiem',
                'chiTietPhanPhois.tiepNhan.yeuCau.nguoiGui',
                'chiTietPhanPhois.tiepNhan.yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->orderBy('idDotPhanPhoi', 'desc')
            ->get();

        return view('user.chien_dich.show', compact(
            'nhom',
            'chienDich',
            'laNhomTruong',
            'capNhats',
            'dongGops',
            'nguonLucs',
            'tiepNhanYeuCaus',
            'dotPhanPhois'
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