<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Models\YeuCauCuuTro;
use App\Models\DongGop;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

            'email' => 'nullable|required_without:sdt|email|unique:NguoiDung,email',
            'sdt' => 'nullable|required_without:email|string|max:15',

            'vaiTro' => 'required|string',
            'trangThai' => 'required|string',
            'gioiTinh' => 'nullable|string|max:50',
            'ngaySinh' => 'nullable|date',

            'anhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'hoTen.required' => 'Vui lòng nhập họ tên.',

            'tenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'tenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',

            'matKhau.required' => 'Vui lòng nhập mật khẩu.',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',

            'email.required_without' => 'Vui lòng nhập email hoặc số điện thoại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',

            'sdt.required_without' => 'Vui lòng nhập số điện thoại hoặc email.',
            'sdt.max' => 'Số điện thoại không được vượt quá 15 ký tự.',

            'ngaySinh.date' => 'Ngày sinh không hợp lệ.',

            'anhDaiDien.image' => 'Ảnh đại diện phải là file hình ảnh.',
            'anhDaiDien.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, png hoặc webp.',
            'anhDaiDien.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        $duongDanAnh = null;

        if ($request->hasFile('anhDaiDien')) {
            $duongDanAnh = $request->file('anhDaiDien')->store('nguoi-dung', 'public');
        }

        NguoiDung::create([
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'matKhau' => Hash::make($request->matKhau),
            'gioiTinh' => $request->gioiTinh,
            'anhDaiDien' => $duongDanAnh,
            'ngaySinh' => $request->ngaySinh,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'vaiTro' => $request->vaiTro,
            'trangThai' => $request->trangThai,
            'ngayTao' => now(),
        ]);

        return redirect('/admin/nguoi-dung')
            ->with('success', 'Thêm người dùng thành công.');
    }

    public function show(int $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        $yeuCaus = YeuCauCuuTro::with('diaDiem')
            ->where('idNguoiGui', $id)
            ->orderBy('idYeuCau', 'desc')
            ->get();

        $dongGops = DongGop::with(['chienDich', 'chiTietDongGops.hangHoa'])
            ->where('idNguoiUngHo', $id)
            ->orderBy('idDongGop', 'desc')
            ->get();

        $thanhVienNhoms = ThanhVienNhom::with('nhom')
            ->where('idNguoiDung', $id)
            ->orderBy('idThanhVien', 'desc')
            ->get();

        return view('admin.nguoi_dung.show', compact(
            'nguoiDung',
            'yeuCaus',
            'dongGops',
            'thanhVienNhoms'
        ));
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

            'email' => 'nullable|required_without:sdt|email|unique:NguoiDung,email,' . $id . ',idNguoiDung',
            'sdt' => 'nullable|required_without:email|string|max:15',

            'vaiTro' => 'required|string',
            'trangThai' => 'required|string',
            'gioiTinh' => 'nullable|string|max:50',
            'ngaySinh' => 'nullable|date',

            'anhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'hoTen.required' => 'Vui lòng nhập họ tên.',

            'tenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'tenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',

            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',

            'email.required_without' => 'Vui lòng nhập email hoặc số điện thoại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',

            'sdt.required_without' => 'Vui lòng nhập số điện thoại hoặc email.',
            'sdt.max' => 'Số điện thoại không được vượt quá 15 ký tự.',

            'ngaySinh.date' => 'Ngày sinh không hợp lệ.',

            'anhDaiDien.image' => 'Ảnh đại diện phải là file hình ảnh.',
            'anhDaiDien.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, png hoặc webp.',
            'anhDaiDien.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        $data = [
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'gioiTinh' => $request->gioiTinh,
            'ngaySinh' => $request->ngaySinh,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'vaiTro' => $request->vaiTro,
            'trangThai' => $request->trangThai,
        ];

        if ($request->filled('matKhau')) {
            $data['matKhau'] = Hash::make($request->matKhau);
        }

        if ($request->hasFile('anhDaiDien')) {
            if ($nguoiDung->anhDaiDien && Storage::disk('public')->exists($nguoiDung->anhDaiDien)) {
                Storage::disk('public')->delete($nguoiDung->anhDaiDien);
            }

            $data['anhDaiDien'] = $request->file('anhDaiDien')->store('nguoi-dung', 'public');
        }

        $nguoiDung->update($data);

        return redirect('/admin/nguoi-dung/' . $nguoiDung->idNguoiDung)
            ->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(int $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        $dangDuocSuDung =
            YeuCauCuuTro::where('idNguoiGui', $id)->exists()
            || DongGop::where('idNguoiUngHo', $id)->exists()
            || ThanhVienNhom::where('idNguoiDung', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/nguoi-dung/' . $id)
                ->with('error', 'Không thể xóa người dùng này vì đang có dữ liệu liên quan. Bạn có thể khóa tài khoản thay vì xóa.');
        }

        if ($nguoiDung->anhDaiDien && Storage::disk('public')->exists($nguoiDung->anhDaiDien)) {
            Storage::disk('public')->delete($nguoiDung->anhDaiDien);
        }

        $nguoiDung->delete();

        return redirect('/admin/nguoi-dung')
            ->with('success', 'Xóa người dùng thành công.');
    }

    public function doiTrangThai(int $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        if ($nguoiDung->trangThai == 'Hoạt động') {
            $nguoiDung->update([
                'trangThai' => 'Bị khóa',
            ]);

            return redirect('/admin/nguoi-dung/' . $id)
                ->with('success', 'Khóa tài khoản thành công.');
        }

        $nguoiDung->update([
            'trangThai' => 'Hoạt động',
        ]);

        return redirect('/admin/nguoi-dung/' . $id)
            ->with('success', 'Mở khóa tài khoản thành công.');
    }
}