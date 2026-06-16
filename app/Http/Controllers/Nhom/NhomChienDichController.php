<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\ChienDichCuuTro;
use App\Models\SuKienCuuTro;
use App\Models\DiaDiem;
use App\Models\CapNhatChienDich;
use App\Models\DongGop;
use App\Models\ChiTietDongGop;
use App\Models\HangHoa;
use App\Models\NguonLucChienDich;
use App\Models\TiepNhanYeuCau;
use App\Models\DotPhanPhoi;
use App\Models\ChiTietPhanPhoi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhomChienDichController extends Controller
{
    private function kiemTraThanhVien(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')
                    ->with('error', 'Vui lòng đăng nhập để tiếp tục.'),
            ];
        }

        $nhom = NhomTinhNguyen::with([
                'nhomTruong',
                'diaDiem',
            ])
            ->findOrFail($idNhom);

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

    private function ngayHomNay(): Carbon
    {
        return Carbon::today();
    }

    private function chuyenThanhNgay(?string $ngay): ?Carbon
    {
        if (!$ngay) {
            return null;
        }

        return Carbon::parse($ngay)->startOfDay();
    }

    private function chuanHoaTrangThai(?string $trangThai): ?string
    {
        if ($trangThai === 'Đang hoạt động') {
            return 'Đang diễn ra';
        }

        return $trangThai;
    }

    private function layTrangThaiMacDinhKhiTao(
        ?string $ngayBatDau,
        ?string $ngayKetThuc
    ): string {
        $homNay = $this->ngayHomNay();
        $batDau = $this->chuyenThanhNgay($ngayBatDau);

        if ($batDau && $homNay->lt($batDau)) {
            return 'Sắp diễn ra';
        }

        return 'Đang diễn ra';
    }

    private function layTrangThaiDuocPhepKhiTao(
        ?string $ngayBatDau,
        ?string $ngayKetThuc
    ): array {
        $homNay = $this->ngayHomNay();
        $batDau = $this->chuyenThanhNgay($ngayBatDau);
        $ketThuc = $this->chuyenThanhNgay($ngayKetThuc);

        /*
         * Tạo trước ngày bắt đầu:
         * - Mặc định Sắp diễn ra.
         * - Cho phép nhóm mở kêu gọi sớm bằng Đang diễn ra.
         */
        if ($batDau && $homNay->lt($batDau)) {
            return [
                'Sắp diễn ra',
                'Đang diễn ra',
            ];
        }

        /*
         * Tạo sau ngày kết thúc:
         * - Cho phép nhập lại chiến dịch thực tế.
         * - Có thể chọn Hoàn thành nếu chiến dịch đã xong.
         */
        if ($ketThuc && $homNay->gt($ketThuc)) {
            return [
                'Đang diễn ra',
                'Hoàn thành',
            ];
        }

        /*
         * Tạo trong khoảng thời gian triển khai hoặc không nhập ngày:
         * - Chỉ cho Đang diễn ra.
         */
        return [
            'Đang diễn ra',
        ];
    }

    private function layTrangThaiDuocPhepKhiSua(?string $ngayBatDau, ?string $ngayKetThuc): array
    {
        $homNay = now()->startOfDay();

        $batDau = $ngayBatDau
            ? \Carbon\Carbon::parse($ngayBatDau)->startOfDay()
            : null;

        if ($batDau && $homNay->lt($batDau)) {
            return [
                'Sắp diễn ra',
                'Đang diễn ra',
                'Tạm ngưng',
            ];
        }

        return [
            'Đang diễn ra',
            'Tạm ngưng',
            'Hoàn thành',
        ];
    }

    private function chienDichDaHoanThanh(ChienDichCuuTro $chienDich): bool
    {
        return $chienDich->trangThai === 'Hoàn thành';
    }

    public function index(Request $request, int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $chienDichs = ChienDichCuuTro::with([
                'suKien',
                'diaDiem',
            ])
            ->where('idNhom', $idNhom)
            ->orderBy('idChienDich', 'desc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaThuong = mb_strtolower(
                $tuKhoa,
                'UTF-8'
            );

            $tuKhoaKhongDau = $this->boDauTiengViet(
                $tuKhoa
            );

            $chienDichs = $chienDichs
                ->filter(function ($chienDich) use (
                    $tuKhoaThuong,
                    $tuKhoaKhongDau
                ) {
                    $diaDiem = $chienDich->diaDiem;

                    $diaChi = implode(' ', [
                        $diaDiem->chiTietDiaDiem ?? '',
                        $diaDiem->phuongXa ?? '',
                        $diaDiem->tinhThanh ?? '',
                    ]);

                    $xacNhan = $chienDich->daXacNhanCuuTro
                        ? 'Đã xác nhận'
                        : 'Chưa xác nhận';

                    $noiDungTimKiem = implode(' ', [
                        $chienDich->idChienDich,
                        $chienDich->tenChienDich,
                        $chienDich->moTa,
                        $chienDich->ngayTao,
                        $chienDich->ngayBatDau,
                        $chienDich->ngayKetThuc,
                        $chienDich->ghiChuXacNhan,
                        $chienDich->trangThai,
                        $xacNhan,
                        $chienDich->suKien->tenSuKien ?? '',
                        $chienDich->suKien->loaiSuKien ?? '',
                        $diaChi,
                    ]);

                    $noiDungThuong = mb_strtolower(
                        $noiDungTimKiem,
                        'UTF-8'
                    );

                    $noiDungKhongDau = $this->boDauTiengViet(
                        $noiDungTimKiem
                    );

                    return str_contains(
                        $noiDungThuong,
                        $tuKhoaThuong
                    ) || str_contains(
                        $noiDungKhongDau,
                        $tuKhoaKhongDau
                    );
                })
                ->values();
        }

        $chienDichs = $chienDichs
            ->sortByDesc(function ($chienDich) {
                return (int) $chienDich->idChienDich;
            })
            ->values();

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

        $suKiens = SuKienCuuTro::where('trangThai', '!=', 'Ẩn')
            ->orderByRaw("
                CASE
                    WHEN loaiSuKien = 'Khẩn cấp' THEN 0
                    WHEN loaiSuKien = 'Thường nhật' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('idSuKien', 'desc')
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

        $hangHoas = HangHoa::with('danhMucHang')
            ->where('trangThai', 'Đang sử dụng')
            ->where(function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom)
                    ->orWhereNull('idNhom');
            })
            ->orderByRaw(
                'CASE WHEN idNhom = ? THEN 0 ELSE 1 END',
                [$idNhom]
            )
            ->orderBy('idHangHoa', 'asc')
            ->get();

        return view('nhom.chien_dich.create', compact(
            'nhom',
            'suKiens',
            'diaDiems',
            'diaDiemJson',
            'hangHoas'
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
            'idSuKien' => 'required|integer|exists:SuKienCuuTro,idSuKien',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daXacNhanCuuTro' => 'nullable|boolean',
            'ghiChuXacNhan' => 'nullable|string',
            'trangThai' => 'nullable|string|max:255',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',
        ], [
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'idSuKien.required' => 'Vui lòng chọn sự kiện cứu trợ.',
            'idSuKien.exists' => 'Sự kiện cứu trợ không hợp lệ.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',

            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
        ]);

        $suKienHopLe = SuKienCuuTro::where('idSuKien', $request->idSuKien)
            ->where('trangThai', '!=', 'Ẩn')
            ->exists();

        if (!$suKienHopLe) {
            return back()
                ->withInput()
                ->with('error', 'Sự kiện cứu trợ không hợp lệ hoặc đã bị ẩn.');
        }

        $trangThaiMacDinh = $this->layTrangThaiMacDinhKhiTao(
            $request->ngayBatDau,
            $request->ngayKetThuc
        );

        $trangThai = $this->chuanHoaTrangThai(
            $request->filled('trangThai')
                ? $request->trangThai
                : $trangThaiMacDinh
        );

        $trangThaiDuocPhep = $this->layTrangThaiDuocPhepKhiTao(
            $request->ngayBatDau,
            $request->ngayKetThuc
        );

        if (!in_array($trangThai, $trangThaiDuocPhep, true)) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Trạng thái chiến dịch không phù hợp với thời gian bắt đầu và kết thúc.'
                );
        }

        $tinhThanh = trim($request->tinhThanh);
        $phuongXa = trim($request->phuongXa);
        $chiTietDiaDiem = trim($request->chiTietDiaDiem);

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

        $nguonLucInput = $request->input('nguonLuc', []);

        $nguonLucDuocChon = collect($nguonLucInput)
            ->filter(function ($duLieu) {
                return !empty($duLieu['chon']);
            });

        if ($nguonLucDuocChon->isEmpty()) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Vui lòng chọn ít nhất một mặt hàng cần kêu gọi cho chiến dịch.'
                );
        }

        $idHangHoaDuocChon = $nguonLucDuocChon
            ->keys()
            ->map(function ($idHangHoa) {
                return (int) $idHangHoa;
            })
            ->values();

        $idHangHoaHopLes = HangHoa::whereIn(
                'idHangHoa',
                $idHangHoaDuocChon
            )
            ->where('trangThai', 'Đang sử dụng')
            ->where(function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom)
                    ->orWhereNull('idNhom');
            })
            ->pluck('idHangHoa')
            ->map(function ($idHangHoa) {
                return (int) $idHangHoa;
            })
            ->toArray();

        foreach ($nguonLucDuocChon as $idHangHoa => $duLieu) {
            $idHangHoa = (int) $idHangHoa;

            if (!in_array($idHangHoa, $idHangHoaHopLes, true)) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Có mặt hàng không hợp lệ hoặc không thuộc phạm vi sử dụng của nhóm.'
                    );
            }

            $soLuongCanKeuGoi =
                $duLieu['soLuongCanKeuGoi'] ?? null;

            if (
                !is_numeric($soLuongCanKeuGoi)
                || (float) $soLuongCanKeuGoi <= 0
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Vui lòng nhập số lượng cần kêu gọi lớn hơn 0 cho các mặt hàng đã chọn.'
                    );
            }
        }

        DB::transaction(function () use (
            $request,
            $idNhom,
            $diaDiem,
            $nguonLucDuocChon,
            $trangThai
        ) {
            $chienDich = ChienDichCuuTro::create([
                'idNhom' => $idNhom,
                'idSuKien' => $request->idSuKien,
                'idDiaDiem' => $diaDiem->idDiaDiem,
                'tenChienDich' => $request->tenChienDich,
                'moTa' => $request->moTa,
                'ngayTao' => now(),
                'ngayBatDau' => $request->ngayBatDau,
                'ngayKetThuc' => $request->ngayKetThuc,
                'daXacNhanCuuTro' => $request->has('daXacNhanCuuTro') ? 1 : 0,
                'ghiChuXacNhan' => $request->ghiChuXacNhan,
                'trangThai' => $trangThai,
            ]);

            foreach ($nguonLucDuocChon as $idHangHoa => $duLieu) {
                NguonLucChienDich::create([
                    'idChienDich' => $chienDich->idChienDich,
                    'idHangHoa' => (int) $idHangHoa,
                    'soLuongCanKeuGoi' => (float) $duLieu['soLuongCanKeuGoi'],
                    'soLuongDaNhan' => 0,
                    'soLuongHienCo' => 0,
                    'hanSuDung' => null,
                    'trangThai' => 'Đang kêu gọi',
                    'ngayCapNhat' => now(),
                ]);
            }
        });

        return redirect('/nhom/' . $idNhom . '/chien-dich')
            ->with(
                'success',
                'Thêm chiến dịch cứu trợ thành công.'
            );
    }

    public function show(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $chienDich = ChienDichCuuTro::with([
                'suKien',
                'diaDiem',
                'nhom',
            ])
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
                'chiTietDongGops.hangHoa',
            ])
            ->where('idChienDich', $idChienDich)
            ->orderBy('idDongGop', 'desc')
            ->get();

        $nguonLucs = NguonLucChienDich::with('hangHoa.danhMucHang')
            ->where('idChienDich', $idChienDich)
            ->orderBy('idNguonLuc', 'asc')
            ->get();

        $tiepNhanYeuCaus = TiepNhanYeuCau::with([
                'yeuCau.nguoiGui',
                'yeuCau.diaDiem',
                'nhom',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('idNhom', $idNhom)
            ->orderBy('idTiepNhan', 'desc')
            ->get();

        $dotPhanPhois = DotPhanPhoi::with([
                'chiTietPhanPhois.nguonLuc.hangHoa',
                'chiTietPhanPhois.diaDiem',
                'chiTietPhanPhois.tiepNhan.yeuCau.nguoiGui',
                'chiTietPhanPhois.tiepNhan.yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->orderBy('idDotPhanPhoi', 'desc')
            ->get();

        return view('nhom.chien_dich.show', compact(
            'nhom',
            'chienDich',
            'laNhomTruong',
            'capNhats',
            'dongGops',
            'nguonLucs',
            'tiepNhanYeuCaus',
            'dotPhanPhois'
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

        $chienDich = ChienDichCuuTro::with([
                'suKien',
                'diaDiem',
            ])
            ->where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể chỉnh sửa.'
                );
        }

        $suKiens = SuKienCuuTro::where('trangThai', '!=', 'Ẩn')
            ->orderByRaw("
                CASE
                    WHEN loaiSuKien = 'Khẩn cấp' THEN 0
                    WHEN loaiSuKien = 'Thường nhật' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('idSuKien', 'desc')
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

        $trangThaiDuocPhep = $this->layTrangThaiDuocPhepKhiSua(
            $chienDich->ngayBatDau,
            $chienDich->ngayKetThuc
        );

        return view('nhom.chien_dich.edit', compact(
            'nhom',
            'chienDich',
            'suKiens',
            'diaDiems',
            'diaDiemJson',
            'trangThaiDuocPhep'
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

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể chỉnh sửa.'
                );
        }

        $request->validate([
            'tenChienDich' => 'required|string|max:255',
            'idSuKien' => 'required|integer|exists:SuKienCuuTro,idSuKien',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daXacNhanCuuTro' => 'nullable|boolean',
            'ghiChuXacNhan' => 'nullable|string',
            'trangThai' => 'required|string|max:255',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',
        ], [
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'idSuKien.required' => 'Vui lòng chọn sự kiện cứu trợ.',
            'idSuKien.exists' => 'Sự kiện cứu trợ không hợp lệ.',
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

        $trangThai = $this->chuanHoaTrangThai(
            $request->trangThai
        );

        $trangThaiDuocPhep = $this->layTrangThaiDuocPhepKhiSua(
            $request->ngayBatDau,
            $request->ngayKetThuc
        );

        if (!in_array($trangThai, $trangThaiDuocPhep, true)) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Trạng thái chiến dịch không phù hợp với thời gian bắt đầu và kết thúc.'
                );
        }

        $tinhThanh = trim($request->tinhThanh);
        $phuongXa = trim($request->phuongXa);
        $chiTietDiaDiem = trim($request->chiTietDiaDiem);

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
            'idSuKien' => $request->idSuKien,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'tenChienDich' => $request->tenChienDich,
            'moTa' => $request->moTa,
            'ngayBatDau' => $request->ngayBatDau,
            'ngayKetThuc' => $request->ngayKetThuc,
            'daXacNhanCuuTro' => $request->has('daXacNhanCuuTro') ? 1 : 0,
            'ghiChuXacNhan' => $request->ghiChuXacNhan,
            'trangThai' => $trangThai,
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

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể thêm cập nhật.'
                );
        }

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

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể thêm cập nhật.'
                );
        }

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
            $duongDanHinhAnh = $request->file('hinhAnh')
                ->store('cap-nhat-chien-dich', 'public');
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

    public function xacNhanChiTietDongGop(
        int $idNhom,
        int $idChienDich,
        int $idChiTietDongGop
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $thanhVien = $kiemTra['thanhVien'];

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return back()
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể xác nhận đóng góp.'
                );
        }

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
                'soLuongDaNhan' => $nguonLuc->soLuongDaNhan + $chiTiet->soLuong,
                'soLuongHienCo' => $nguonLuc->soLuongHienCo + $chiTiet->soLuong,
                'trangThai' => 'Còn hàng',
                'ngayCapNhat' => now(),
            ]);
        } else {
            NguonLucChienDich::create([
                'idChienDich' => $idChienDich,
                'idHangHoa' => $chiTiet->idHangHoa,
                'soLuongCanKeuGoi' => 0,
                'soLuongDaNhan' => $chiTiet->soLuong,
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

    public function tuChoiChiTietDongGop(
        int $idNhom,
        int $idChienDich,
        int $idChiTietDongGop
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return back()
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể từ chối đóng góp.'
                );
        }

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

    public function createDotPhanPhoi(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể tạo đợt phân phối.'
                );
        }

        $tiepNhanYeuCaus = TiepNhanYeuCau::with([
                'yeuCau.nguoiGui',
                'yeuCau.diaDiem',
            ])
            ->where('idChienDich', $idChienDich)
            ->where('idNhom', $idNhom)
            ->orderBy('idTiepNhan', 'desc')
            ->get();

        $nguonLucs = NguonLucChienDich::with('hangHoa')
            ->where('idChienDich', $idChienDich)
            ->where('soLuongHienCo', '>', 0)
            ->orderBy('idNguonLuc', 'desc')
            ->get();

        return view('nhom.chien_dich.phan_phoi_create', compact(
            'nhom',
            'chienDich',
            'laNhomTruong',
            'tiepNhanYeuCaus',
            'nguonLucs'
        ));
    }

    public function storeDotPhanPhoi(Request $request, int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể tạo đợt phân phối.'
                );
        }

        $request->validate([
            'idTiepNhan' => 'required|exists:TiepNhanYeuCau,idTiepNhan',
            'ngayPhanPhoi' => 'required|date',
            'nguoiNhan' => 'nullable|string|max:255',
            'ghiChu' => 'nullable|string',

            'idNguonLuc' => 'required|array|min:1',
            'idNguonLuc.*' => 'required|exists:NguonLucChienDich,idNguonLuc',

            'soLuongGiao' => 'required|array|min:1',
            'soLuongGiao.*' => 'required|numeric|min:1',
        ], [
            'idTiepNhan.required' => 'Vui lòng chọn yêu cầu cứu trợ cần phân phối.',
            'ngayPhanPhoi.required' => 'Vui lòng chọn ngày phân phối.',
            'idNguonLuc.required' => 'Vui lòng chọn ít nhất một nguồn lực.',
            'soLuongGiao.*.required' => 'Vui lòng nhập số lượng giao.',
            'soLuongGiao.*.min' => 'Số lượng giao phải lớn hơn 0.',
        ]);

        $tiepNhan = TiepNhanYeuCau::with('yeuCau.diaDiem')
            ->where('idTiepNhan', $request->idTiepNhan)
            ->where('idChienDich', $idChienDich)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        try {
            DB::transaction(function () use ($request, $chienDich, $tiepNhan) {
                $dotPhanPhoi = DotPhanPhoi::create([
                    'idChienDich' => $chienDich->idChienDich,
                    'ngayPhanPhoi' => $request->ngayPhanPhoi,
                    'trangThai' => 'Đã phân phối',
                    'ghiChu' => $request->ghiChu,
                ]);

                foreach ($request->idNguonLuc as $index => $idNguonLuc) {
                    $soLuongGiao = (float) $request->soLuongGiao[$index];

                    $nguonLuc = NguonLucChienDich::where('idChienDich', $chienDich->idChienDich)
                        ->where('idNguonLuc', $idNguonLuc)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($soLuongGiao > $nguonLuc->soLuongHienCo) {
                        throw new \Exception(
                            'Số lượng giao không được lớn hơn số lượng hiện có của nguồn lực.'
                        );
                    }

                    ChiTietPhanPhoi::create([
                        'idDotPhanPhoi' => $dotPhanPhoi->idDotPhanPhoi,
                        'idNguonLuc' => $nguonLuc->idNguonLuc,
                        'idDiaDiem' => $tiepNhan->yeuCau->idDiaDiem,
                        'idTiepNhan' => $tiepNhan->idTiepNhan,
                        'nguoiNhan' => $request->nguoiNhan,
                        'soLuongGiao' => $soLuongGiao,
                        'hinhAnh' => null,
                        'thoiGianGiao' => $request->ngayPhanPhoi,
                        'trangThai' => 'Đã giao',
                    ]);

                    $soLuongConLai = $nguonLuc->soLuongHienCo - $soLuongGiao;

                    $nguonLuc->update([
                        'soLuongHienCo' => $soLuongConLai,
                        'trangThai' => $soLuongConLai > 0 ? 'Còn hàng' : 'Hết hàng',
                        'ngayCapNhat' => now(),
                    ]);
                }

                $tiepNhan->update([
                    'trangThai' => 'Đang hỗ trợ',
                ]);

                $tiepNhan->yeuCau->update([
                    'trangThai' => 'Đang hỗ trợ',
                ]);
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
            ->with('success', 'Tạo đợt phân phối và trừ nguồn lực thành công.');
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã',
            'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ',
            'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ',
            'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ',
            'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ',
            'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ',
            'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e',
            'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u',
            'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace(
            $coDau,
            $khongDau,
            $chuoi
        );
    }
}