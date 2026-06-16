<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\ChienDichCuuTro;
use App\Models\NhomTinhNguyen;
use App\Models\SuKienCuuTro;
use App\Models\ThanhVienNhom;
use App\Models\TiepNhanYeuCau;
use App\Models\YeuCauCuuTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhomYeuCauCuuTroController extends Controller
{
    private function kiemTraThanhVien(int $idNhom): array
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')
                    ->with('error', 'Vui lòng đăng nhập.'),
            ];
        }

        $nhom = NhomTinhNguyen::findOrFail($idNhom);

        if ($nhom->trangThai !== 'Đang hoạt động') {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with(
                        'error',
                        'Nhóm này chưa hoạt động, đã ngừng hoạt động hoặc đã bị khóa.'
                    ),
            ];
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->first();

        if (!$thanhVien) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with('error', 'Bạn không thuộc nhóm này.'),
            ];
        }

        return [
            'hopLe' => true,
            'nhom' => $nhom,
            'thanhVien' => $thanhVien,
            'laNhomTruong' => $thanhVien->vaiTro === 'Nhóm trưởng',
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

        /*
         * Một yêu cầu vẫn có thể được nhiều nhóm tiếp nhận.
         * Không hiển thị lại nếu chính nhóm hiện tại đã tiếp nhận yêu cầu đó.
         */
        $yeuCausChoTiepNhan = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
            ->whereIn('trangThai', [
                'Chờ tiếp nhận',
                'Đã tiếp nhận',
                'Cần thêm hỗ trợ',
            ])
            ->whereDoesntHave('tiepNhans', function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom);
            })
            ->orderByRaw("
                CASE
                    WHEN trangThai = 'Cần thêm hỗ trợ' THEN 1
                    WHEN mucDoKhanCap = 'Khẩn cấp' THEN 2
                    WHEN mucDoKhanCap = 'Cao' THEN 3
                    WHEN mucDoKhanCap = 'Trung bình' THEN 4
                    WHEN mucDoKhanCap = 'Thấp' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('idYeuCau', 'desc')
            ->get();

        $yeuCausDaTiepNhan = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.chienDich',
                'tiepNhans.nhom',
            ])
            ->whereHas('tiepNhans', function ($query) use ($idNhom) {
                $query->where('idNhom', $idNhom);
            })
            ->orderBy('idYeuCau', 'desc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.index', compact(
            'nhom',
            'laNhomTruong',
            'yeuCausChoTiepNhan',
            'yeuCausDaTiepNhan'
        ));
    }

    public function show(int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.chienDich',
                'tiepNhans.nhom',
            ])
            ->findOrFail($idYeuCau);

        $tiepNhanCuaNhom = TiepNhanYeuCau::with('chienDich')
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->first();

        $daDuocNhomTiepNhan = $tiepNhanCuaNhom !== null;

        $tiepNhanDangCanThem = TiepNhanYeuCau::with('nhom')
            ->where('idYeuCau', $idYeuCau)
            ->where('trangThai', 'Cần thêm hỗ trợ')
            ->first();

        return view('nhom.yeu_cau_cuu_tro.show', compact(
            'nhom',
            'laNhomTruong',
            'yeuCau',
            'daDuocNhomTiepNhan',
            'tiepNhanCuaNhom',
            'tiepNhanDangCanThem'
        ));
    }

    public function createTiepNhan(int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
            ])
            ->findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDaTiepNhanYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đã tiếp nhận yêu cầu này trước đó.'
            );
        }

        $chienDichs = ChienDichCuuTro::where('idNhom', $idNhom)
            ->whereIn('trangThai', [
                'Sắp diễn ra',
                'Đang diễn ra',
                'Đang hoạt động',
            ])
            ->orderBy('idChienDich', 'desc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.tiep_nhan', compact(
            'nhom',
            'yeuCau',
            'chienDichs'
        ));
    }

    public function storeTiepNhan(
        Request $request,
        int $idNhom,
        int $idYeuCau
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $request->validate([
            'idChienDich' => 'required|exists:ChienDichCuuTro,idChienDich',
            'thoiGianDuKienHoTro' => 'nullable|date',
            'noiDungDamNhan' => 'required|string',
        ], [
            'idChienDich.required' => 'Vui lòng chọn chiến dịch tiếp nhận yêu cầu.',
            'idChienDich.exists' => 'Chiến dịch không hợp lệ.',
            'thoiGianDuKienHoTro.date' => 'Thời gian dự kiến hỗ trợ không hợp lệ.',
            'noiDungDamNhan.required' => 'Vui lòng nhập nội dung nhóm sẽ đảm nhận.',
        ]);

        $yeuCau = YeuCauCuuTro::findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDaTiepNhanYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đã tiếp nhận yêu cầu này trước đó.'
            );
        }

        $chienDich = ChienDichCuuTro::where('idChienDich', $request->idChienDich)
            ->where('idNhom', $idNhom)
            ->whereIn('trangThai', [
                'Sắp diễn ra',
                'Đang diễn ra',
                'Đang hoạt động',
            ])
            ->first();

        if (!$chienDich) {
            return back()
                ->withInput()
                ->with('error', 'Chiến dịch không thuộc nhóm hoặc không còn hoạt động.');
        }

        $this->taoLuotTiepNhan(
            $yeuCau,
            $chienDich,
            $idNhom,
            $request->thoiGianDuKienHoTro,
            $request->noiDungDamNhan
        );

        return redirect(
            '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
        )->with('success', 'Tiếp nhận yêu cầu cứu trợ thành công.');
    }

    public function createChienDichTuYeuCau(
        int $idNhom,
        int $idYeuCau
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền tạo chiến dịch từ yêu cầu cứu trợ.'
            );
        }

        $nhom = $kiemTra['nhom'];

        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
            ])
            ->findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDaTiepNhanYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đã tiếp nhận yêu cầu này trước đó.'
            );
        }

        $suKiens = SuKienCuuTro::where('trangThai', '!=', 'Đã ẩn')
            ->orderBy('idSuKien', 'desc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.tao_chien_dich', compact(
            'nhom',
            'yeuCau',
            'suKiens'
        ));
    }

    public function storeChienDichTuYeuCau(
        Request $request,
        int $idNhom,
        int $idYeuCau
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền tạo chiến dịch từ yêu cầu cứu trợ.'
            );
        }

        $request->validate([
            'idSuKien' => 'required|exists:SuKienCuuTro,idSuKien',
            'tenChienDich' => 'required|string|max:255',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'required|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daXacNhanCuuTro' => 'nullable|in:0,1',
            'ghiChuXacNhan' => 'nullable|string',
            'trangThaiChienDich' => 'required|string|max:255',

            'thoiGianDuKienHoTro' => 'nullable|date',
            'noiDungDamNhan' => 'required|string',
        ], [
            'idSuKien.required' => 'Vui lòng chọn sự kiện cứu trợ.',
            'idSuKien.exists' => 'Sự kiện cứu trợ không hợp lệ.',
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'ngayBatDau.required' => 'Vui lòng chọn ngày bắt đầu.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThaiChienDich.required' => 'Vui lòng chọn trạng thái chiến dịch.',
            'thoiGianDuKienHoTro.date' => 'Thời gian dự kiến hỗ trợ không hợp lệ.',
            'noiDungDamNhan.required' => 'Vui lòng nhập nội dung nhóm sẽ đảm nhận.',
        ]);

        $yeuCau = YeuCauCuuTro::with('diaDiem')
            ->findOrFail($idYeuCau);

        if (!$this->yeuCauCoTheTiepNhan($yeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Yêu cầu này không còn ở trạng thái cho phép tiếp nhận.'
            );
        }

        if ($this->nhomDaTiepNhanYeuCau($idNhom, $idYeuCau)) {
            return redirect(
                '/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau
            )->with(
                'error',
                'Nhóm đã tiếp nhận yêu cầu này trước đó.'
            );
        }

        DB::transaction(function () use (
            $request,
            $idNhom,
            $yeuCau
        ) {
            $chienDich = ChienDichCuuTro::create([
                'idNhom' => $idNhom,
                'idSuKien' => $request->idSuKien,
                'idDiaDiem' => $yeuCau->idDiaDiem,
                'tenChienDich' => trim($request->tenChienDich),
                'moTa' => $request->moTa,
                'ngayTao' => now(),
                'ngayBatDau' => $request->ngayBatDau,
                'ngayKetThuc' => $request->ngayKetThuc,
                'daXacNhanCuuTro' => (int) ($request->daXacNhanCuuTro ?? 0),
                'ghiChuXacNhan' => $request->ghiChuXacNhan,
                'trangThai' => $request->trangThaiChienDich,
            ]);

            $this->taoLuotTiepNhan(
                $yeuCau,
                $chienDich,
                $idNhom,
                $request->thoiGianDuKienHoTro,
                $request->noiDungDamNhan
            );
        });

        $chienDichMoi = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('tenChienDich', trim($request->tenChienDich))
            ->orderBy('idChienDich', 'desc')
            ->first();

        return redirect(
            '/nhom/' . $idNhom . '/chien-dich/' . $chienDichMoi->idChienDich
        )->with(
            'success',
            'Tạo chiến dịch từ yêu cầu cứu trợ thành công.'
        );
    }

    /**
     * Nhóm báo phần mình đảm nhận đang thiếu nguồn lực.
     */
    public function canThemHoTro(
        Request $request,
        int $idNhom,
        int $idYeuCau,
        int $idTiepNhan
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $request->validate([
            'noiDungDamNhan' => 'required|string',
        ], [
            'noiDungDamNhan.required' =>
                'Vui lòng ghi rõ phần đã hỗ trợ và phần còn thiếu.',
        ]);

        $tiepNhan = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        if ($tiepNhan->trangThai === 'Hoàn thành') {
            return back()->with(
                'error',
                'Lượt tiếp nhận đã hoàn thành nên không thể yêu cầu hỗ trợ thêm.'
            );
        }

        $dangCoNhomKhacCanThem = TiepNhanYeuCau::where(
                'idYeuCau',
                $idYeuCau
            )
            ->where('idTiepNhan', '!=', $idTiepNhan)
            ->where('trangThai', 'Cần thêm hỗ trợ')
            ->exists();

        if ($dangCoNhomKhacCanThem) {
            return back()->with(
                'error',
                'Đang có một nhóm khác cần hỗ trợ bổ sung. Vui lòng xử lý lượt đó trước.'
            );
        }

        $tiepNhan->update([
            'noiDungDamNhan' => $this->noiThemNoiDungDamNhan(
                $tiepNhan->noiDungDamNhan,
                'Cần thêm hỗ trợ: ' . trim($request->noiDungDamNhan)
            ),
            'trangThai' => 'Cần thêm hỗ trợ',
        ]);

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        return back()->with(
            'success',
            'Đã thông báo yêu cầu cần thêm hỗ trợ.'
        );
    }

    /**
     * Nhóm hoàn thành phần việc của lượt tiếp nhận.
     */
    public function hoanThanhTiepNhan(
        int $idNhom,
        int $idYeuCau,
        int $idTiepNhan
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $tiepNhan = TiepNhanYeuCau::where('idTiepNhan', $idTiepNhan)
            ->where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        if ($tiepNhan->trangThai === 'Hoàn thành') {
            return back()->with(
                'error',
                'Lượt tiếp nhận này đã hoàn thành trước đó.'
            );
        }

        $tiepNhan->update([
            'trangThai' => 'Hoàn thành',
        ]);

        $this->capNhatTrangThaiTongYeuCau($idYeuCau);

        return back()->with(
            'success',
            'Đã hoàn thành phần hỗ trợ của nhóm.'
        );
    }

    private function yeuCauCoTheTiepNhan(
        YeuCauCuuTro $yeuCau
    ): bool {
        return in_array($yeuCau->trangThai, [
            'Chờ tiếp nhận',
            'Đã tiếp nhận',
            'Cần thêm hỗ trợ',
        ], true);
    }

    private function nhomDaTiepNhanYeuCau(
        int $idNhom,
        int $idYeuCau
    ): bool {
        return TiepNhanYeuCau::where('idNhom', $idNhom)
            ->where('idYeuCau', $idYeuCau)
            ->exists();
    }

    /**
     * Hàm dùng chung cho:
     * - Thêm yêu cầu vào chiến dịch có sẵn.
     * - Tạo chiến dịch mới từ yêu cầu.
     */
    private function taoLuotTiepNhan(
        YeuCauCuuTro $yeuCau,
        ChienDichCuuTro $chienDich,
        int $idNhom,
        ?string $thoiGianDuKienHoTro,
        ?string $noiDungDamNhan
    ): TiepNhanYeuCau {
        return DB::transaction(function () use (
            $yeuCau,
            $chienDich,
            $idNhom,
            $thoiGianDuKienHoTro,
            $noiDungDamNhan
        ) {
            $yeuCauKhoa = YeuCauCuuTro::where(
                    'idYeuCau',
                    $yeuCau->idYeuCau
                )
                ->lockForUpdate()
                ->firstOrFail();

            if (!$this->yeuCauCoTheTiepNhan($yeuCauKhoa)) {
                abort(422, 'Yêu cầu không còn được phép tiếp nhận.');
            }

            $daTiepNhan = TiepNhanYeuCau::where(
                    'idYeuCau',
                    $yeuCauKhoa->idYeuCau
                )
                ->where('idNhom', $idNhom)
                ->exists();

            if ($daTiepNhan) {
                abort(422, 'Nhóm đã tiếp nhận yêu cầu này.');
            }

            /*
             * Nếu đang có một nhóm báo thiếu, lượt cũ trở lại Đã tiếp nhận
             * khi nhóm mới nhận phần hỗ trợ bổ sung.
             */
            $tiepNhanDangCanThem = TiepNhanYeuCau::where(
                    'idYeuCau',
                    $yeuCauKhoa->idYeuCau
                )
                ->where('trangThai', 'Cần thêm hỗ trợ')
                ->lockForUpdate()
                ->first();

            if (
                $tiepNhanDangCanThem
                && $tiepNhanDangCanThem->idNhom != $idNhom
            ) {
                $tiepNhanDangCanThem->update([
                    'trangThai' => 'Đã tiếp nhận',
                ]);
            }

            $tiepNhanMoi = TiepNhanYeuCau::create([
                'idYeuCau' => $yeuCauKhoa->idYeuCau,
                'idChienDich' => $chienDich->idChienDich,
                'idNhom' => $idNhom,
                'thoiGianTiepNhan' => now(),
                'thoiGianDuKienHoTro' => $thoiGianDuKienHoTro,
                'noiDungDamNhan' => $this->noiThemNoiDungDamNhan(
                    null,
                    $noiDungDamNhan
                ),
                'trangThai' => 'Đã tiếp nhận',
            ]);

            $yeuCauKhoa->update([
                'trangThai' => 'Đã tiếp nhận',
            ]);

            return $tiepNhanMoi;
        });
    }

    /**
     * Chỉ hệ thống gọi hàm này.
     * Nhóm không được trực tiếp chỉnh trạng thái tổng của yêu cầu.
     */
    private function capNhatTrangThaiTongYeuCau(
        int $idYeuCau
    ): void {
        $yeuCau = YeuCauCuuTro::findOrFail($idYeuCau);

        if ($yeuCau->trangThai === 'Đã hủy') {
            return;
        }

        $tiepNhans = TiepNhanYeuCau::where(
            'idYeuCau',
            $idYeuCau
        )->get();

        if ($tiepNhans->isEmpty()) {
            $yeuCau->update([
                'trangThai' => 'Chờ tiếp nhận',
            ]);

            return;
        }

        $tatCaHoanThanh = $tiepNhans->every(function ($tiepNhan) {
            return $tiepNhan->trangThai === 'Hoàn thành';
        });

        if ($tatCaHoanThanh) {
            $yeuCau->update([
                'trangThai' => 'Hoàn thành',
            ]);

            return;
        }

        $coLuotCanThemHoTro = $tiepNhans->contains(function ($tiepNhan) {
            return $tiepNhan->trangThai === 'Cần thêm hỗ trợ';
        });

        $yeuCau->update([
            'trangThai' => $coLuotCanThemHoTro
                ? 'Cần thêm hỗ trợ'
                : 'Đã tiếp nhận',
        ]);
    }

    private function noiThemNoiDungDamNhan(
        ?string $noiDungCu,
        ?string $noiDungMoi
    ): string {
        $chuanHoaDanhSach = function (?string $noiDung): array {
            $noiDung = trim((string) $noiDung);

            if ($noiDung === '') {
                return [];
            }

            $noiDung = str_replace(["\r\n", "\r"], "\n", $noiDung);
            $cacDong = explode("\n", $noiDung);

            $ketQua = [];

            foreach ($cacDong as $dong) {
                $dong = trim($dong);

                if ($dong === '') {
                    continue;
                }

                // Tránh trường hợp người dùng tự nhập dấu "-" rồi hệ thống thêm lần nữa.
                $dong = preg_replace('/^\s*-\s*/u', '', $dong);
                $dong = trim((string) $dong);

                if ($dong !== '') {
                    $ketQua[] = '- ' . $dong;
                }
            }

            return $ketQua;
        };

        $cacDongCu = $chuanHoaDanhSach($noiDungCu);
        $cacDongMoi = $chuanHoaDanhSach($noiDungMoi);

        return implode(PHP_EOL, array_merge($cacDongCu, $cacDongMoi));
    }
}