<?php

namespace App\Http\Controllers;

use App\Models\NhomTinhNguyen;
use App\Models\NguoiDung;
use App\Models\DiaDiem;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\TiepNhanYeuCau;
use Illuminate\Http\Request;

class NhomTinhNguyenController extends Controller
{
    public function index()
    {
        $nhomTinhNguyens = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->orderBy('idNhom', 'desc')
            ->get();

        return view('admin.nhom_tinh_nguyen.index', compact('nhomTinhNguyens'));
    }

    public function create()
    {
        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->get();

        return view('admin.nhom_tinh_nguyen.create', compact('nguoiDungs', 'diaDiems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'idNhomTruong.required' => 'Vui lòng chọn nhóm trưởng.',
            'idNhomTruong.exists' => 'Nhóm trưởng không hợp lệ.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'idDiaDiem.exists' => 'Địa điểm không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $nhomTinhNguyen = NhomTinhNguyen::create([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $request->idDiaDiem,
            'trangThai' => $request->trangThai,
            'ngayTao' => now(),
        ]);

        if ($request->trangThai == 'Đang hoạt động' || $request->trangThai == 'Hoạt động') {
            ThanhVienNhom::firstOrCreate(
                [
                    'idNhom' => $nhomTinhNguyen->idNhom,
                    'idNguoiDung' => $request->idNhomTruong,
                ],
                [
                    'vaiTro' => 'Nhóm trưởng',
                    'ngayThamGia' => now(),
                ]
            );
        }
        return redirect('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom)
            ->with('success', 'Thêm nhóm tình nguyện thành công.');
    }

    public function show(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->findOrFail($id);

        $thanhViens = ThanhVienNhom::with('nguoiDung')
            ->where('idNhom', $id)
            ->orderBy('idThanhVien', 'desc')
            ->get();

        $chienDichs = ChienDichCuuTro::with(['thienTai', 'diaDiem'])
            ->where('idNhom', $id)
            ->orderBy('idChienDich', 'desc')
            ->get();

        return view('admin.nhom_tinh_nguyen.show', compact(
            'nhomTinhNguyen',
            'thanhViens',
            'chienDichs'
        ));
    }

    public function edit(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->orWhere('idNguoiDung', $nhomTinhNguyen->idNhomTruong)
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->get();

        return view('admin.nhom_tinh_nguyen.edit', compact('nhomTinhNguyen', 'nguoiDungs', 'diaDiems'));
    }

    public function update(Request $request, int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);
        $idNhomTruongCu = $nhomTinhNguyen->idNhomTruong;

        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'idNhomTruong.required' => 'Vui lòng chọn nhóm trưởng.',
            'idNhomTruong.exists' => 'Nhóm trưởng không hợp lệ.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'idDiaDiem.exists' => 'Địa điểm không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $nhomTinhNguyen->update([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $request->idDiaDiem,
            'trangThai' => $request->trangThai,
        ]);

        if ($request->trangThai == 'Đang hoạt động' || $request->trangThai == 'Hoạt động') {
            $thanhVienMoi = ThanhVienNhom::firstOrCreate(
                [
                    'idNhom' => $nhomTinhNguyen->idNhom,
                    'idNguoiDung' => $request->idNhomTruong,
                ],
                [
                    'vaiTro' => 'Nhóm trưởng',
                    'ngayThamGia' => now(),
                ]
            );

            $thanhVienMoi->update([
                'vaiTro' => 'Nhóm trưởng',
            ]);

            if ($idNhomTruongCu != $request->idNhomTruong) {
                $thanhVienCu = ThanhVienNhom::where('idNhom', $nhomTinhNguyen->idNhom)
                    ->where('idNguoiDung', $idNhomTruongCu)
                    ->where('vaiTro', 'Nhóm trưởng')
                    ->first();

                if ($thanhVienCu) {
                    $thanhVienCu->update([
                        'vaiTro' => 'Thành viên',
                    ]);
                }
            }
        }

        return redirect('/admin/nhom-tinh-nguyen/' . $nhomTinhNguyen->idNhom)
            ->with('success', 'Cập nhật nhóm tình nguyện thành công.');
    }

    public function destroy(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        $dangDuocSuDung =
            ThanhVienNhom::where('idNhom', $id)->exists()
            || ChienDichCuuTro::where('idNhom', $id)->exists()
            || TiepNhanYeuCau::where('idNhom', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('error', 'Không thể xóa nhóm này vì đang có dữ liệu liên quan. Bạn có thể khóa nhóm thay vì xóa.');
        }

        $nhomTinhNguyen->delete();

        return redirect('/admin/nhom-tinh-nguyen')
            ->with('success', 'Xóa nhóm tình nguyện thành công.');
    }

    public function doiTrangThai(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        if ($nhomTinhNguyen->trangThai == 'Chờ duyệt') {
            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('error', 'Nhóm đang chờ duyệt. Vui lòng dùng chức năng Duyệt nhóm hoặc Từ chối.');
        }

        if ($nhomTinhNguyen->trangThai == 'Từ chối') {
            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('error', 'Nhóm đã bị từ chối, không thể khóa/mở trạng thái như nhóm đang hoạt động.');
        }

        if ($nhomTinhNguyen->trangThai == 'Đang hoạt động' || $nhomTinhNguyen->trangThai == 'Hoạt động') {
            $nhomTinhNguyen->update([
                'trangThai' => 'Bị khóa',
            ]);

            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('success', 'Khóa nhóm tình nguyện thành công.');
        }

        if ($nhomTinhNguyen->trangThai == 'Bị khóa' || $nhomTinhNguyen->trangThai == 'Tạm ngưng') {
            $nhomTinhNguyen->update([
                'trangThai' => 'Đang hoạt động',
            ]);

            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('success', 'Mở nhóm tình nguyện thành công.');
        }

        return redirect('/admin/nhom-tinh-nguyen/' . $id)
            ->with('error', 'Trạng thái nhóm không hợp lệ.');
    }

    public function duyetNhom(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        if ($nhomTinhNguyen->trangThai != 'Chờ duyệt') {
            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('error', 'Chỉ có thể duyệt nhóm đang ở trạng thái Chờ duyệt.');
        }

        $nhomTinhNguyen->update([
            'trangThai' => 'Đang hoạt động',
        ]);

        // Sau khi duyệt, tự thêm người đăng ký làm nhóm trưởng trong bảng thành viên nhóm
        $daLaThanhVien = ThanhVienNhom::where('idNhom', $nhomTinhNguyen->idNhom)
            ->where('idNguoiDung', $nhomTinhNguyen->idNhomTruong)
            ->exists();

        if (!$daLaThanhVien) {
            ThanhVienNhom::create([
                'idNhom' => $nhomTinhNguyen->idNhom,
                'idNguoiDung' => $nhomTinhNguyen->idNhomTruong,
                'vaiTro' => 'Nhóm trưởng',
                'ngayThamGia' => now(),
            ]);
        }

        return redirect('/admin/nhom-tinh-nguyen/' . $id)
            ->with('success', 'Duyệt nhóm tình nguyện thành công.');
    }

    public function tuChoiNhom(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);

        if ($nhomTinhNguyen->trangThai != 'Chờ duyệt') {
            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('error', 'Chỉ có thể từ chối nhóm đang ở trạng thái Chờ duyệt.');
        }

        $nhomTinhNguyen->update([
            'trangThai' => 'Từ chối',
        ]);

        return redirect('/admin/nhom-tinh-nguyen/' . $id)
            ->with('success', 'Đã từ chối đăng ký tạo nhóm.');
    }
}