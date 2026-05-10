<?php

namespace App\Http\Controllers;

use App\Models\DiaDiem;
use Illuminate\Http\Request;

class DiaDiemController extends Controller
{
    public function index()
    {
        $diaDiems = DiaDiem::orderBy('idDiaDiem', 'desc')->get();

        return view('admin.dia_diem.index', compact('diaDiems'));
    }

    public function create()
    {
        return view('admin.dia_diem.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'nullable|string|max:255',
            'chiTietDiaDiem' => 'nullable|string|max:255',
            'viDo' => 'nullable|numeric',
            'kinhDo' => 'nullable|numeric',
        ], [
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
        ]);

        DiaDiem::create($request->only([
            'tinhThanh',
            'phuongXa',
            'chiTietDiaDiem',
            'viDo',
            'kinhDo',
        ]));

        return redirect('/admin/dia-diem')->with('success', 'Thêm địa điểm thành công.');
    }

    public function edit($id)
    {
        $diaDiem = DiaDiem::findOrFail($id);

        return view('admin.dia_diem.edit', compact('diaDiem'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'nullable|string|max:255',
            'chiTietDiaDiem' => 'nullable|string|max:255',
            'viDo' => 'nullable|numeric',
            'kinhDo' => 'nullable|numeric',
        ], [
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
        ]);

        $diaDiem = DiaDiem::findOrFail($id);

        $diaDiem->update($request->only([
            'tinhThanh',
            'phuongXa',
            'chiTietDiaDiem',
            'viDo',
            'kinhDo',
        ]));

        return redirect('/admin/dia-diem')->with('success', 'Cập nhật địa điểm thành công.');
    }

    public function destroy($id)
    {
        $diaDiem = DiaDiem::findOrFail($id);
        $diaDiem->delete();

        return redirect('/admin/dia-diem')->with('success', 'Xóa địa điểm thành công.');
    }
}