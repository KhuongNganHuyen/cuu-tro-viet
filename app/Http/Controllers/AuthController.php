<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'tenDangNhap' => 'required|string',
            'matKhau' => 'required|string',
        ], [
            'tenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'matKhau.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $nguoiDung = NguoiDung::where('tenDangNhap', $request->tenDangNhap)->first();

        if (!$nguoiDung || !Hash::check($request->matKhau, $nguoiDung->matKhau)) {
            return back()
                ->withInput()
                ->with('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
        }

        if ($nguoiDung->trangThai != 'Hoạt động') {
            return back()
                ->withInput()
                ->with('error', 'Tài khoản của bạn hiện không hoạt động hoặc đã bị khóa.');
        }

        session([
            'idNguoiDung' => $nguoiDung->idNguoiDung,
            'hoTen' => $nguoiDung->hoTen,
            'tenDangNhap' => $nguoiDung->tenDangNhap,
            'vaiTro' => $nguoiDung->vaiTro,
            'anhDaiDien' => $nguoiDung->anhDaiDien,
        ]);

        if ($nguoiDung->vaiTro == 'Quản trị viên') {
            return redirect('/admin/dashboard');
        }

        return redirect('/user/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'hoTen' => 'required|string|max:255',
            'tenDangNhap' => 'required|string|max:255|unique:NguoiDung,tenDangNhap',
            'matKhau' => 'required|string|min:6|confirmed',
            'email' => 'nullable|required_without:sdt|email|unique:NguoiDung,email',
            'sdt' => 'nullable|required_without:email|string|max:10',
            'gioiTinh' => 'nullable|string|max:255',
            'ngaySinh' => 'nullable|date',
        ], [
            'hoTen.required' => 'Vui lòng nhập họ tên.',
            'tenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'tenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'matKhau.required' => 'Vui lòng nhập mật khẩu.',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'matKhau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'email.required_without' => 'Vui lòng nhập email hoặc số điện thoại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'sdt.required_without' => 'Vui lòng nhập số điện thoại hoặc email.',
            'sdt.max' => 'Số điện thoại không được vượt quá 10 ký tự.',
            'ngaySinh.date' => 'Ngày sinh không hợp lệ.',
        ]);

        $nguoiDung = NguoiDung::create([
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'matKhau' => Hash::make($request->matKhau),
            'gioiTinh' => $request->gioiTinh,
            'ngaySinh' => $request->ngaySinh,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'vaiTro' => 'Người dùng',
            'trangThai' => 'Hoạt động',
            'ngayTao' => now(),
        ]);

        session([
            'idNguoiDung' => $nguoiDung->idNguoiDung,
            'hoTen' => $nguoiDung->hoTen,
            'tenDangNhap' => $nguoiDung->tenDangNhap,
            'vaiTro' => $nguoiDung->vaiTro,
            'anhDaiDien' => $nguoiDung->anhDaiDien,
        ]);

        return redirect('/user/dashboard')->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();

        return redirect('/login')->with('success', 'Đăng xuất thành công.');
    }
}