<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\NguoiDung;
use Illuminate\Http\Request;

class NhomThanhVienController extends Controller
{
    private function kiemTraThanhVien(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.'),
            ];
        }

        $nhom = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])->findOrFail($idNhom);

        if ($nhom->trangThai != 'Đang hoạt động') {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with('error', 'Nhóm này chưa được phép hoạt động hoặc đã bị khóa.'),
            ];
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->first();

        if (!$thanhVien) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with('error', 'Bạn không thuộc nhóm tình nguyện này.'),
            ];
        }

        $laNhomTruong = $thanhVien->vaiTro == 'Nhóm trưởng'
            || $nhom->idNhomTruong == $idNguoiDung;

        return [
            'hopLe' => true,
            'nhom' => $nhom,
            'thanhVien' => $thanhVien,
            'laNhomTruong' => $laNhomTruong,
            'vaiTroTrongNhom' => $thanhVien->vaiTro ?? 'Thành viên',
        ];
    }

    public function index(int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];
        $vaiTroTrongNhom = $kiemTra['vaiTroTrongNhom'];

        $thanhViens = ThanhVienNhom::with('nguoiDung')
            ->where('idNhom', $idNhom)
            ->orderByRaw("CASE WHEN vaiTro = 'Nhóm trưởng' THEN 0 ELSE 1 END")
            ->orderBy('idThanhVien', 'desc')
            ->get();

        return view('nhom.thanh_vien.index', compact(
            'nhom',
            'thanhViens',
            'laNhomTruong',
            'vaiTroTrongNhom'
        ));
    }

    public function create(int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/thanh-vien')
                ->with('error', 'Chỉ nhóm trưởng mới có quyền thêm thành viên.');
        }

        $nhom = $kiemTra['nhom'];

        $idThanhVienDaCo = ThanhVienNhom::where('idNhom', $idNhom)
            ->pluck('idNguoiDung')
            ->toArray();

        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->where('vaiTro', '!=', 'Quản trị viên')
            ->whereNotIn('idNguoiDung', $idThanhVienDaCo)
            ->orderBy('hoTen')
            ->get();

        $nguoiDungJson = $nguoiDungs->map(function ($nguoiDung) {
            return [
                'idNguoiDung' => $nguoiDung->idNguoiDung,
                'hoTen' => $nguoiDung->hoTen,
                'tenDangNhap' => $nguoiDung->tenDangNhap,
                'email' => $nguoiDung->email,
                'sdt' => $nguoiDung->sdt,
                'label' => $nguoiDung->hoTen . ' - ' . $nguoiDung->tenDangNhap,
            ];
        })->values()->toJson();

        return view('nhom.thanh_vien.create', compact(
            'nhom',
            'nguoiDungs',
            'nguoiDungJson'
        ));
    }

    public function store(Request $request, int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/thanh-vien')
                ->with('error', 'Chỉ nhóm trưởng mới có quyền thêm thành viên.');
        }

        $request->validate([
            'idNguoiDung' => 'required|exists:NguoiDung,idNguoiDung',
            'vaiTro' => 'nullable|string|max:255',
        ], [
            'idNguoiDung.required' => 'Vui lòng chọn người dùng hợp lệ từ danh sách gợi ý.',
            'idNguoiDung.exists' => 'Người dùng không hợp lệ.',
            'vaiTro.max' => 'Vai trò trong nhóm không được vượt quá 255 ký tự.',
        ]);

        $daTonTai = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $request->idNguoiDung)
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with('error', 'Người dùng này đã là thành viên của nhóm.');
        }

        $vaiTroNhap = trim($request->vaiTro ?? '');

        if ($vaiTroNhap == '') {
            $vaiTroNhap = 'Thành viên';
        }

        $vaiTroKiemTra = mb_strtolower($vaiTroNhap, 'UTF-8');

        if ($vaiTroKiemTra == 'nhóm trưởng' || $vaiTroKiemTra == 'nhom truong') {
            return back()
                ->withInput()
                ->with('error', 'Không thể thêm thành viên với vai trò Nhóm trưởng tại đây. Nếu muốn chuyển nhượng nhóm trưởng, vui lòng vào phần Sửa thông tin nhóm.');
        }

        ThanhVienNhom::create([
            'idNhom' => $idNhom,
            'idNguoiDung' => $request->idNguoiDung,
            'vaiTro' => $vaiTroNhap,
            'ngayThamGia' => now(),
        ]);

        return redirect('/nhom/' . $idNhom . '/thanh-vien')
            ->with('success', 'Thêm thành viên nhóm thành công.');
    }

    public function destroy(int $idNhom, int $idThanhVien)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/thanh-vien')
                ->with('error', 'Chỉ nhóm trưởng mới có quyền xóa thành viên.');
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idThanhVien', $idThanhVien)
            ->firstOrFail();

        if ($thanhVien->vaiTro == 'Nhóm trưởng') {
            return redirect('/nhom/' . $idNhom . '/thanh-vien')
                ->with('error', 'Không thể xóa nhóm trưởng khỏi nhóm tại đây.');
        }

        $thanhVien->delete();

        return redirect('/nhom/' . $idNhom . '/thanh-vien')
            ->with('success', 'Xóa thành viên khỏi nhóm thành công.');
    }
}