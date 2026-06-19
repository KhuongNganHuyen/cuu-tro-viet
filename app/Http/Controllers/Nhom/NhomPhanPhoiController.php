<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\ChiTietPhanPhoi;
use App\Models\ChienDichCuuTro;
use App\Models\DiaDiem;
use App\Models\DotPhanPhoi;
use App\Models\NguonLucChienDich;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\TiepNhanYeuCau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhomPhanPhoiController extends Controller
{
    private function kiemTraThanhVien(int $idNhom): array
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')
                    ->with('error', 'Vui lòng đăng nhập để tiếp tục.'),
            ];
        }

        $nhom = NhomTinhNguyen::with(['nhomTruong', 'diaDiem'])
            ->findOrFail($idNhom);

        if ($nhom->trangThai !== 'Đang hoạt động') {
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

        $laNhomTruong =
            $thanhVien->vaiTro === 'Nhóm trưởng'
            || (int) $nhom->idNhomTruong === (int) $idNguoiDung;

        return [
            'hopLe' => true,
            'nhom' => $nhom,
            'thanhVien' => $thanhVien,
            'laNhomTruong' => $laNhomTruong,
        ];
    }

    private function chienDichDaHoanThanh(ChienDichCuuTro $chienDich): bool
    {
        return $chienDich->trangThai === 'Hoàn thành';
    }

    private function layChienDich(int $idNhom, int $idChienDich): ChienDichCuuTro
    {
        return ChienDichCuuTro::with(['suKien', 'diaDiem'])
            ->where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();
    }

    private function coDiaDiemMoi(array $duLieu): bool
    {
        return !empty($duLieu['tinhThanh'])
            && !empty($duLieu['phuongXa'])
            && !empty($duLieu['chiTietDiaDiem'])
            && isset($duLieu['viDo'])
            && isset($duLieu['kinhDo']);
    }

    private function layHoacTaoDiaDiem(array $duLieu): DiaDiem
    {
        if (!empty($duLieu['idDiaDiemCoSan'])) {
            return DiaDiem::findOrFail($duLieu['idDiaDiemCoSan']);
        }

        $tinhThanh = trim($duLieu['tinhThanh'] ?? '');
        $phuongXa = trim($duLieu['phuongXa'] ?? '');
        $chiTietDiaDiem = trim($duLieu['chiTietDiaDiem'] ?? '');

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
            'viDo' => $duLieu['viDo'],
            'kinhDo' => $duLieu['kinhDo'],
        ]);
    }

    public function create(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];

        $chienDich = $this->layChienDich($idNhom, $idChienDich);

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '#phan-phoi')
                ->with('error', 'Chiến dịch đã hoàn thành nên không thể tạo đợt phân phối.');
        }

        $tiepNhanYeuCaus = TiepNhanYeuCau::with([
                'yeuCau.nguoiGui',
                'yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('idNhom', $idNhom)
            ->whereIn('trangThai', [
                'Đã tiếp nhận',
                'Đang hỗ trợ',
                'Cần thêm hỗ trợ',
            ])
            ->orderBy('idTiepNhan', 'desc')
            ->get();

        $nguonLucs = NguonLucChienDich::with([
                'hangHoa.danhMucHang',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('soLuongHienCo', '>', 0)
            ->orderBy('idNguonLuc', 'asc')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
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
        })->values()->toJson();

        return view('nhom.phan_phoi.create', compact(
            'nhom',
            'chienDich',
            'tiepNhanYeuCaus',
            'nguonLucs',
            'diaDiems',
            'diaDiemJson'
        ));
    }

    public function store(Request $request, int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $chienDich = $this->layChienDich($idNhom, $idChienDich);

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '#phan-phoi')
                ->with('error', 'Chiến dịch đã hoàn thành nên không thể tạo đợt phân phối.');
        }

        $request->validate([
            'ngayPhanPhoi' => 'required|date',
            'trangThaiDot' => 'required|string|in:Đang chuẩn bị,Đang phân phối,Hoàn thành',
            'ghiChu' => 'nullable|string',

            'chiTiet' => 'required|array|min:1',
            'chiTiet.*.nguoiNhan' => 'nullable|string|max:255',
            'chiTiet.*.thoiGianGiao' => 'nullable|date',
            'chiTiet.*.loaiPhanPhoi' => 'required|string|in:Địa điểm,Địa điểm và yêu cầu,Yêu cầu',

            'chiTiet.*.idTiepNhan' => 'nullable|integer|exists:TiepNhanYeuCau,idTiepNhan',

            'chiTiet.*.idDiaDiemCoSan' => 'nullable|integer|exists:DiaDiem,idDiaDiem',
            'chiTiet.*.tinhThanh' => 'nullable|string|max:255',
            'chiTiet.*.phuongXa' => 'nullable|string|max:255',
            'chiTiet.*.chiTietDiaDiem' => 'nullable|string|max:255',
            'chiTiet.*.viDo' => 'nullable|numeric',
            'chiTiet.*.kinhDo' => 'nullable|numeric',

            'chiTiet.*.hangHoa' => 'required|array|min:1',
            'chiTiet.*.hangHoa.*.idNguonLuc' => 'required|integer|exists:NguonLucChienDich,idNguonLuc',
            'chiTiet.*.hangHoa.*.soLuongGiao' => 'required|numeric|min:0.01',
        ], [
            'ngayPhanPhoi.required' => 'Vui lòng chọn ngày phân phối.',
            'chiTiet.required' => 'Vui lòng thêm ít nhất một chi tiết phân phối.',
            'chiTiet.*.loaiPhanPhoi.required' => 'Vui lòng chọn loại phân phối.',
            'chiTiet.*.loaiPhanPhoi.in' => 'Loại phân phối không hợp lệ.',
            'chiTiet.*.hangHoa.required' => 'Vui lòng thêm hàng hóa phân phối.',
            'chiTiet.*.hangHoa.*.idNguonLuc.required' => 'Vui lòng chọn nguồn lực.',
            'chiTiet.*.hangHoa.*.soLuongGiao.required' => 'Vui lòng nhập số lượng giao.',
            'chiTiet.*.hangHoa.*.soLuongGiao.min' => 'Số lượng giao phải lớn hơn 0.',
        ]);

        $chiTiets = collect($request->input('chiTiet', []))->values();

        $ngayBatDauDot = \Carbon\Carbon::parse($request->ngayPhanPhoi);
        $hienTai = now();

        $trangThaiDot = $request->trangThaiDot;

        if ($hienTai->lt($ngayBatDauDot)) {
            $trangThaiDotHopLe = [
                'Đang chuẩn bị',
                'Đang phân phối',
            ];
        } else {
            $trangThaiDotHopLe = [
                'Đang phân phối',
                'Hoàn thành',
            ];
        }

        if (!in_array($trangThaiDot, $trangThaiDotHopLe, true)) {
            return back()
                ->withInput()
                ->with('error', 'Trạng thái đợt phân phối không phù hợp với ngày bắt đầu.');
        }

        foreach ($chiTiets as $index => $chiTiet) {
            $loaiPhanPhoi = $chiTiet['loaiPhanPhoi'] ?? null;

            $coDiaDiemCoSan = !empty($chiTiet['idDiaDiemCoSan']);
            $coDiaDiemMoi = $this->coDiaDiemMoi($chiTiet);

            if ($loaiPhanPhoi === 'Địa điểm') {
                if (!$coDiaDiemCoSan && !$coDiaDiemMoi) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn hoặc nhập địa điểm phân phối.');
                }
            }

            if ($loaiPhanPhoi === 'Địa điểm và yêu cầu') {
                if (!$coDiaDiemCoSan && !$coDiaDiemMoi) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn hoặc nhập địa điểm phân phối.');
                }

                if (empty($chiTiet['idTiepNhan'])) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn yêu cầu cứu trợ đi kèm.');
                }
            }

            if ($loaiPhanPhoi === 'Yêu cầu') {
                if (empty($chiTiet['idTiepNhan'])) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn yêu cầu cứu trợ.');
                }
            }
        }

        try {
            DB::transaction(function () use (
                $request,
                $idNhom,
                $idChienDich,
                $chienDich,
                $chiTiets
            ) {
                $dotPhanPhoi = DotPhanPhoi::create([
                    'idChienDich' => $chienDich->idChienDich,
                    'ngayPhanPhoi' => $request->ngayPhanPhoi,
                    'trangThai' => $request->trangThaiDot,
                    'ghiChu' => $request->ghiChu,
                ]);

                foreach ($chiTiets as $chiTiet) {
                    $loaiPhanPhoi = $chiTiet['loaiPhanPhoi'];

                    $idTiepNhan = in_array($loaiPhanPhoi, ['Địa điểm và yêu cầu', 'Yêu cầu'], true)
                        ? ($chiTiet['idTiepNhan'] ?? null)
                        : null;

                    $idDiaDiem = null;

                    if (in_array($loaiPhanPhoi, ['Địa điểm', 'Địa điểm và yêu cầu'], true)) {
                        $diaDiem = $this->layHoacTaoDiaDiem($chiTiet);
                        $idDiaDiem = $diaDiem->idDiaDiem;
                    }

                    if ($loaiPhanPhoi === 'Yêu cầu') {
                        $idDiaDiem = null;
                    }

                    if ($idTiepNhan) {
                        $tiepNhanHopLe = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
                            ->where('idChienDich', $idChienDich)
                            ->where('idNhom', $idNhom)
                            ->exists();

                        if (!$tiepNhanHopLe) {
                            throw new \Exception('Có yêu cầu cứu trợ không thuộc chiến dịch hoặc không thuộc nhóm.');
                        }
                    }

                    foreach ($chiTiet['hangHoa'] as $hangHoaPhanPhoi) {
                        $nguonLuc = NguonLucChienDich::with('hangHoa')
                            ->where('idChienDich', $chienDich->idChienDich)
                            ->where('idNguonLuc', $hangHoaPhanPhoi['idNguonLuc'])
                            ->lockForUpdate()
                            ->firstOrFail();

                        $soLuongGiao = (float) $hangHoaPhanPhoi['soLuongGiao'];

                        if ($soLuongGiao > (float) $nguonLuc->soLuongHienCo) {
                            throw new \Exception(
                                'Số lượng giao không được lớn hơn số lượng hiện có của nguồn lực: '
                                . ($nguonLuc->hangHoa->tenHangHoa ?? '#' . $nguonLuc->idNguonLuc)
                            );
                        }

                        $trangThaiChiTiet = $request->trangThaiDot === 'Hoàn thành'
                            ? 'Đã giao'
                            : 'Chưa giao';

                        $thoiGianGiao = $request->trangThaiDot === 'Hoàn thành'
                            ? ($chiTiet['thoiGianGiao'] ?? $request->ngayPhanPhoi)
                            : ($chiTiet['thoiGianGiao'] ?? null);

                        ChiTietPhanPhoi::create([
                            'idDotPhanPhoi' => $dotPhanPhoi->idDotPhanPhoi,
                            'idNguonLuc' => $nguonLuc->idNguonLuc,
                            'idDiaDiem' => $idDiaDiem,
                            'idTiepNhan' => $idTiepNhan,
                            'loaiPhanPhoi' => $loaiPhanPhoi,
                            'nguoiNhan' => $chiTiet['nguoiNhan'] ?? null,
                            'soLuongGiao' => $soLuongGiao,
                            'thoiGianGiao' => $thoiGianGiao,
                            'trangThai' => $trangThaiChiTiet,
                        ]);

                        $nguonLuc->update([
                            'soLuongHienCo' => (float) $nguonLuc->soLuongHienCo - $soLuongGiao,
                            'ngayCapNhat' => now(),
                        ]);
                    }

                    if ($idTiepNhan) {
                        $tiepNhan = TiepNhanYeuCau::with('yeuCau')
                            ->where('idTiepNhan', $idTiepNhan)
                            ->first();

                        if ($tiepNhan && $tiepNhan->trangThai !== 'Hoàn thành') {
                            $tiepNhan->update([
                                'trangThai' => 'Đang hỗ trợ',
                            ]);
                        }

                        if ($tiepNhan?->yeuCau && $tiepNhan->yeuCau->trangThai !== 'Hoàn thành') {
                            $tiepNhan->yeuCau->update([
                                'trangThai' => 'Đang hỗ trợ',
                            ]);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '#phan-phoi')
            ->with('success', 'Tạo đợt phân phối thành công.');
    }

    public function show(int $idNhom, int $idChienDich, int $idDotPhanPhoi)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];

        $chienDich = $this->layChienDich($idNhom, $idChienDich);

        $dotPhanPhoi = DotPhanPhoi::with([
                'chiTietPhanPhois.nguonLuc.hangHoa.danhMucHang',
                'chiTietPhanPhois.diaDiem',
                'chiTietPhanPhois.tiepNhan.yeuCau.nguoiGui',
                'chiTietPhanPhois.tiepNhan.yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('idDotPhanPhoi', $idDotPhanPhoi)
            ->firstOrFail();

        return view('nhom.phan_phoi.show', compact(
            'nhom',
            'chienDich',
            'dotPhanPhoi'
        ));
    }

    public function edit(int $idNhom, int $idChienDich, int $idDotPhanPhoi)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];

        $chienDich = $this->layChienDich($idNhom, $idChienDich);

        $dotPhanPhoi = DotPhanPhoi::with([
                'chiTietPhanPhois.nguonLuc.hangHoa.danhMucHang',
                'chiTietPhanPhois.diaDiem',
                'chiTietPhanPhois.tiepNhan.yeuCau.nguoiGui',
                'chiTietPhanPhois.tiepNhan.yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('idDotPhanPhoi', $idDotPhanPhoi)
            ->firstOrFail();

        if (in_array($dotPhanPhoi->trangThai, ['Hoàn thành', 'Đã hủy'], true)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '/phan-phoi/' . $idDotPhanPhoi)
                ->with('error', 'Không thể sửa đợt phân phối đã hoàn thành hoặc đã hủy.');
        }

        $tiepNhanYeuCaus = TiepNhanYeuCau::with([
                'yeuCau.nguoiGui',
                'yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('idNhom', $idNhom)
            ->whereIn('trangThai', [
                'Đã tiếp nhận',
                'Đang hỗ trợ',
                'Cần thêm hỗ trợ',
                'Hoàn thành',
            ])
            ->orderBy('idTiepNhan', 'desc')
            ->get();

        $nguonLucs = NguonLucChienDich::with([
                'hangHoa.danhMucHang',
            ])
            ->where('idChienDich', $idChienDich)
            ->orderBy('idNguonLuc', 'asc')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')
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
        })->values()->toJson();

        return view('nhom.phan_phoi.edit', compact(
            'nhom',
            'chienDich',
            'dotPhanPhoi',
            'tiepNhanYeuCaus',
            'nguonLucs',
            'diaDiems',
            'diaDiemJson'
        ));
    }

    public function update(Request $request, int $idNhom, int $idChienDich, int $idDotPhanPhoi)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $chienDich = $this->layChienDich($idNhom, $idChienDich);

        $dotPhanPhoi = DotPhanPhoi::with(['chiTietPhanPhois'])
            ->where('idChienDich', $idChienDich)
            ->where('idDotPhanPhoi', $idDotPhanPhoi)
            ->firstOrFail();

        if (in_array($dotPhanPhoi->trangThai, ['Hoàn thành', 'Đã hủy'], true)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '/phan-phoi/' . $idDotPhanPhoi)
                ->with('error', 'Không thể cập nhật đợt phân phối đã hoàn thành hoặc đã hủy.');
        }

        $request->validate([
            'ngayPhanPhoi' => 'required|date',
            'trangThaiDot' => 'required|string|in:Đang chuẩn bị,Đang phân phối,Hoàn thành,Đã hủy',
            'ghiChu' => 'nullable|string',

            'chiTiet' => 'required|array|min:1',
            'chiTiet.*.nguoiNhan' => 'nullable|string|max:255',
            'chiTiet.*.thoiGianGiao' => 'nullable|date',
            'chiTiet.*.loaiPhanPhoi' => 'required|string|in:Địa điểm,Địa điểm và yêu cầu,Yêu cầu',

            'chiTiet.*.idTiepNhan' => 'nullable|integer|exists:TiepNhanYeuCau,idTiepNhan',

            'chiTiet.*.idDiaDiemCoSan' => 'nullable|integer|exists:DiaDiem,idDiaDiem',
            'chiTiet.*.tinhThanh' => 'nullable|string|max:255',
            'chiTiet.*.phuongXa' => 'nullable|string|max:255',
            'chiTiet.*.chiTietDiaDiem' => 'nullable|string|max:255',
            'chiTiet.*.viDo' => 'nullable|numeric',
            'chiTiet.*.kinhDo' => 'nullable|numeric',

            'chiTiet.*.hangHoa' => 'required|array|min:1',
            'chiTiet.*.hangHoa.*.idChiTietPhanPhoi' => 'nullable|integer|exists:ChiTietPhanPhoi,idChiTietPhanPhoi',
            'chiTiet.*.hangHoa.*.idNguonLuc' => 'required|integer|exists:NguonLucChienDich,idNguonLuc',
            'chiTiet.*.hangHoa.*.soLuongGiao' => 'required|numeric|min:0.01',
            'chiTiet.*.hangHoa.*.trangThai' => 'required|string|in:Chưa giao,Đã giao,Không giao được,Đã hủy',
        ]);

        $ngayBatDauDot = \Carbon\Carbon::parse($request->ngayPhanPhoi);

        if ($request->trangThaiDot === 'Đang chuẩn bị' && now()->gte($ngayBatDauDot)) {
            return back()
                ->withInput()
                ->with('error', 'Chỉ được chọn trạng thái Đang chuẩn bị khi ngày bắt đầu lớn hơn ngày hiện tại.');
        }

        $chiTiets = collect($request->input('chiTiet', []))->values();

        foreach ($chiTiets as $index => $chiTiet) {
            $loaiPhanPhoi = $chiTiet['loaiPhanPhoi'] ?? null;

            $coDiaDiemCoSan = !empty($chiTiet['idDiaDiemCoSan']);
            $coDiaDiemMoi = $this->coDiaDiemMoi($chiTiet);

            if ($loaiPhanPhoi === 'Địa điểm') {
                if (!$coDiaDiemCoSan && !$coDiaDiemMoi) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn hoặc nhập địa điểm phân phối.');
                }
            }

            if ($loaiPhanPhoi === 'Địa điểm và yêu cầu') {
                if (!$coDiaDiemCoSan && !$coDiaDiemMoi) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn hoặc nhập địa điểm phân phối.');
                }

                if (empty($chiTiet['idTiepNhan'])) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn yêu cầu cứu trợ đi kèm.');
                }
            }

            if ($loaiPhanPhoi === 'Yêu cầu') {
                if (empty($chiTiet['idTiepNhan'])) {
                    return back()
                        ->withInput()
                        ->with('error', 'Chi tiết #' . ($index + 1) . ': Vui lòng chọn yêu cầu cứu trợ.');
                }
            }
        }

        try {
            DB::transaction(function () use (
                $request,
                $idNhom,
                $idChienDich,
                $chienDich,
                $dotPhanPhoi,
                $chiTiets
            ) {
                $dotPhanPhoi->update([
                    'ngayPhanPhoi' => $request->ngayPhanPhoi,
                    'trangThai' => $request->trangThaiDot,
                    'ghiChu' => $request->ghiChu,
                ]);

                $chiTietCuTheoId = $dotPhanPhoi->chiTietPhanPhois->keyBy('idChiTietPhanPhoi');
                $idChiTietDaXuLy = [];

                foreach ($chiTiets as $chiTiet) {
                    $loaiPhanPhoi = $chiTiet['loaiPhanPhoi'];

                    $idTiepNhan = in_array($loaiPhanPhoi, ['Địa điểm và yêu cầu', 'Yêu cầu'], true)
                        ? ($chiTiet['idTiepNhan'] ?? null)
                        : null;

                    $idDiaDiem = null;

                    if (in_array($loaiPhanPhoi, ['Địa điểm', 'Địa điểm và yêu cầu'], true)) {
                        $diaDiem = $this->layHoacTaoDiaDiem($chiTiet);
                        $idDiaDiem = $diaDiem->idDiaDiem;
                    }

                    if ($loaiPhanPhoi === 'Yêu cầu') {
                        $idDiaDiem = null;
                    }

                    if ($idTiepNhan) {
                        $tiepNhanHopLe = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
                            ->where('idChienDich', $idChienDich)
                            ->where('idNhom', $idNhom)
                            ->exists();

                        if (!$tiepNhanHopLe) {
                            throw new \Exception('Có yêu cầu cứu trợ không thuộc chiến dịch hoặc không thuộc nhóm.');
                        }
                    }

                    foreach ($chiTiet['hangHoa'] as $hangHoaPhanPhoi) {
                        $idChiTietPhanPhoi = $hangHoaPhanPhoi['idChiTietPhanPhoi'] ?? null;
                        $chiTietCu = null;

                        if ($idChiTietPhanPhoi) {
                            $chiTietCu = $chiTietCuTheoId->get((int) $idChiTietPhanPhoi);

                            if (!$chiTietCu) {
                                throw new \Exception('Có chi tiết phân phối không thuộc đợt phân phối này.');
                            }
                        }

                        $trangThaiMoi = $hangHoaPhanPhoi['trangThai'];
                        $trangThaiCu = $chiTietCu->trangThai ?? null;

                        if ($request->trangThaiDot === 'Hoàn thành') {
                            $trangThaiMoi = $trangThaiCu === 'Chưa giao' || !$trangThaiCu
                                ? 'Đã giao'
                                : $trangThaiCu;
                        }

                        if ($request->trangThaiDot === 'Đã hủy') {
                            $trangThaiMoi = $trangThaiCu === 'Chưa giao' || !$trangThaiCu
                                ? 'Đã hủy'
                                : $trangThaiCu;
                        }

                        $soLuongGiaoMoi = (float) $hangHoaPhanPhoi['soLuongGiao'];
                        $idNguonLucMoi = (int) $hangHoaPhanPhoi['idNguonLuc'];

                        if ($chiTietCu) {
                            $nguonLucCu = NguonLucChienDich::where('idNguonLuc', $chiTietCu->idNguonLuc)
                                ->lockForUpdate()
                                ->firstOrFail();

                            if ($chiTietCu->trangThai !== 'Đã hủy') {
                                $nguonLucCu->update([
                                    'soLuongHienCo' => (float) $nguonLucCu->soLuongHienCo + (float) $chiTietCu->soLuongGiao,
                                    'ngayCapNhat' => now(),
                                ]);
                            }
                        }

                        $nguonLucMoi = NguonLucChienDich::with('hangHoa')
                            ->where('idChienDich', $chienDich->idChienDich)
                            ->where('idNguonLuc', $idNguonLucMoi)
                            ->lockForUpdate()
                            ->firstOrFail();

                        if ($trangThaiMoi !== 'Đã hủy') {
                            if ($soLuongGiaoMoi > (float) $nguonLucMoi->soLuongHienCo) {
                                throw new \Exception(
                                    'Số lượng giao không được lớn hơn số lượng hiện có của nguồn lực: '
                                    . ($nguonLucMoi->hangHoa->tenHangHoa ?? '#' . $nguonLucMoi->idNguonLuc)
                                );
                            }

                            $nguonLucMoi->update([
                                'soLuongHienCo' => (float) $nguonLucMoi->soLuongHienCo - $soLuongGiaoMoi,
                                'ngayCapNhat' => now(),
                            ]);
                        }

                        $thoiGianGiao = $chiTiet['thoiGianGiao'] ?? null;

                        if ($trangThaiMoi === 'Đã giao' && !$thoiGianGiao) {
                            $thoiGianGiao = now();
                        }

                        if ($chiTietCu) {
                            $chiTietCu->update([
                                'idNguonLuc' => $nguonLucMoi->idNguonLuc,
                                'idDiaDiem' => $idDiaDiem,
                                'idTiepNhan' => $idTiepNhan,
                                'loaiPhanPhoi' => $loaiPhanPhoi,
                                'nguoiNhan' => $chiTiet['nguoiNhan'] ?? null,
                                'soLuongGiao' => $soLuongGiaoMoi,
                                'thoiGianGiao' => $thoiGianGiao,
                                'trangThai' => $trangThaiMoi,
                            ]);

                            $idChiTietDaXuLy[] = (int) $chiTietCu->idChiTietPhanPhoi;
                        } else {
                            $chiTietMoi = ChiTietPhanPhoi::create([
                                'idDotPhanPhoi' => $dotPhanPhoi->idDotPhanPhoi,
                                'idNguonLuc' => $nguonLucMoi->idNguonLuc,
                                'idDiaDiem' => $idDiaDiem,
                                'idTiepNhan' => $idTiepNhan,
                                'loaiPhanPhoi' => $loaiPhanPhoi,
                                'nguoiNhan' => $chiTiet['nguoiNhan'] ?? null,
                                'soLuongGiao' => $soLuongGiaoMoi,
                                'thoiGianGiao' => $thoiGianGiao,
                                'trangThai' => $trangThaiMoi,
                            ]);

                            $idChiTietDaXuLy[] = (int) $chiTietMoi->idChiTietPhanPhoi;
                        }
                    }

                    if ($idTiepNhan) {
                        $tiepNhan = TiepNhanYeuCau::with('yeuCau')
                            ->where('idTiepNhan', $idTiepNhan)
                            ->first();

                        if ($tiepNhan && $tiepNhan->trangThai !== 'Hoàn thành') {
                            $tiepNhan->update([
                                'trangThai' => 'Đang hỗ trợ',
                            ]);
                        }

                        if ($tiepNhan?->yeuCau && $tiepNhan->yeuCau->trangThai !== 'Hoàn thành') {
                            $tiepNhan->yeuCau->update([
                                'trangThai' => 'Đang hỗ trợ',
                            ]);
                        }
                    }
                }

                foreach ($chiTietCuTheoId as $chiTietCu) {
                    if (in_array((int) $chiTietCu->idChiTietPhanPhoi, $idChiTietDaXuLy, true)) {
                        continue;
                    }

                    $nguonLuc = NguonLucChienDich::where('idNguonLuc', $chiTietCu->idNguonLuc)
                        ->lockForUpdate()
                        ->first();

                    if ($nguonLuc && $chiTietCu->trangThai !== 'Đã hủy') {
                        $nguonLuc->update([
                            'soLuongHienCo' => (float) $nguonLuc->soLuongHienCo + (float) $chiTietCu->soLuongGiao,
                            'ngayCapNhat' => now(),
                        ]);
                    }

                    $chiTietCu->delete();
                }
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '/phan-phoi/' . $idDotPhanPhoi)
            ->with('success', 'Cập nhật đợt phân phối thành công.');
    }

    public function destroy(int $idNhom, int $idChienDich, int $idDotPhanPhoi)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $dotPhanPhoi = DotPhanPhoi::with(['chiTietPhanPhois'])
            ->where('idChienDich', $idChienDich)
            ->where('idDotPhanPhoi', $idDotPhanPhoi)
            ->firstOrFail();

        if (in_array($dotPhanPhoi->trangThai, ['Hoàn thành', 'Đã hủy'], true)) {
            return back()
                ->with('error', 'Không thể xóa đợt phân phối đã hoàn thành hoặc đã hủy.');
        }

        try {
            DB::transaction(function () use ($dotPhanPhoi) {
                foreach ($dotPhanPhoi->chiTietPhanPhois as $chiTiet) {
                    $nguonLuc = NguonLucChienDich::where('idNguonLuc', $chiTiet->idNguonLuc)
                        ->lockForUpdate()
                        ->first();

                    if ($nguonLuc && $chiTiet->trangThai !== 'Đã hủy') {
                        $nguonLuc->update([
                            'soLuongHienCo' => (float) $nguonLuc->soLuongHienCo + (float) $chiTiet->soLuongGiao,
                            'ngayCapNhat' => now(),
                        ]);
                    }

                    $chiTiet->delete();
                }

                $dotPhanPhoi->delete();
            });
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '#phan-phoi')
            ->with('success', 'Xóa đợt phân phối thành công.');
    }
}