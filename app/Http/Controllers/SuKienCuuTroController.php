<?php

namespace App\Http\Controllers;

use App\Models\SuKienCuuTro;
use Illuminate\Http\Request;

class SuKienCuuTroController extends Controller
{
    public function index()
    {
        $suKiens = SuKienCuuTro::orderBy('idSuKien', 'desc')->get();

        return view('admin.su_kien_cuu_tro.index', compact('suKiens'));
    }

    public function create()
    {
        return view('admin.su_kien_cuu_tro.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenSuKien' => 'required|string|max:255',
            'loaiSuKien' => 'required|string|max:50',
            'moTa' => 'nullable|string',
            'trangThai' => 'required|string|max:50',
        ], [
            'tenSuKien.required' => 'Vui lòng nhập tên sự kiện cứu trợ.',
            'loaiSuKien.required' => 'Vui lòng chọn loại sự kiện.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        SuKienCuuTro::create([
            'tenSuKien' => trim($request->tenSuKien),
            'loaiSuKien' => $request->loaiSuKien,
            'moTa' => $request->moTa,
            'trangThai' => $request->trangThai,
            'ngayTao' => now(),
        ]);

        return redirect('/admin/su-kien-cuu-tro')
            ->with('success', 'Thêm sự kiện cứu trợ thành công.');
    }

    public function edit(int $id)
    {
        $suKien = SuKienCuuTro::findOrFail($id);

        return view('admin.su_kien_cuu_tro.edit', compact('suKien'));
    }

    public function update(Request $request, int $id)
    {
        $suKien = SuKienCuuTro::findOrFail($id);

        $request->validate([
            'tenSuKien' => 'required|string|max:255',
            'loaiSuKien' => 'required|string|max:50',
            'moTa' => 'nullable|string',
            'trangThai' => 'required|string|max:50',
        ], [
            'tenSuKien.required' => 'Vui lòng nhập tên sự kiện cứu trợ.',
            'loaiSuKien.required' => 'Vui lòng chọn loại sự kiện.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $suKien->update([
            'tenSuKien' => trim($request->tenSuKien),
            'loaiSuKien' => $request->loaiSuKien,
            'moTa' => $request->moTa,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/su-kien-cuu-tro')
            ->with('success', 'Cập nhật sự kiện cứu trợ thành công.');
    }

    public function destroy(int $id)
    {
        $suKien = SuKienCuuTro::findOrFail($id);

        $dangDuocSuDung = \App\Models\ChienDichCuuTro::where('idSuKien', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/su-kien-cuu-tro')
                ->with('error', 'Không thể xóa sự kiện này vì đang được sử dụng trong chiến dịch cứu trợ.');
        }

        $suKien->delete();

        return redirect('/admin/su-kien-cuu-tro')
            ->with('success', 'Xóa sự kiện cứu trợ thành công.');
    }
}