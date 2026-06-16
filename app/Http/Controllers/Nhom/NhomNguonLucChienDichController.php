<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\ChienDichCuuTro;
use App\Models\ChiTietDongGop;
use App\Models\ChiTietPhanPhoi;
use App\Models\HangHoa;
use App\Models\NguonLucChienDich;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhomNguonLucChienDichController extends Controller
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

        $nhom = NhomTinhNguyen::with([
                'nhomTruong',
                'diaDiem',
            ])
            ->findOrFail($idNhom);

        if ($nhom->trangThai !== 'Đang hoạt động') {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with(
                        'error',
                        'Nhóm này chưa được phép hoạt động hoặc đã bị khóa.'
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
                    ->with(
                        'error',
                        'Bạn không thuộc nhóm tình nguyện này.'
                    ),
            ];
        }

        $laNhomTruong =
            $thanhVien->vaiTro === 'Nhóm trưởng'
            || $nhom->idNhomTruong == $idNguoiDung;

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

    private function layHangHoaHopLe(int $idNhom)
    {
        return HangHoa::with('danhMucHang')
            ->where('trangThai', 'Đang sử dụng')
            ->where(function ($query) use ($idNhom) {
                $query->whereNull('idNhom')
                    ->orWhere('idNhom', $idNhom);
            })
            ->get();
    }

    private function nguonLucDaPhatSinh(
        NguonLucChienDich $nguonLuc,
        int $idChienDich
    ): bool {
        if (
            (float) $nguonLuc->soLuongDaNhan > 0
            || (float) $nguonLuc->soLuongHienCo > 0
        ) {
            return true;
        }

        $coDongGop = ChiTietDongGop::where('idHangHoa', $nguonLuc->idHangHoa)
            ->whereIn('trangThai', [
                'Chờ xác nhận',
                'Đã xác nhận',
            ])
            ->whereHas('dongGop', function ($query) use ($idChienDich) {
                $query->where('idChienDich', $idChienDich);
            })
            ->exists();

        if ($coDongGop) {
            return true;
        }

        return ChiTietPhanPhoi::where('idNguonLuc', $nguonLuc->idNguonLuc)
            ->exists();
    }

    public function edit(int $idNhom, int $idChienDich)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền cập nhật nguồn lực chiến dịch.'
                );
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
                    'Chiến dịch đã hoàn thành nên không thể cập nhật nguồn lực.'
                );
        }

        $nguonLucTheoHangHoa = NguonLucChienDich::with('hangHoa.danhMucHang')
            ->where('idChienDich', $idChienDich)
            ->get()
            ->keyBy('idHangHoa');

        $hangHoas = $this->layHangHoaHopLe($idNhom)
            ->sortBy(function ($hangHoa) use ($nguonLucTheoHangHoa, $idNhom) {
                $daDuocChon = $nguonLucTheoHangHoa->has($hangHoa->idHangHoa)
                    ? 0
                    : 1;

                $phamVi = (int) $hangHoa->idNhom === (int) $idNhom
                    ? 0
                    : 1;

                return sprintf(
                    '%d-%d-%010d',
                    $daDuocChon,
                    $phamVi,
                    (int) $hangHoa->idHangHoa
                );
            })
            ->values();

        return view('nhom.chien_dich.cap_nhat_nguon_luc', compact(
            'nhom',
            'chienDich',
            'hangHoas',
            'nguonLucTheoHangHoa'
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
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền cập nhật nguồn lực chiến dịch.'
                );
        }

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $idChienDich)
            ->firstOrFail();

        if ($this->chienDichDaHoanThanh($chienDich)) {
            return redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich)
                ->with(
                    'error',
                    'Chiến dịch đã hoàn thành nên không thể cập nhật nguồn lực.'
                );
        }

        $request->validate([
            'nguonLuc' => 'nullable|array',
        ], [
            'nguonLuc.array' => 'Dữ liệu nguồn lực không hợp lệ.',
        ]);

        $trangThaiHopLe = [
            'Đang kêu gọi',
            'Ngừng tiếp nhận',
            'Đã đủ',
        ];

        $duLieuNguonLuc = $request->input('nguonLuc', []);

        $hangHoaHopLeTheoId = $this->layHangHoaHopLe($idNhom)
            ->keyBy('idHangHoa');

        $nguonLucHienTaiTheoHangHoa = NguonLucChienDich::where('idChienDich', $idChienDich)
            ->get()
            ->keyBy('idHangHoa');

        $nguonLucDuocChon = [];

        foreach ($duLieuNguonLuc as $idHangHoa => $duLieu) {
            if (empty($duLieu['chon'])) {
                continue;
            }

            $idHangHoa = (int) $idHangHoa;

            if (!$hangHoaHopLeTheoId->has($idHangHoa)) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Có mặt hàng không hợp lệ hoặc không thuộc phạm vi sử dụng của nhóm.'
                    );
            }

            $soLuongCanKeuGoi = $duLieu['soLuongCanKeuGoi'] ?? null;

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

            $trangThai = $duLieu['trangThai'] ?? 'Đang kêu gọi';

            if (!in_array($trangThai, $trangThaiHopLe, true)) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Trạng thái nguồn lực không hợp lệ.'
                    );
            }

            $nguonLucDuocChon[$idHangHoa] = [
                'soLuongCanKeuGoi' => (float) $soLuongCanKeuGoi,
                'trangThai' => $trangThai,
            ];
        }

        $nguonLucKhongTheXoa = [];

        DB::transaction(function () use (
            $idChienDich,
            $nguonLucDuocChon,
            $nguonLucHienTaiTheoHangHoa,
            &$nguonLucKhongTheXoa
        ) {
            foreach ($nguonLucDuocChon as $idHangHoa => $duLieu) {
                $nguonLuc = $nguonLucHienTaiTheoHangHoa->get($idHangHoa);

                if ($nguonLuc) {
                    $nguonLuc->update([
                        'soLuongCanKeuGoi' => $duLieu['soLuongCanKeuGoi'],
                        'trangThai' => $duLieu['trangThai'],
                        'ngayCapNhat' => now(),
                    ]);

                    continue;
                }

                NguonLucChienDich::create([
                    'idChienDich' => $idChienDich,
                    'idHangHoa' => $idHangHoa,
                    'soLuongCanKeuGoi' => $duLieu['soLuongCanKeuGoi'],
                    'soLuongDaNhan' => 0,
                    'soLuongHienCo' => 0,
                    'hanSuDung' => null,
                    'trangThai' => $duLieu['trangThai'],
                    'ngayCapNhat' => now(),
                ]);
            }

            foreach ($nguonLucHienTaiTheoHangHoa as $idHangHoa => $nguonLuc) {
                if (array_key_exists($idHangHoa, $nguonLucDuocChon)) {
                    continue;
                }

                if ($this->nguonLucDaPhatSinh($nguonLuc, $idChienDich)) {
                    $nguonLucKhongTheXoa[] =
                        $nguonLuc->hangHoa->tenHangHoa ?? ('#' . $nguonLuc->idNguonLuc);

                    continue;
                }

                $nguonLuc->delete();
            }
        });

        $redirect = redirect('/nhom/' . $idNhom . '/chien-dich/' . $idChienDich . '#nguon-luc')
            ->with(
                'success',
                'Cập nhật nguồn lực chiến dịch thành công.'
            );

        if (count($nguonLucKhongTheXoa) > 0) {
            $redirect->with(
                'error',
                'Một số nguồn lực đã phát sinh đóng góp hoặc phân phối nên không thể xóa: '
                    . implode(', ', $nguonLucKhongTheXoa)
                    . '. Bạn có thể chuyển trạng thái sang Ngừng tiếp nhận.'
            );
        }

        return $redirect;
    }
}