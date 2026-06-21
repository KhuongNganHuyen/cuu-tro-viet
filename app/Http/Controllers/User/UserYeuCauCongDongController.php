<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\YeuCauCuuTro;
use Illuminate\Http\Request;

class UserYeuCauCongDongController extends Controller
{
    public function index(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));
        $mucDoDangChon = trim((string) $request->input('mucDoKhanCap'));
        $trangThaiDangChon = trim((string) $request->input('trangThai'));
        $tinhThanhDangChon = trim((string) $request->input('tinhThanh'));

        $query = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
            ->orderBy('idYeuCau', 'desc');

        if ($tuKhoa !== '') {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('tieuDeYeuCau', 'like', '%' . $tuKhoa . '%')
                    ->orWhere('moTa', 'like', '%' . $tuKhoa . '%')
                    ->orWhereHas('nguoiGui', function ($subQuery) use ($tuKhoa) {
                        $subQuery->where('hoTen', 'like', '%' . $tuKhoa . '%')
                            ->orWhere('tenDangNhap', 'like', '%' . $tuKhoa . '%')
                            ->orWhere('email', 'like', '%' . $tuKhoa . '%')
                            ->orWhere('sdt', 'like', '%' . $tuKhoa . '%');
                    })
                    ->orWhereHas('diaDiem', function ($subQuery) use ($tuKhoa) {
                        $subQuery->where('chiTietDiaDiem', 'like', '%' . $tuKhoa . '%')
                            ->orWhere('phuongXa', 'like', '%' . $tuKhoa . '%')
                            ->orWhere('tinhThanh', 'like', '%' . $tuKhoa . '%');
                    });
            });
        }

        if ($mucDoDangChon !== '') {
            $query->where('mucDoKhanCap', $mucDoDangChon);
        }

        if ($trangThaiDangChon !== '') {
            $query->where('trangThai', $trangThaiDangChon);
        }

        if ($tinhThanhDangChon !== '') {
            $query->whereHas('diaDiem', function ($q) use ($tinhThanhDangChon) {
                $q->where('tinhThanh', $tinhThanhDangChon);
            });
        }

        $yeuCaus = $query->get();

        $yeuCausChuaTiepNhan = $yeuCaus
            ->filter(function ($yeuCau) {
                return $yeuCau->tiepNhans->isEmpty();
            })
            ->values();

        $yeuCausDaTiepNhan = $yeuCaus
            ->filter(function ($yeuCau) {
                return $yeuCau->tiepNhans->isNotEmpty();
            })
            ->values();

        $danhSachTinhThanh = YeuCauCuuTro::with('diaDiem')
            ->get()
            ->pluck('diaDiem.tinhThanh')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('user.yeu_cau_cong_dong.index', compact(
            'yeuCausChuaTiepNhan',
            'yeuCausDaTiepNhan',
            'danhSachTinhThanh',
            'tuKhoa',
            'mucDoDangChon',
            'trangThaiDangChon',
            'tinhThanhDangChon'
        ));
    }

    public function show(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
            ->findOrFail($idYeuCau);

        $tiepNhans = $yeuCau->tiepNhans
            ->sortByDesc('idTiepNhan')
            ->values();

        return view('user.yeu_cau_cong_dong.show', compact(
            'yeuCau',
            'tiepNhans'
        ));
    }
}