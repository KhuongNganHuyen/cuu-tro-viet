<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\YeuCauCuuTro;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class UserYeuCauCuuTroController extends Controller
{
    public function index()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $yeuCaus = YeuCauCuuTro::with(['diaDiem', 'tiepNhans.nhom', 'tiepNhans.chienDich'])
            ->where('idNguoiGui', $idNguoiDung)
            ->orderBy('idYeuCau', 'desc')
            ->get();

        return view('user.yeu_cau_cuu_tro.index', compact('yeuCaus'));
    }

    public function create()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->orderBy('chiTietDiaDiem')
            ->get();

        return view('user.yeu_cau_cuu_tro.create', compact('diaDiems'));
    }

    public function store(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $request->validate([
            'loaiYeuCau' => 'required|string|max:255',
            'moTa' => 'required|string',
            'soHoDan' => 'nullable|integer|min:1',
            'mucDoKhanCap' => 'required|string|max:255',

            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'nullable|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'nullable|numeric',
            'kinhDo' => 'nullable|numeric',

            'hinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'loaiYeuCau.required' => 'Vui lòng nhập loại yêu cầu.',
            'moTa.required' => 'Vui lòng nhập mô tả tình hình cần hỗ trợ.',
            'soHoDan.integer' => 'Số hộ dân phải là số.',
            'soHoDan.min' => 'Số hộ dân phải lớn hơn 0.',
            'mucDoKhanCap.required' => 'Vui lòng chọn mức độ khẩn cấp.',
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'hinhAnh.image' => 'Tệp tải lên phải là hình ảnh.',
            'hinhAnh.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'hinhAnh.max' => 'Hình ảnh không được vượt quá 4MB.',
        ]);

        $diaDiem = DiaDiem::where('tinhThanh', trim($request->tinhThanh))
            ->where('phuongXa', trim($request->phuongXa))
            ->where('chiTietDiaDiem', trim($request->chiTietDiaDiem))
            ->first();

        if (!$diaDiem) {
            $diaDiem = DiaDiem::create([
                'tinhThanh' => trim($request->tinhThanh),
                'phuongXa' => trim($request->phuongXa),
                'chiTietDiaDiem' => trim($request->chiTietDiaDiem),
                'viDo' => $request->viDo,
                'kinhDo' => $request->kinhDo,
            ]);
        }

        $duongDanHinhAnh = null;

        if ($request->hasFile('hinhAnh')) {
            $duongDanHinhAnh = $request->file('hinhAnh')->store('yeu-cau-cuu-tro', 'public');
        }

        YeuCauCuuTro::create([
            'idNguoiGui' => $idNguoiDung,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'loaiYeuCau' => trim($request->loaiYeuCau),
            'moTa' => trim($request->moTa),
            'soHoDan' => $request->soHoDan,
            'mucDoKhanCap' => $request->mucDoKhanCap,
            'hinhAnh' => $duongDanHinhAnh,
            'trangThai' => 'Chờ tiếp nhận',
            'thoiGianGui' => now(),
        ]);

        return redirect('/user/yeu-cau-cuu-tro')
            ->with('success', 'Gửi yêu cầu cứu trợ thành công.');
    }

    public function show(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $yeuCau = YeuCauCuuTro::with([
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich'
            ])
            ->where('idNguoiGui', $idNguoiDung)
            ->where('idYeuCau', $idYeuCau)
            ->firstOrFail();

        return view('user.yeu_cau_cuu_tro.show', compact('yeuCau'));
    }
}