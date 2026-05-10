<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NguoiDungController extends Controller
{
    public function index()
    {
        $nguoiDungs = NguoiDung::orderBy('idNguoiDung', 'desc')->get();

        return view('admin.nguoi_dung.index', compact('nguoiDungs'));
    }

    public function create()
    {
        return view('admin.nguoi_dung.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoTen' => 'required|string|max:255',
            'tenDangNhap' => 'required|string|max:255|unique:NguoiDung,tenDangNhap',
            'matKhau' => 'required|string|min:6',
            'email' => 'nullable|email|unique:NguoiDung,email',
            'sdt' => 'nullable|string|max:10',
            'vaiTro' => 'required|string',
            'trangThai' => 'required|string',
            'anhDaiDien' => 'nullable|string|max:255',
        ], [
            'hoTen.required' => 'Vui lòng nhập họ tên.',
            'tenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'tenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'matKhau.required' => 'Vui lòng nhập mật khẩu.',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
        ]);

        NguoiDung::create([
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'matKhau' => Hash::make($request->matKhau),
            'gioiTinh' => $request->gioiTinh,
            'anhDaiDien' => $request->anhDaiDien,
            'ngaySinh' => $request->ngaySinh,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'vaiTro' => $request->vaiTro,
            'trangThai' => $request->trangThai,
            'ngayTao' => now(),
        ]);

        return redirect('/admin/nguoi-dung')->with('success', 'Thêm người dùng thành công.');
    }

    public function edit(int $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        return view('admin.nguoi_dung.edit', compact('nguoiDung'));
    }

    public function update(Request $request, int $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        $request->validate([
            'hoTen' => 'required|string|max:255',
            'tenDangNhap' => 'required|string|max:255|unique:NguoiDung,tenDangNhap,' . $id . ',idNguoiDung',
            'matKhau' => 'nullable|string|min:6',
            'email' => 'nullable|email|unique:NguoiDung,email,' . $id . ',idNguoiDung',
            'sdt' => 'nullable|string|max:10',
            'vaiTro' => 'required|string',
            'trangThai' => 'required|string',
            'anhDaiDien' => 'nullable|string|max:255',
        ], [
            'hoTen.required' => 'Vui lòng nhập họ tên.',
            'tenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'tenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
        ]);

        $data = [
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'gioiTinh' => $request->gioiTinh,
            'anhDaiDien' => $request->anhDaiDien,
            'ngaySinh' => $request->ngaySinh,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'vaiTro' => $request->vaiTro,
            'trangThai' => $request->trangThai,
        ];

        if ($request->filled('matKhau')) {
            $data['matKhau'] = Hash::make($request->matKhau);
        }

        $nguoiDung->update($data);

        return redirect('/admin/nguoi-dung')->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(int $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        $nguoiDung->delete();

        return redirect('/admin/nguoi-dung')->with('success', 'Xóa người dùng thành công.');
    }
}