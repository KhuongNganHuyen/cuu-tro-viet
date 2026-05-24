<?php

namespace App\Http\Controllers;

use App\Models\ThienTai;
use Illuminate\Http\Request;

class ThienTaiController extends Controller
{
    public function index()
    {
        $thienTais = ThienTai::orderBy('idThienTai', 'desc')->get();

        return view('admin.thien_tai.index', compact('thienTais'));
    }

    public function create()
    {
        return view('admin.thien_tai.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenThienTai' => 'required|string|max:255',
            'namXayRa' => 'nullable|integer|min:1900|max:2100',
        ], [
            'tenThienTai.required' => 'Vui lòng nhập tên thiên tai.',
            'namXayRa.integer' => 'Năm xảy ra phải là số.',
            'namXayRa.min' => 'Năm xảy ra không hợp lệ.',
            'namXayRa.max' => 'Năm xảy ra không hợp lệ.',
        ]);

        ThienTai::create([
            'tenThienTai' => $request->tenThienTai,
            'namXayRa' => $request->namXayRa,
        ]);

        return redirect('/admin/thien-tai')->with('success', 'Thêm thiên tai thành công.');
    }

    public function edit(int $id)
    {
        $thienTai = ThienTai::findOrFail($id);

        return view('admin.thien_tai.edit', compact('thienTai'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tenThienTai' => 'required|string|max:255',
            'namXayRa' => 'nullable|integer|min:1900|max:2100',
        ], [
            'tenThienTai.required' => 'Vui lòng nhập tên thiên tai.',
            'namXayRa.integer' => 'Năm xảy ra phải là số.',
            'namXayRa.min' => 'Năm xảy ra không hợp lệ.',
            'namXayRa.max' => 'Năm xảy ra không hợp lệ.',
        ]);

        $thienTai = ThienTai::findOrFail($id);

        $thienTai->update([
            'tenThienTai' => $request->tenThienTai,
            'namXayRa' => $request->namXayRa,
        ]);

        return redirect('/admin/thien-tai')->with('success', 'Cập nhật thiên tai thành công.');
    }

    public function destroy(int $id)
    {
        $thienTai = ThienTai::findOrFail($id);

        $dangDuocSuDung = \App\Models\ChienDichCuuTro::where('idThienTai', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/thien-tai')
                ->with('error', 'Không thể xóa thiên tai này vì đang được sử dụng trong chiến dịch cứu trợ.');
        }

        $thienTai->delete();

        return redirect('/admin/thien-tai')
            ->with('success', 'Xóa thiên tai thành công.');
    }
}