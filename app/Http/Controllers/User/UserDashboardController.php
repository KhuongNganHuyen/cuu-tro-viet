<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\YeuCauCuuTro;
use App\Models\DongGop;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\ThongBao;

class UserDashboardController extends Controller
{
    public function index()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $vaiTroDangNhap = session('vaiTro', 'Người dùng');

        $yeuCaus = YeuCauCuuTro::where('idNguoiGui', $idNguoiDung)->get();

        $dongGops = DongGop::with(['chiTietDongGops'])
            ->where('idNguoiUngHo', $idNguoiDung)
            ->get();

        $soYeuCau = $yeuCaus->count();
        $soDongGop = $dongGops->count();

        $soNhomThamGia = ThanhVienNhom::where('idNguoiDung', $idNguoiDung)->count();

        $soNhomChoDuyet = NhomTinhNguyen::where('idNhomTruong', $idNguoiDung)
            ->where('trangThai', 'Chờ duyệt')
            ->count();

        $soYeuCauHoanThanh = $yeuCaus
            ->filter(function ($yeuCau) {
                return ($yeuCau->trangThai ?? '') === 'Hoàn thành';
            })
            ->count();

        $soDongGopDaXacNhan = $dongGops
            ->filter(function ($dongGop) {
                return $dongGop->chiTietDongGops->contains('trangThai', 'Đã xác nhận');
            })
            ->count();

        $soChiTietDongGop = $dongGops
            ->flatMap(function ($dongGop) {
                return $dongGop->chiTietDongGops;
            })
            ->count();

        $thongKe = [
            'soYeuCau' => $soYeuCau,
            'soYeuCauHoanThanh' => $soYeuCauHoanThanh,
            'soDongGop' => $soDongGop,
            'soDongGopDaXacNhan' => $soDongGopDaXacNhan,
            'soChiTietDongGop' => $soChiTietDongGop,
            'soNhomThamGia' => $soNhomThamGia,
            'soNhomChoDuyet' => $soNhomChoDuyet,
        ];

        $thongKeTrangThaiYeuCau = $yeuCaus
            ->groupBy(function ($yeuCau) {
                return $yeuCau->trangThai ?: 'Chưa xác định';
            })
            ->map(function ($items) {
                return $items->count();
            });

        $thongKeTrangThaiDongGop = $dongGops
            ->flatMap(function ($dongGop) {
                return $dongGop->chiTietDongGops;
            })
            ->groupBy(function ($chiTiet) {
                return $chiTiet->trangThai ?: 'Chưa xác định';
            })
            ->map(function ($items) {
                return $items->count();
            });

        $duLieuDashboard = [
            'tongQuanCaNhan' => [
                'labels' => [
                    'Yêu cầu cứu trợ',
                    'Lượt đóng góp',
                    'Nhóm tham gia',
                    'Nhóm chờ duyệt',
                ],
                'data' => [
                    $soYeuCau,
                    $soDongGop,
                    $soNhomThamGia,
                    $soNhomChoDuyet,
                ],
            ],
            'trangThaiYeuCau' => [
                'labels' => $thongKeTrangThaiYeuCau->keys()->values()->toArray(),
                'data' => $thongKeTrangThaiYeuCau->values()->toArray(),
            ],
            'trangThaiDongGop' => [
                'labels' => $thongKeTrangThaiDongGop->keys()->values()->toArray(),
                'data' => $thongKeTrangThaiDongGop->values()->toArray(),
            ],
        ];

        $thongBaoDashboard = ThongBao::where('trangThai', 'Hiển thị')
            ->where(function ($query) use ($idNguoiDung, $vaiTroDangNhap) {
                $query->where('doiTuong', 'Tất cả')
                    ->orWhere('doiTuong', $vaiTroDangNhap)
                    ->orWhere('idNguoiNhan', $idNguoiDung);
            })
            ->orderBy('idThongBao', 'desc')
            ->take(3)
            ->get();

        return view('user.dashboard', compact(
            'thongKe',
            'duLieuDashboard',
            'thongBaoDashboard'
        ));
    }
}