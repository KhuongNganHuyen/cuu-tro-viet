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
        ->where('vaiTro', '!=', 'Quản trị viên')
        ->orderBy('hoTen')
        ->get();

    $diaDiems = DiaDiem::orderBy('tinhThanh')
        ->orderBy('phuongXa')
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

    $diaDiemJson = $diaDiems->map(function ($diaDiem) {
        return [
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'tinhThanh' => $diaDiem->tinhThanh,
            'phuongXa' => $diaDiem->phuongXa,
            'chiTietDiaDiem' => $diaDiem->chiTietDiaDiem,
            'viDo' => $diaDiem->viDo,
            'kinhDo' => $diaDiem->kinhDo,
            'label' => trim(
                ($diaDiem->chiTietDiaDiem ? $diaDiem->chiTietDiaDiem . ', ' : '') .
                ($diaDiem->phuongXa ? $diaDiem->phuongXa . ', ' : '') .
                $diaDiem->tinhThanh
            ),
        ];
    })->values()->toJson();

    return view('admin.nhom_tinh_nguyen.create', compact(
        'nguoiDungs',
        'diaDiems',
        'nguoiDungJson',
        'diaDiemJson'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',

            'trangThai' => 'required|string|max:255',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'idNhomTruong.required' => 'Vui lòng chọn nhóm trưởng hợp lệ từ danh sách gợi ý.',
            'idNhomTruong.exists' => 'Nhóm trưởng không hợp lệ.',

            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
        ]);

        $diaDiem = null;

        if ($request->idDiaDiemCoSan) {
            $diaDiem = DiaDiem::findOrFail($request->idDiaDiemCoSan);
        } else {
            $diaDiem = DiaDiem::where('tinhThanh', $request->tinhThanh)
                ->where('phuongXa', $request->phuongXa)
                ->where('chiTietDiaDiem', $request->chiTietDiaDiem)
                ->first();

            if (!$diaDiem) {
                $diaDiem = DiaDiem::create([
                    'tinhThanh' => $request->tinhThanh,
                    'phuongXa' => $request->phuongXa,
                    'chiTietDiaDiem' => $request->chiTietDiaDiem,
                    'viDo' => $request->viDo,
                    'kinhDo' => $request->kinhDo,
                ]);
            }
        }

        $nhomTinhNguyen = NhomTinhNguyen::create([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $diaDiem->idDiaDiem,
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
            ->orderByRaw("CASE WHEN vaiTro = 'Nhóm trưởng' THEN 0 ELSE 1 END")
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
        $nhomTinhNguyen = NhomTinhNguyen::with(['diaDiem', 'nhomTruong'])
            ->findOrFail($id);

        $nguoiDungs = NguoiDung::where('trangThai', 'Hoạt động')
            ->where('vaiTro', '!=', 'Quản trị viên')
            ->orWhere('idNguoiDung', $nhomTinhNguyen->idNhomTruong)
            ->orderBy('hoTen')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
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

        $diaDiemJson = $diaDiems->map(function ($diaDiem) {
            return [
                'idDiaDiem' => $diaDiem->idDiaDiem,
                'tinhThanh' => $diaDiem->tinhThanh,
                'phuongXa' => $diaDiem->phuongXa,
                'chiTietDiaDiem' => $diaDiem->chiTietDiaDiem,
                'viDo' => $diaDiem->viDo,
                'kinhDo' => $diaDiem->kinhDo,
                'label' => trim(
                    ($diaDiem->chiTietDiaDiem ? $diaDiem->chiTietDiaDiem . ', ' : '') .
                    ($diaDiem->phuongXa ? $diaDiem->phuongXa . ', ' : '') .
                    $diaDiem->tinhThanh
                ),
            ];
        })->values()->toJson();

        $tenNhomTruongHienThi = $nhomTinhNguyen->nhomTruong
            ? $nhomTinhNguyen->nhomTruong->hoTen . ' - ' . $nhomTinhNguyen->nhomTruong->tenDangNhap
            : '';

        return view('admin.nhom_tinh_nguyen.edit', compact(
            'nhomTinhNguyen',
            'nguoiDungs',
            'diaDiems',
            'nguoiDungJson',
            'diaDiemJson',
            'tenNhomTruongHienThi'
        ));
    }

    public function update(Request $request, int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::findOrFail($id);
        $idNhomTruongCu = $nhomTinhNguyen->idNhomTruong;

        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',

            'trangThai' => 'required|string|max:255',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'idNhomTruong.required' => 'Vui lòng chọn nhóm trưởng hợp lệ từ danh sách gợi ý.',
            'idNhomTruong.exists' => 'Nhóm trưởng không hợp lệ.',

            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',

            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);
        
        $tinhThanh = trim($request->tinhThanh);
        $phuongXa = trim($request->phuongXa);
        $chiTietDiaDiem = trim($request->chiTietDiaDiem);

        $diaDiem = null;

        if ($request->idDiaDiemCoSan) {
            $diaDiem = DiaDiem::findOrFail($request->idDiaDiemCoSan);
        } else {
            $diaDiem = DiaDiem::where('tinhThanh', $tinhThanh)
                ->where('phuongXa', $phuongXa)
                ->where('chiTietDiaDiem', $chiTietDiaDiem)
                ->first();

            if (!$diaDiem) {
                $diaDiem = DiaDiem::create([
                    'tinhThanh' => $tinhThanh,
                    'phuongXa' => $phuongXa,
                    'chiTietDiaDiem' => $chiTietDiaDiem,
                    'viDo' => $request->viDo,
                    'kinhDo' => $request->kinhDo,
                ]);
            }
        }

        $nhomTinhNguyen->update([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $diaDiem->idDiaDiem,
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