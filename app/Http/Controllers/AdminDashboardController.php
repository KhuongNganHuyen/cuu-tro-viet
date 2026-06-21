<?php

namespace App\Http\Controllers;

use App\Models\ChienDichCuuTro;
use App\Models\DongGop;
use App\Models\NguoiDung;
use App\Models\NhomTinhNguyen;
use App\Models\ThongBao;
use App\Models\YeuCauCuuTro;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $idNguoiDung = session('idNguoiDung');
        $vaiTroDangNhap = session('vaiTro', 'Admin');

        $soNguoiDung = NguoiDung::count();
        $soNhomTinhNguyen = NhomTinhNguyen::count();
        $soNhomChoDuyet = NhomTinhNguyen::where('trangThai', 'Chờ duyệt')->count();
        $soChienDich = ChienDichCuuTro::count();
        $soChienDichDangHoatDong = ChienDichCuuTro::whereIn('trangThai', ['Đang hoạt động', 'Đang diễn ra'])->count();
        $soYeuCau = YeuCauCuuTro::count();
        $soYeuCauChoTiepNhan = YeuCauCuuTro::where('trangThai', 'Chờ tiếp nhận')->count();
        $soDongGop = DongGop::count();

        $trangThaiYeuCau = YeuCauCuuTro::query()
            ->select('trangThai')
            ->selectRaw('COUNT(*) as tong')
            ->groupBy('trangThai')
            ->pluck('tong', 'trangThai');

        $trangThaiChienDich = ChienDichCuuTro::query()
            ->select('trangThai')
            ->selectRaw('COUNT(*) as tong')
            ->groupBy('trangThai')
            ->pluck('tong', 'trangThai');

        $duLieuDashboard = [
            'tongQuanHeThong' => [
                'labels' => ['Người dùng', 'Nhóm', 'Chiến dịch', 'Yêu cầu', 'Đóng góp'],
                'data' => [
                    $soNguoiDung,
                    $soNhomTinhNguyen,
                    $soChienDich,
                    $soYeuCau,
                    $soDongGop,
                ],
            ],
            'trangThaiYeuCau' => [
                'labels' => $trangThaiYeuCau->keys()->values()->toArray(),
                'data' => $trangThaiYeuCau->values()->map(function ($value) {
                    return (int) $value;
                })->toArray(),
            ],
            'trangThaiChienDich' => [
                'labels' => $trangThaiChienDich->keys()->values()->toArray(),
                'data' => $trangThaiChienDich->values()->map(function ($value) {
                    return (int) $value;
                })->toArray(),
            ],
        ];

        $thongKe = [
            'soNguoiDung' => $soNguoiDung,
            'soNhomTinhNguyen' => $soNhomTinhNguyen,
            'soNhomChoDuyet' => $soNhomChoDuyet,
            'soChienDich' => $soChienDich,
            'soChienDichDangHoatDong' => $soChienDichDangHoatDong,
            'soYeuCau' => $soYeuCau,
            'soYeuCauChoTiepNhan' => $soYeuCauChoTiepNhan,
            'soDongGop' => $soDongGop,
        ];

        $thongBaoDashboard = ThongBao::where('trangThai', 'Hiển thị')
            ->where(function ($query) use ($idNguoiDung, $vaiTroDangNhap) {
                $query->where('doiTuong', 'Tất cả')
                    ->orWhere('doiTuong', 'Admin')
                    ->orWhere('doiTuong', $vaiTroDangNhap)
                    ->orWhere('idNguoiNhan', $idNguoiDung);
            })
            ->orderBy('idThongBao', 'desc')
            ->take(4)
            ->get();

        return view('admin.dashboard', compact(
            'thongKe',
            'duLieuDashboard',
            'thongBaoDashboard'
        ));
    }
}