<?php

namespace App\Http\Controllers;

use App\Models\NhomTinhNguyen;
use App\Models\NguoiDung;
use App\Models\DiaDiem;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\TiepNhanYeuCau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NhomTinhNguyenController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $nhomTinhNguyens = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->orderBy('idNhom', 'asc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $nhomTinhNguyens = $nhomTinhNguyens->filter(function ($nhom) use ($tuKhoa, $tuKhoaKhongDau) {
                $diaDiem = $nhom->diaDiem;

                $noiDungTimKiem = implode(' ', [
                    $nhom->idNhom,
                    $nhom->tenNhom,
                    $nhom->moTa,
                    $nhom->trangThai,
                    $nhom->ngayTao,
                    $nhom->nhomTruong->hoTen ?? '',
                    $nhom->nhomTruong->tenDangNhap ?? '',
                    $diaDiem->chiTietDiaDiem ?? '',
                    $diaDiem->phuongXa ?? '',
                    $diaDiem->tinhThanh ?? '',
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem, 'UTF-8'), mb_strtolower($tuKhoa, 'UTF-8'))
                    || str_contains(mb_strtolower($noiDungKhongDau, 'UTF-8'), mb_strtolower($tuKhoaKhongDau, 'UTF-8'));
            })->values();
        }

        if ($tuKhoa === '' && session()->has('nhomMoi')) {
            $idMoi = session('nhomMoi');

            $nhomTinhNguyens = $nhomTinhNguyens->sortBy(function ($nhom) use ($idMoi) {
                return $nhom->idNhom == $idMoi ? -1 : $nhom->idNhom;
            })->values();
        }

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
            'anhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',

            'trangThai' => 'nullable|string|in:Đang hoạt động',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',

            'anhDaiDien.image' => 'Ảnh đại diện nhóm phải là file hình ảnh.',
            'anhDaiDien.mimes' => 'Ảnh đại diện nhóm phải có định dạng jpg, jpeg, png hoặc webp.',
            'anhDaiDien.max' => 'Ảnh đại diện nhóm không được vượt quá 2MB.',

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

        $diaDiem = $this->layHoacTaoDiaDiem($request);

        $duongDanAnh = 'nhom-tinh-nguyen/group.jpg';

        if ($request->hasFile('anhDaiDien')) {
            $duongDanAnh = $request->file('anhDaiDien')->store('nhom-tinh-nguyen', 'public');
        }

        $nhomTinhNguyen = NhomTinhNguyen::create([
            'tenNhom' => trim($request->tenNhom),
            'moTa' => $request->moTa,
            'anhDaiDien' => $duongDanAnh,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'trangThai' => 'Đang hoạt động',
            'ngayTao' => now(),
        ]);

        if ($nhomTinhNguyen->trangThai == 'Đang hoạt động') {
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
            ->with('success', 'Thêm nhóm tình nguyện thành công.')
            ->with('nhomMoi', $nhomTinhNguyen->idNhom);
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

        $chienDichs = ChienDichCuuTro::with(['suKien', 'diaDiem'])
            ->where('idNhom', $id)
            ->orderBy('idChienDich', 'desc')
            ->get();

        $yeuCauDaNhans = TiepNhanYeuCau::with(['yeuCau.diaDiem'])
            ->where('idNhom', $id)
            ->orderBy('idTiepNhan', 'desc')
            ->get();

        return view('admin.nhom_tinh_nguyen.show', compact(
            'nhomTinhNguyen',
            'thanhViens',
            'chienDichs',
            'yeuCauDaNhans'
        ));
    }

    public function edit(int $id)
    {
        $nhomTinhNguyen = NhomTinhNguyen::with(['diaDiem', 'nhomTruong'])
            ->findOrFail($id);

        $nguoiDungs = NguoiDung::where(function ($query) use ($nhomTinhNguyen) {
                $query->where(function ($q) {
                    $q->where('trangThai', 'Hoạt động')
                        ->where('vaiTro', '!=', 'Quản trị viên');
                })
                ->orWhere('idNguoiDung', $nhomTinhNguyen->idNhomTruong);
            })
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
            'anhDaiDien' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'idNhomTruong' => 'required|exists:NguoiDung,idNguoiDung',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',

            'trangThai' => 'required|string|in:Chờ duyệt,Đang hoạt động,Tạm ngừng hoạt động,Ngừng hoạt động,Bị khóa,Từ chối',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',

            'anhDaiDien.image' => 'Ảnh đại diện nhóm phải là file hình ảnh.',
            'anhDaiDien.mimes' => 'Ảnh đại diện nhóm phải có định dạng jpg, jpeg, png hoặc webp.',
            'anhDaiDien.max' => 'Ảnh đại diện nhóm không được vượt quá 2MB.',

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

        $trangThaiHienTai = $nhomTinhNguyen->trangThai;
        $trangThaiMoi = $request->trangThai;

        if ($trangThaiHienTai == 'Từ chối' && $trangThaiMoi != 'Từ chối') {
            return back()
                ->withInput()
                ->withErrors(['trangThai' => 'Nhóm đã bị từ chối thì không thể chuyển sang trạng thái khác.']);
        }

        if ($trangThaiHienTai == 'Chờ duyệt') {
            $trangThaiHopLe = ['Chờ duyệt', 'Đang hoạt động', 'Từ chối'];
        } else {
            $trangThaiHopLe = ['Đang hoạt động', 'Tạm ngừng hoạt động', 'Ngừng hoạt động', 'Bị khóa'];
        }

        if (!in_array($trangThaiMoi, $trangThaiHopLe)) {
            return back()
                ->withInput()
                ->withErrors(['trangThai' => 'Trạng thái nhóm không hợp lệ theo luồng hiện tại.']);
        }

        $diaDiem = $this->layHoacTaoDiaDiem($request);

        $duLieuCapNhat = [
            'tenNhom' => trim($request->tenNhom),
            'moTa' => $request->moTa,
            'idNhomTruong' => $request->idNhomTruong,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'trangThai' => $request->trangThai,
        ];

        if ($request->hasFile('anhDaiDien')) {
            if (
                $nhomTinhNguyen->anhDaiDien
                && $nhomTinhNguyen->anhDaiDien !== 'nhom-tinh-nguyen/group.jpg'
                && Storage::disk('public')->exists($nhomTinhNguyen->anhDaiDien)
            ) {
                Storage::disk('public')->delete($nhomTinhNguyen->anhDaiDien);
            }

            $duLieuCapNhat['anhDaiDien'] = $request->file('anhDaiDien')->store('nhom-tinh-nguyen', 'public');
        }

        $nhomTinhNguyen->update($duLieuCapNhat);

        if ($request->trangThai == 'Đang hoạt động') {
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

        if (
            $nhomTinhNguyen->anhDaiDien
            && $nhomTinhNguyen->anhDaiDien !== 'nhom-tinh-nguyen/group.jpg'
            && Storage::disk('public')->exists($nhomTinhNguyen->anhDaiDien)
        ) {
            Storage::disk('public')->delete($nhomTinhNguyen->anhDaiDien);
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
                ->with('error', 'Nhóm đã bị từ chối, không thể mở lại hoạt động.');
        }

        if ($nhomTinhNguyen->trangThai == 'Bị khóa') {
            $nhomTinhNguyen->update([
                'trangThai' => 'Đang hoạt động',
            ]);

            return redirect('/admin/nhom-tinh-nguyen/' . $id)
                ->with('success', 'Mở khóa nhóm tình nguyện thành công.');
        }

        $nhomTinhNguyen->update([
            'trangThai' => 'Bị khóa',
        ]);

        return redirect('/admin/nhom-tinh-nguyen/' . $id)
            ->with('success', 'Khóa nhóm tình nguyện thành công.');
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

    private function layHoacTaoDiaDiem(Request $request): DiaDiem
    {
        $tinhThanh = trim($request->tinhThanh);
        $phuongXa = trim($request->phuongXa);
        $chiTietDiaDiem = trim($request->chiTietDiaDiem);

        $diaDiem = DiaDiem::where('tinhThanh', $tinhThanh)
            ->where('phuongXa', $phuongXa)
            ->where('chiTietDiaDiem', $chiTietDiaDiem)
            ->first();

        if ($diaDiem) {
            return $diaDiem;
        }

        return DiaDiem::create([
            'tinhThanh' => $tinhThanh,
            'phuongXa' => $phuongXa,
            'chiTietDiaDiem' => $chiTietDiaDiem,
            'viDo' => $request->viDo,
            'kinhDo' => $request->kinhDo,
        ]);
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($coDau, $khongDau, $chuoi);
    }
}