<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\TiepNhanYeuCau;

class NhomDashboardController extends Controller
{
    public function index(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhom = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->findOrFail($idNhom);

        if ($nhom->trangThai != 'Đang hoạt động') {
            return redirect('/user/nhom-cua-toi')
                ->with('error', 'Nhóm này chưa được phép hoạt động hoặc đã bị khóa.');
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->first();

        if (!$thanhVien) {
            return redirect('/user/nhom-cua-toi')
                ->with('error', 'Bạn không thuộc nhóm tình nguyện này.');
        }

        $vaiTroTrongNhom = $thanhVien->vaiTro ?? 'Thành viên';

        $thongKe = [
            'soThanhVien' => ThanhVienNhom::where('idNhom', $idNhom)->count(),
            'soChienDich' => ChienDichCuuTro::where('idNhom', $idNhom)->count(),
            'soYeuCauTiepNhan' => TiepNhanYeuCau::where('idNhom', $idNhom)->count(),
        ];

        return view('nhom.dashboard', compact(
            'nhom',
            'thanhVien',
            'vaiTroTrongNhom',
            'thongKe'
        ));
    }
}