<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nguoiDung = NguoiDung::findOrFail($idNguoiDung);

        return view('profile.edit', compact('nguoiDung'));
    }

    public function update(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nguoiDung = NguoiDung::findOrFail($idNguoiDung);

        $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:NguoiDung,email,' . $nguoiDung->idNguoiDung . ',idNguoiDung',
            'sdt' => 'nullable|string|max:20',
            'gioiTinh' => 'nullable|string|max:20',
            'ngaySinh' => 'nullable|date',
            'anhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'hoTen.required' => 'Vui lòng nhập họ tên.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'anhDaiDien.image' => 'Tệp tải lên phải là hình ảnh.',
            'anhDaiDien.mimes' => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'anhDaiDien.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        $duongDanAnh = $nguoiDung->anhDaiDien;

        if ($request->hasFile('anhDaiDien')) {
            if ($duongDanAnh && Storage::disk('public')->exists($duongDanAnh)) {
                Storage::disk('public')->delete($duongDanAnh);
            }

            $duongDanAnh = $request->file('anhDaiDien')
                ->store('anh-dai-dien', 'public');
        }

        $duLieuCapNhat = [
            'hoTen' => $request->hoTen,
            'email' => $request->email,
            'sdt' => $request->sdt,
            'gioiTinh' => $request->gioiTinh,
            'ngaySinh' => $request->ngaySinh,
            'anhDaiDien' => $duongDanAnh,
        ];

        $nguoiDung->update($duLieuCapNhat);

        return redirect('/ho-so')
            ->with('success', 'Cập nhật hồ sơ cá nhân thành công.');
    }

    public function editPassword()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nguoiDung = NguoiDung::findOrFail($idNguoiDung);

        return view('profile.doi_mat_khau', compact('nguoiDung'));
    }

    public function updatePassword(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nguoiDung = NguoiDung::findOrFail($idNguoiDung);

        $request->validate([
            'matKhauCu' => 'required|string',
            'matKhauMoi' => 'required|string|min:6|confirmed',
        ], [
            'matKhauCu.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'matKhauMoi.required' => 'Vui lòng nhập mật khẩu mới.',
            'matKhauMoi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'matKhauMoi.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        if (!Hash::check($request->matKhauCu, $nguoiDung->matKhau)) {
            return back()
                ->withInput()
                ->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        if (Hash::check($request->matKhauMoi, $nguoiDung->matKhau)) {
            return back()
                ->withInput()
                ->with('error', 'Mật khẩu mới không được trùng với mật khẩu hiện tại.');
        }

        $nguoiDung->update([
            'matKhau' => Hash::make($request->matKhauMoi),
        ]);

        return redirect('/doi-mat-khau')
            ->with('success', 'Đổi mật khẩu thành công.');
    }
}