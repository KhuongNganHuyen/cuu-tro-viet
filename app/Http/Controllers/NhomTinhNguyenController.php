<?php

namespace App\Http\Controllers;

use App\Models\NhomTinhNguyen;
use App\Models\NguoiDung;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class NhomTinhNguyenController extends Controller
{
    public function index()
    {
        $nhomTinhNguyens = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->orderBy('idNhom', 'desc')
            ->get();

        return view('admin.nhom_tinh_nguyen.index', compact('nhomTinhNguyens'));
    }

    public function create()
    {
        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')->get();

        return view('admin.nhom_tinh_nguyen.create', compact('nguoiDungs', 'diaDiems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'idNhomTruong.required' => 'Vui lòng chọn nhóm trưởng.',
            'idNhomTruong.exists' => 'Nhóm trưởng không hợp lệ.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'idDiaDiem.exists' => 'Địa điểm không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        NhomTinhNguyen::create([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $request->idDiaDiem,
            'trangThai' => $request->trangThai,
            'ngayTao' => now(),
        ]);

        return redirect('/admin/nhom-tinh-nguyen')->with('success', 'Thêm nhóm tình nguyện thành công.');
    }

    public function edit(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')->get();

        return view('admin.nhom_tinh_nguyen.edit', compact('nhomTinhNguyen', 'nguoiDungs', 'diaDiems'));
    }

    public function update(Request $request, int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'idNhomTruong.required' => 'Vui lòng chọn nhóm trưởng.',
            'idNhomTruong.exists' => 'Nhóm trưởng không hợp lệ.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'idDiaDiem.exists' => 'Địa điểm không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $nhomTinhNguyen->update([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $request->idDiaDiem,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/nhom-tinh-nguyen')->with('success', 'Cập nhật nhóm tình nguyện thành công.');
    }

    public function destroy(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);
        $nhomTinhNguyen->delete();

        return redirect('/admin/nhom-tinh-nguyen')->with('success', 'Xóa nhóm tình nguyện thành công.');
    }
}