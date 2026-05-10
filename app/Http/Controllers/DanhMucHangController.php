<?php

namespace App\Http\Controllers;

use App\Models\DanhMucHang;
use Illuminate\Http\Request;

class DanhMucHangController extends Controller
{
    public function index()
    {
        $danhMucHangs = DanhMucHang::orderBy('idDanhMucHang', 'desc')->get();

        return view('admin.danh_muc_hang.index', compact('danhMucHangs'));
    }

    public function create()
    {
        return view('admin.danh_muc_hang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenDanhMucHang' => 'required|string|max:255',
        ], [
            'tenDanhMucHang.required' => 'Vui lòng nhập tên danh mục hàng.',
        ]);

        DanhMucHang::create([
            'tenDanhMucHang' => $request->tenDanhMucHang,
        ]);

        return redirect('/admin/danh-muc-hang')->with('success', 'Thêm danh mục hàng thành công.');
    }

    public function edit(int $id)
    {
        $danhMucHang = DanhMucHang::findOrFail($id);

        return view('admin.danh_muc_hang.edit', compact('danhMucHang'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tenDanhMucHang' => 'required|string|max:255',
        ], [
            'tenDanhMucHang.required' => 'Vui lòng nhập tên danh mục hàng.',
        ]);

        $danhMucHang = DanhMucHang::findOrFail($id);

        $danhMucHang->update([
            'tenDanhMucHang' => $request->tenDanhMucHang,
        ]);

        return redirect('/admin/danh-muc-hang')->with('success', 'Cập nhật danh mục hàng thành công.');
    }

    public function destroy(int $id)
    {
        $danhMucHang = DanhMucHang::findOrFail($id);
        $danhMucHang->delete();

        return redirect('/admin/danh-muc-hang')->with('success', 'Xóa danh mục hàng thành công.');
    }
}