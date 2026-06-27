<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\TiepNhanYeuCau;
use App\Models\DongGop;
use App\Models\DotPhanPhoi;
use App\Models\NguonLucChienDich;
use App\Models\DiaDiem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NhomDashboardController extends Controller
{
    public function index(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhom = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->findOrFail($idNhom);

        if (in_array($nhom->trangThai, ['Chờ duyệt', 'Bị khóa', 'Từ chối'])) {
            return redirect('/user/nhom-cua-toi')
                ->with('error', 'Nhóm này chưa được phép hoạt động hoặc đã bị khóa.');
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->where('vaiTro', '!=', 'Đã rời nhóm')
            ->first();

        if (!$thanhVien) {
            return redirect('/user/nhom-cua-toi')
                ->with('error', 'Bạn không thuộc nhóm tình nguyện này.');
        }

        $vaiTroTrongNhom = $thanhVien->vaiTro ?? 'Thành viên';
        $laNhomTruong = $vaiTroTrongNhom === 'Nhóm trưởng';

        $idChienDichs = ChienDichCuuTro::where('idNhom', $idNhom)
            ->pluck('idChienDich');

        $soThanhVien = ThanhVienNhom::where('idNhom', $idNhom)->count();
        $soChienDich = ChienDichCuuTro::where('idNhom', $idNhom)->count();

        $soChienDichDangDienRa = ChienDichCuuTro::where('idNhom', $idNhom)
            ->whereIn('trangThai', ['Đang hoạt động', 'Đang diễn ra'])
            ->count();

        $soYeuCauTiepNhan = TiepNhanYeuCau::where('idNhom', $idNhom)->count();

        $soYeuCauDangXuLy = TiepNhanYeuCau::where('idNhom', $idNhom)
            ->whereIn('trangThai', ['Đã tiếp nhận', 'Đang hỗ trợ', 'Cần thêm hỗ trợ'])
            ->count();

        $soYeuCauHoanThanh = TiepNhanYeuCau::where('idNhom', $idNhom)
            ->where('trangThai', 'Hoàn thành')
            ->count();

        $soDongGop = DongGop::whereIn('idChienDich', $idChienDichs)->count();

        $soDongGopDaXacNhan = DongGop::whereIn('idChienDich', $idChienDichs)
            ->whereHas('chiTietDongGops', function ($query) {
                $query->where('trangThai', 'Đã xác nhận');
            })
            ->count();

        $soDotPhanPhoi = DotPhanPhoi::whereIn('idChienDich', $idChienDichs)->count();

        $soDotPhanPhoiHoanThanh = DotPhanPhoi::whereIn('idChienDich', $idChienDichs)
            ->where('trangThai', 'Hoàn thành')
            ->count();

        $soHangHoaSuDung = NguonLucChienDich::whereIn('idChienDich', $idChienDichs)
            ->distinct('idHangHoa')
            ->count('idHangHoa');

        $thongKe = [
            'soThanhVien' => $soThanhVien,
            'soChienDich' => $soChienDich,
            'soChienDichDangDienRa' => $soChienDichDangDienRa,
            'soYeuCauTiepNhan' => $soYeuCauTiepNhan,
            'soYeuCauDangXuLy' => $soYeuCauDangXuLy,
            'soYeuCauHoanThanh' => $soYeuCauHoanThanh,
            'soDongGop' => $soDongGop,
            'soDongGopDaXacNhan' => $soDongGopDaXacNhan,
            'soDotPhanPhoi' => $soDotPhanPhoi,
            'soDotPhanPhoiHoanThanh' => $soDotPhanPhoiHoanThanh,
            'soHangHoaSuDung' => $soHangHoaSuDung,
        ];

        $thongKeTrangThaiChienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->selectRaw('trangThai, COUNT(*) as tong')
            ->groupBy('trangThai')
            ->pluck('tong', 'trangThai');

        $thongKeTrangThaiYeuCau = TiepNhanYeuCau::where('idNhom', $idNhom)
            ->selectRaw('trangThai, COUNT(*) as tong')
            ->groupBy('trangThai')
            ->pluck('tong', 'trangThai');

        $soYeuCauTheoChienDich = TiepNhanYeuCau::whereIn('idChienDich', $idChienDichs)
            ->selectRaw('idChienDich, COUNT(*) as tong')
            ->groupBy('idChienDich')
            ->pluck('tong', 'idChienDich');

        $soDongGopDaXacNhanTheoChienDich = DongGop::whereIn('idChienDich', $idChienDichs)
            ->whereHas('chiTietDongGops', function ($query) {
                $query->where('trangThai', 'Đã xác nhận');
            })
            ->selectRaw('idChienDich, COUNT(*) as tong')
            ->groupBy('idChienDich')
            ->pluck('tong', 'idChienDich');

        $soDotPhanPhoiTheoChienDich = DotPhanPhoi::whereIn('idChienDich', $idChienDichs)
            ->selectRaw('idChienDich, COUNT(*) as tong')
            ->groupBy('idChienDich')
            ->pluck('tong', 'idChienDich');

        $soNguonLucTheoChienDich = NguonLucChienDich::whereIn('idChienDich', $idChienDichs)
            ->selectRaw('idChienDich, COUNT(DISTINCT idHangHoa) as tong')
            ->groupBy('idChienDich')
            ->pluck('tong', 'idChienDich');

        $duLieuSoSanhChienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->get(['idChienDich', 'tenChienDich', 'trangThai'])
            ->map(function ($chienDich) use (
                $soYeuCauTheoChienDich,
                $soDongGopDaXacNhanTheoChienDich,
                $soDotPhanPhoiTheoChienDich,
                $soNguonLucTheoChienDich
            ) {
                $idChienDich = $chienDich->idChienDich;

                $soYeuCau = (int) ($soYeuCauTheoChienDich[$idChienDich] ?? 0);
                $soDongGopXacNhan = (int) ($soDongGopDaXacNhanTheoChienDich[$idChienDich] ?? 0);
                $soDotPhanPhoi = (int) ($soDotPhanPhoiTheoChienDich[$idChienDich] ?? 0);
                $soNguonLuc = (int) ($soNguonLucTheoChienDich[$idChienDich] ?? 0);

                return [
                    'idChienDich' => $idChienDich,
                    'maChienDich' => '#' . $idChienDich,
                    'tenChienDich' => $chienDich->tenChienDich ?? ('Chiến dịch #' . $idChienDich),
                    'trangThai' => $chienDich->trangThai ?? '-',
                    'soYeuCau' => $soYeuCau,
                    'soDongGopXacNhan' => $soDongGopXacNhan,
                    'soDotPhanPhoi' => $soDotPhanPhoi,
                    'soNguonLuc' => $soNguonLuc,
                    'tongHoatDong' => $soYeuCau + $soDongGopXacNhan + $soDotPhanPhoi + $soNguonLuc,
                ];
            })
            ->sortByDesc('tongHoatDong')
            ->take(6)
            ->values();

        $duLieuDashboard = [
            'soSanhChienDich' => [
                'labels' => $duLieuSoSanhChienDich->pluck('maChienDich')->toArray(),
                'tenChienDich' => $duLieuSoSanhChienDich->pluck('tenChienDich')->toArray(),
                'trangThai' => $duLieuSoSanhChienDich->pluck('trangThai')->toArray(),
                'yeuCau' => $duLieuSoSanhChienDich->pluck('soYeuCau')->toArray(),
                'dongGop' => $duLieuSoSanhChienDich->pluck('soDongGopXacNhan')->toArray(),
                'phanPhoi' => $duLieuSoSanhChienDich->pluck('soDotPhanPhoi')->toArray(),
                'nguonLuc' => $duLieuSoSanhChienDich->pluck('soNguonLuc')->toArray(),
            ],
            'trangThaiChienDich' => [
                'labels' => $thongKeTrangThaiChienDich->keys()->values()->toArray(),
                'data' => $thongKeTrangThaiChienDich->values()->map(fn ($value) => (int) $value)->toArray(),
            ],
            'trangThaiYeuCau' => [
                'labels' => $thongKeTrangThaiYeuCau->keys()->values()->toArray(),
                'data' => $thongKeTrangThaiYeuCau->values()->map(fn ($value) => (int) $value)->toArray(),
            ],
        ];

        return view('nhom.dashboard', compact(
            'nhom',
            'thanhVien',
            'vaiTroTrongNhom',
            'laNhomTruong',
            'thongKe',
            'duLieuDashboard'
        ));
    }

    public function edit(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhom = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->findOrFail($idNhom);

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->where('vaiTro', '!=', 'Đã rời nhóm')
            ->first();

        if (!$thanhVien) {
            return redirect('/user/nhom-cua-toi')
                ->with('error', 'Bạn không thuộc nhóm tình nguyện này.');
        }

        if (($thanhVien->vaiTro ?? '') !== 'Nhóm trưởng') {
            return redirect('/nhom/' . $idNhom . '/dashboard')
                ->with('error', 'Chỉ nhóm trưởng mới được sửa thông tin nhóm.');
        }

        $diaDiems = DiaDiem::whereNotNull('tinhThanh')
            ->whereNotNull('phuongXa')
            ->orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->orderBy('chiTietDiaDiem')
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
        })->values()->toJson(JSON_UNESCAPED_UNICODE);

        return view('nhom.edit', compact(
            'nhom',
            'thanhVien',
            'diaDiems',
            'diaDiemJson'
        ));
    }

    public function update(Request $request, int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhom = NhomTinhNguyen::with('diaDiem')->findOrFail($idNhom);

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->where('vaiTro', '!=', 'Đã rời nhóm')
            ->first();

        if (!$thanhVien) {
            return redirect('/user/nhom-cua-toi')
                ->with('error', 'Bạn không thuộc nhóm tình nguyện này.');
        }

        if (($thanhVien->vaiTro ?? '') !== 'Nhóm trưởng') {
            return redirect('/nhom/' . $idNhom . '/dashboard')
                ->with('error', 'Chỉ nhóm trưởng mới được cập nhật thông tin nhóm.');
        }

        $request->validate([
            'tenNhom' => ['required', 'string', 'max:255'],
            'moTa' => ['nullable', 'string'],
            'trangThai' => ['required', 'in:Đang hoạt động,Tạm ngừng hoạt động,Ngừng hoạt động'],
            'idDiaDiemCoSan' => ['nullable', 'integer'],
            'tinhThanh' => ['required', 'string', 'max:255'],
            'phuongXa' => ['required', 'string', 'max:255'],
            'chiTietDiaDiem' => ['required', 'string', 'max:255'],
            'viDo' => ['nullable', 'numeric'],
            'kinhDo' => ['nullable', 'numeric'],
            'anhDaiDien' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm.',
            'trangThai.required' => 'Vui lòng chọn trạng thái nhóm.',
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'anhDaiDien.image' => 'Tệp tải lên phải là hình ảnh.',
            'anhDaiDien.max' => 'Ảnh đại diện tối đa 2MB.',
        ]);

        $nhom->tenNhom = $request->tenNhom;
        $nhom->moTa = $request->moTa;
        $nhom->trangThai = $request->trangThai;

        if ($request->hasFile('anhDaiDien')) {
            if ($nhom->anhDaiDien && Storage::disk('public')->exists($nhom->anhDaiDien)) {
                Storage::disk('public')->delete($nhom->anhDaiDien);
            }

            $nhom->anhDaiDien = $request->file('anhDaiDien')
                ->store('nhom-tinh-nguyen', 'public');
        }

        if ($request->filled('idDiaDiemCoSan')) {
            $diaDiemCoSan = DiaDiem::find($request->idDiaDiemCoSan);

            if ($diaDiemCoSan) {
                $nhom->idDiaDiem = $diaDiemCoSan->idDiaDiem;
            }
        } else {
            $diaDiem = DiaDiem::create([
                'tinhThanh' => $request->tinhThanh,
                'phuongXa' => $request->phuongXa,
                'chiTietDiaDiem' => $request->chiTietDiaDiem,
                'viDo' => $request->viDo,
                'kinhDo' => $request->kinhDo,
            ]);

            $nhom->idDiaDiem = $diaDiem->idDiaDiem;
        }

        $nhom->save();

        return redirect('/nhom/' . $idNhom . '/dashboard')
            ->with('success', 'Cập nhật thông tin nhóm thành công.');
    }
}