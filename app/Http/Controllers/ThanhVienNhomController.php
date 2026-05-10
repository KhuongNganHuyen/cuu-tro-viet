<?php

namespace App\Http\Controllers;

use App\Models\NhomTinhNguyen;
use App\Models\NguoiDung;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;

class ThanhVienNhomController extends Controller
{
    public function index(int $idNhom)
    {
        $nhom = NhomTinhNguyen::with(['thanhViens.nguoiDung'])->findOrFail($idNhom);

        return view('admin.thanh_vien_nhom.index', compact('nhom'));
    }

    public function create(int $idNhom)
    {
        $nhom = NhomTinhNguyen::findOrFail($idNhom);

        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orderBy('hoTen')
            ->get();

        return view('admin.thanh_vien_nhom.create', compact('nhom', 'nguoiDungs'));
    }

    public function store(Request $request, int $idNhom)
    {
        $nhom = NhomTinhNguyen::findOrFail($idNhom);

        $request->validate([
            'idNguoiDung' => 'required|exists:NguoiDung,idNguoiDung',
            'vaiTro' => 'required|string|max:255',
        ], [
            'idNguoiDung.required' => 'Vui lòng chọn người dùng.',
            'idNguoiDung.exists' => 'Người dùng không hợp lệ.',
            'vaiTro.required' => 'Vui lòng chọn vai trò trong nhóm.',
        ]);

        $daTonTai = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $request->idNguoiDung)
            ->exists();

        if ($daTonTai) {
            return redirect()
                ->back()
                ->withErrors(['idNguoiDung' => 'Người dùng này đã là thành viên của nhóm.'])
                ->withInput();
        }

        ThanhVienNhom::create([
            'idNhom' => $nhom->idNhom,
            'idNguoiDung' => $request->idNguoiDung,
            'vaiTro' => $request->vaiTro,
            'ngayThamGia' => now(),
        ]);

        return redirect('/admin/nhom-tinh-nguyen/' . $idNhom . '/thanh-vien')
            ->with('success', 'Thêm thành viên nhóm thành công.');
    }

    public function destroy(int $idThanhVien)
    {
        $thanhVien = ThanhVienNhom::findOrFail($idThanhVien);
        $idNhom = $thanhVien->idNhom;

        $thanhVien->delete();

        return redirect('/admin/nhom-tinh-nguyen/' . $idNhom . '/thanh-vien')
            ->with('success', 'Xóa thành viên nhóm thành công.');
    }
}