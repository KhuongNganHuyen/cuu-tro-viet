<?php

namespace App\Http\Controllers;

use App\Models\HangHoa;
use App\Models\DanhMucHang;
use Illuminate\Http\Request;

class HangHoaController extends Controller
{
    public function index(int $idDanhMucHang)
    {
        $danhMucHang = DanhMucHang::findOrFail($idDanhMucHang);

        $hangHoas = HangHoa::where('idDanhMucHang', $idDanhMucHang)
            ->orderBy('idHangHoa', 'desc')
            ->get();

        return view('admin.hang_hoa.index', compact('danhMucHang', 'hangHoas'));
    }

    public function create(int $idDanhMucHang)
    {
        $danhMucHang = DanhMucHang::findOrFail($idDanhMucHang);

        return view('admin.hang_hoa.create', compact('danhMucHang'));
    }

    public function store(Request $request, int $idDanhMucHang)
    {
        $danhMucHang = DanhMucHang::findOrFail($idDanhMucHang);

        $request->validate([
            'tenHangHoa' => 'required|string|max:255',
            'donViTinh' => 'required|string|max:100',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenHangHoa.required' => 'Vui lòng nhập tên hàng hóa.',
            'donViTinh.required' => 'Vui lòng nhập đơn vị tính.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $daTonTai = HangHoa::where('idDanhMucHang', $idDanhMucHang)
            ->where('tenHangHoa', trim($request->tenHangHoa))
            ->where('donViTinh', trim($request->donViTinh))
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with('error', 'Hàng hóa này đã tồn tại trong danh mục.');
        }

        HangHoa::create([
            'idDanhMucHang' => $danhMucHang->idDanhMucHang,
            'tenHangHoa' => trim($request->tenHangHoa),
            'donViTinh' => trim($request->donViTinh),
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/danh-muc-hang/' . $idDanhMucHang . '/hang-hoa')
            ->with('success', 'Thêm hàng hóa thành công.');
    }

    public function edit(int $idHangHoa)
    {
        $hangHoa = HangHoa::with('danhMucHang')->findOrFail($idHangHoa);

        return view('admin.hang_hoa.edit', compact('hangHoa'));
    }

    public function update(Request $request, int $idHangHoa)
    {
        $hangHoa = HangHoa::findOrFail($idHangHoa);

        $request->validate([
            'tenHangHoa' => 'required|string|max:255',
            'donViTinh' => 'required|string|max:100',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenHangHoa.required' => 'Vui lòng nhập tên hàng hóa.',
            'donViTinh.required' => 'Vui lòng nhập đơn vị tính.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $daTonTai = HangHoa::where('idDanhMucHang', $hangHoa->idDanhMucHang)
            ->where('tenHangHoa', trim($request->tenHangHoa))
            ->where('donViTinh', trim($request->donViTinh))
            ->where('idHangHoa', '!=', $hangHoa->idHangHoa)
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with('error', 'Hàng hóa này đã tồn tại trong danh mục.');
        }

        $hangHoa->update([
            'tenHangHoa' => trim($request->tenHangHoa),
            'donViTinh' => trim($request->donViTinh),
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/danh-muc-hang/' . $hangHoa->idDanhMucHang . '/hang-hoa')
            ->with('success', 'Cập nhật hàng hóa thành công.');
    }

    public function doiTrangThai(int $idHangHoa)
    {
        $hangHoa = HangHoa::findOrFail($idHangHoa);

        if ($hangHoa->trangThai == 'Đang sử dụng') {
            $hangHoa->update([
                'trangThai' => 'Ngừng sử dụng',
            ]);

            return redirect('/admin/danh-muc-hang/' . $hangHoa->idDanhMucHang . '/hang-hoa')
                ->with('success', 'Ngừng sử dụng hàng hóa thành công.');
        }

        $hangHoa->update([
            'trangThai' => 'Đang sử dụng',
        ]);

        return redirect('/admin/danh-muc-hang/' . $hangHoa->idDanhMucHang . '/hang-hoa')
            ->with('success', 'Mở sử dụng hàng hóa thành công.');
    }
}