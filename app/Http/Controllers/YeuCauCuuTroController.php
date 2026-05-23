<?php

namespace App\Http\Controllers;

use App\Models\YeuCauCuuTro;
use App\Models\NguoiDung;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class YeuCauCuuTroController extends Controller
{
    public function index()
    {
        $yeuCaus = YeuCauCuuTro::with(['nguoiGui', 'diaDiem'])
            ->orderBy('idYeuCau', 'desc')
            ->get();

        return view('admin.yeu_cau_cuu_tro.index', compact('yeuCaus'));
    }

    public function create()
    {
        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')->get();

        return view('admin.yeu_cau_cuu_tro.create', compact('nguoiDungs', 'diaDiems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idNguoiGui' => 'required|exists:NguoiDung,idNguoiDung',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'loaiYeuCau' => 'required|string|max:255',
            'moTa' => 'required|string',
            'soHoDan' => 'nullable|integer|min:1',
            'mucDoKhanCap' => 'nullable|string|max:255',
            'hinhAnh' => 'nullable|string|max:255',
            'trangThai' => 'required|string|max:255',
        ], [
            'idNguoiGui.required' => 'Vui lòng chọn người gửi.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'loaiYeuCau.required' => 'Vui lòng nhập loại yêu cầu.',
            'moTa.required' => 'Vui lòng nhập mô tả yêu cầu.',
            'soHoDan.integer' => 'Số hộ dân phải là số.',
            'soHoDan.min' => 'Số hộ dân phải lớn hơn 0.',
        ]);

        YeuCauCuuTro::create([
            'idNguoiGui' => $request->idNguoiGui,
            'idDiaDiem' => $request->idDiaDiem,
            'loaiYeuCau' => $request->loaiYeuCau,
            'moTa' => $request->moTa,
            'soHoDan' => $request->soHoDan,
            'mucDoKhanCap' => $request->mucDoKhanCap,
            'hinhAnh' => $request->hinhAnh,
            'trangThai' => $request->trangThai,
            'thoiGianGui' => now(),
        ]);

        return redirect('/admin/yeu-cau-cuu-tro')->with('success', 'Thêm yêu cầu cứu trợ thành công.');
    }

    public function edit(int $id)
    {
        $yeuCau = YeuCauCuuTro::findOrFail($id);

        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')->get();

        return view('admin.yeu_cau_cuu_tro.edit', compact('yeuCau', 'nguoiDungs', 'diaDiems'));
    }

    public function update(Request $request, int $id)
    {
        $yeuCau = YeuCauCuuTro::findOrFail($id);

        $request->validate([
            'idNguoiGui' => 'required|exists:NguoiDung,idNguoiDung',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'loaiYeuCau' => 'required|string|max:255',
            'moTa' => 'required|string',
            'soHoDan' => 'nullable|integer|min:1',
            'mucDoKhanCap' => 'nullable|string|max:255',
            'hinhAnh' => 'nullable|string|max:255',
            'trangThai' => 'required|string|max:255',
        ]);

        $yeuCau->update([
            'idNguoiGui' => $request->idNguoiGui,
            'idDiaDiem' => $request->idDiaDiem,
            'loaiYeuCau' => $request->loaiYeuCau,
            'moTa' => $request->moTa,
            'soHoDan' => $request->soHoDan,
            'mucDoKhanCap' => $request->mucDoKhanCap,
            'hinhAnh' => $request->hinhAnh,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/yeu-cau-cuu-tro')->with('success', 'Cập nhật yêu cầu cứu trợ thành công.');
    }

    public function destroy(int $id)
    {
        $yeuCau = YeuCauCuuTro::findOrFail($id);
        $yeuCau->delete();

        return redirect('/admin/yeu-cau-cuu-tro')->with('success', 'Xóa yêu cầu cứu trợ thành công.');
    }

    public function show(int $id)
    {
        $yeuCau = YeuCauCuuTro::with([
            'nguoiGui',
            'diaDiem',
            'tiepNhans.chienDich',
            'tiepNhans.nhom'
        ])->findOrFail($id);

        return view('admin.yeu_cau_cuu_tro.show', compact('yeuCau'));
    }
}