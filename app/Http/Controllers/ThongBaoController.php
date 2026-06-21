<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;

class ThongBaoController extends Controller
{
    public function index()
    {
        $idNguoiDung = session('idNguoiDung');
        $vaiTro = session('vaiTro', 'Người dùng');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $thongBaos = ThongBao::where('trangThai', 'Hiển thị')
            ->where(function ($query) use ($idNguoiDung, $vaiTro) {
                $query->where('doiTuong', 'Tất cả')
                    ->orWhere('doiTuong', $vaiTro)
                    ->orWhere('idNguoiNhan', $idNguoiDung);
            })
            ->orderBy('idThongBao', 'desc')
            ->get();

        $idMoThongBao = request('mo');

        return view('thong_bao.index', compact('thongBaos', 'idMoThongBao'));
    }
}