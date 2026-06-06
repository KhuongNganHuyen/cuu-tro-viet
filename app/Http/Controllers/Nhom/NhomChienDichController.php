<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\ThienTai;
use App\Models\DiaDiem;
use App\Models\CapNhatChienDich;
use App\Models\DongGop;
use App\Models\ChiTietDongGop;
use App\Models\NguonLucChienDich;
use Illuminate\Http\Request;

class NhomChienDichController extends Controller
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

        $chienDichs = ChienDichCuuTro::with(['thienTai', 'diaDiem'])
            ->where('idNhom', $idNhom)
            ->orderBy('idChienDich', 'desc')
            ->get();

        return view('nhom.chien_dich.index', compact(
            'nhom',
            'chienDichs',
            'laNhomTruong'
        ));
    }

    public function create(int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/chien-dich')
                ->with('error', 'Chỉ nhóm trưởng mới có quyền thêm chiến dịch.');
        }

        $nhom = $kiemTra['nhom'];

        $thienTais = ThienTai::orderBy('namXayRa', 'desc')
            ->orderBy('tenThienTai')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->get();

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

        return view('nhom.chien_dich.create', compact(
            'nhom',
            'thienTais',
            'diaDiems',
            'diaDiemJson'
        ));
    }

    public function store(Request $request, int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/chien-dich')
                ->with('error', 'Chỉ nhóm trưởng mới có quyền thêm chiến dịch.');
        }

        $request->validate([
            'tenChienDich' => 'required|string|max:255',
            'idThienTai' => 'required|exists:ThienTai,idThienTai',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daThongBaoUBND' => 'nullable',
            'ghiChuUBND' => 'nullable|string|max:255',
            'trangThai' => 'required|string|max:255',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',
        ], [
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'idThienTai.required' => 'Vui lòng chọn thiên tai.',
            'idThienTai.exists' => 'Thiên tai không hợp lệ.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',

            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
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

        ChienDichCuuTro::create([
            'idNhom' => $idNhom,
            'idThienTai' => $request->idThienTai,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'tenChienDich' => $request->tenChienDich,
            'moTa' => $request->moTa,
            'ngayTao' => now(),
            'ngayBatDau' => $request->ngayBatDau,
            'ngayKetThuc' => $request->ngayKetThuc,
            'daThongBaoUBND' => $request->has('daThongBaoUBND'),
            'ghiChuUBND' => $request->ghiChuUBND,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/nhom/' . $idNhom . '/chien-dich')
            ->with('success', 'Thêm chiến dịch cứu trợ thành công.');
    }

    public function show(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $chienDich = ChienDichCuuTro::with(['thienTai', 'diaDiem', 'nhom'])
            ->where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $capNhats = CapNhatChienDich::with('thanhVien.nguoiDung')
            ->where('idChienDich', $idChienDich)
            ->orderBy('idCapNhat', 'desc')
            ->get();

        $dongGops = DongGop::with([
                'nguoiUngHo',
                'thanhVienTiepNhan.nguoiDung',
                'chiTietDongGops.hangHoa'
            ])
            ->where('idChienDich', $idChienDich)
            ->orderBy('idDongGop', 'desc')
            ->get();

        $nguonLucs = NguonLucChienDich::with('hangHoa')
            ->where('idChienDich', $idChienDich)
            ->orderBy('idNguonLuc', 'desc')
            ->get();
            
        return view('nhom.chien_dich.show', compact(
            'nhom',
            'chienDich',
            'laNhomTruong',
            'capNhats',
            'dongGops',
            'nguonLucs'
        ));
    }

    public function edit(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with('error', 'Chỉ nhóm trưởng mới có quyền sửa chiến dịch.');
        }

        $nhom = $kiemTra['nhom'];

        $chienDich = ChienDichCuuTro::with(['thienTai', 'diaDiem'])
            ->where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $thienTais = ThienTai::orderBy('namXayRa', 'desc')
            ->orderBy('tenThienTai')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->get();

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

        return view('nhom.chien_dich.edit', compact(
            'nhom',
            'chienDich',
            'thienTais',
            'diaDiems',
            'diaDiemJson'
        ));
    }

    public function update(Request $request, int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with('error', 'Chỉ nhóm trưởng mới có quyền sửa chiến dịch.');
        }

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $request->validate([
            'tenChienDich' => 'required|string|max:255',
            'idThienTai' => 'required|exists:ThienTai,idThienTai',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daThongBaoUBND' => 'nullable',
            'ghiChuUBND' => 'nullable|string|max:255',
            'trangThai' => 'required|string|max:255',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',
        ], [
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'idThienTai.required' => 'Vui lòng chọn thiên tai.',
            'idThienTai.exists' => 'Thiên tai không hợp lệ.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',

            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
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

        $chienDich->update([
            'idThienTai' => $request->idThienTai,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'tenChienDich' => $request->tenChienDich,
            'moTa' => $request->moTa,
            'ngayBatDau' => $request->ngayBatDau,
            'ngayKetThuc' => $request->ngayKetThuc,
            'daThongBaoUBND' => $request->has('daThongBaoUBND'),
            'ghiChuUBND' => $request->ghiChuUBND,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
            ->with('success', 'Cập nhật chiến dịch cứu trợ thành công.');
    }

    public function createCapNhat(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $thanhVien = $kiemTra['thanhVien'];

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        return view('nhom.chien_dich.cap_nhat_create', compact(
            'nhom',
            'chienDich',
            'thanhVien'
        ));
    }

    public function storeCapNhat(Request $request, int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $thanhVien = $kiemTra['thanhVien'];

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $request->validate([
            'noiDung' => 'required|string',
            'hinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'noiDung.required' => 'Vui lòng nhập nội dung cập nhật.',
            'hinhAnh.image' => 'Tệp tải lên phải là hình ảnh.',
            'hinhAnh.mimes' => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'hinhAnh.max' => 'Ảnh không được vượt quá 2MB.',
        ]);

        $duongDanHinhAnh = null;

        if ($request->hasFile('hinhAnh')) {
            $duongDanHinhAnh = $request->file('hinhAnh')->store('cap-nhat-chien-dich', 'public');
        }

        CapNhatChienDich::create([
            'idChienDich' => $chienDich->idChienDich,
            'idThanhVien' => $thanhVien->idThanhVien,
            'noiDung' => $request->noiDung,
            'hinhAnh' => $duongDanHinhAnh,
            'thoiGianCapNhat' => now(),
        ]);

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
            ->with('success', 'Thêm cập nhật tiến độ thành công.');
    }

    public function xacNhanChiTietDongGop(int $idNhom, int $idChienDich, int $idChiTietDongGop)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $thanhVien = $kiemTra['thanhVien'];

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $chiTiet = ChiTietDongGop::with('dongGop')
            ->where('idChiTietDongGop', $idChiTietDongGop)
            ->firstOrFail();

        if ($chiTiet->dongGop->idChienDich != $chienDich->idChienDich) {
            return back()->with('error', 'Chi tiết đóng góp không thuộc chiến dịch này.');
        }

        if ($chiTiet->trangThai == 'Đã xác nhận') {
            return back()->with('error', 'Chi tiết đóng góp này đã được xác nhận trước đó.');
        }

        if ($chiTiet->trangThai == 'Từ chối') {
            return back()->with('error', 'Chi tiết đóng góp này đã bị từ chối, không thể xác nhận.');
        }

        $nguonLuc = NguonLucChienDich::where('idChienDich', $idChienDich)
            ->where('idHangHoa', $chiTiet->idHangHoa)
            ->where(function ($query) use ($chiTiet) {
                if ($chiTiet->hanSuDung) {
                    $query->where('hanSuDung', $chiTiet->hanSuDung);
                } else {
                    $query->whereNull('hanSuDung');
                }
            })
            ->first();

        if ($nguonLuc) {
            $nguonLuc->update([
                'soLuongHienCo' => $nguonLuc->soLuongHienCo + $chiTiet->soLuong,
                'trangThai' => 'Còn hàng',
                'ngayCapNhat' => now(),
            ]);
        } else {
            NguonLucChienDich::create([
                'idChienDich' => $idChienDich,
                'idHangHoa' => $chiTiet->idHangHoa,
                'soLuongHienCo' => $chiTiet->soLuong,
                'hanSuDung' => $chiTiet->hanSuDung,
                'trangThai' => 'Còn hàng',
                'ngayCapNhat' => now(),
            ]);
        }

        $chiTiet->update([
            'trangThai' => 'Đã xác nhận',
        ]);

        $chiTiet->dongGop->update([
            'idNguoiTiepNhan' => $thanhVien->idThanhVien,
        ]);

        return back()->with('success', 'Xác nhận đóng góp và cộng vào nguồn lực thành công.');
    }

    public function tuChoiChiTietDongGop(int $idNhom, int $idChienDich, int $idChiTietDongGop)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        $chiTiet = ChiTietDongGop::with('dongGop')
            ->where('idChiTietDongGop', $idChiTietDongGop)
            ->firstOrFail();

        if ($chiTiet->dongGop->idChienDich != $chienDich->idChienDich) {
            return back()->with('error', 'Chi tiết đóng góp không thuộc chiến dịch này.');
        }

        if ($chiTiet->trangThai == 'Đã xác nhận') {
            return back()->with('error', 'Chi tiết đóng góp đã xác nhận nên không thể từ chối.');
        }

        $chiTiet->update([
            'trangThai' => 'Từ chối',
        ]);

        return back()->with('success', 'Đã từ chối chi tiết đóng góp.');
    }
}