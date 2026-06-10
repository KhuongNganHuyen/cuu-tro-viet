<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\YeuCauCuuTro;
use App\Models\TiepNhanYeuCau;
use App\Models\ChienDichCuuTro;
use App\Models\SuKienCuuTro;
use Illuminate\Http\Request;

class NhomYeuCauCuuTroController extends Controller
{
    private function kiemTraThanhVien(int $idNhom)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')->with('error', 'Vui lòng đăng nhập.'),
            ];
        }

        $nhom = NhomTinhNguyen::findOrFail($idNhom);

        if ($nhom->trangThai != 'Đang hoạt động') {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with('error', 'Nhóm này chưa hoạt động hoặc đã bị khóa.'),
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
            'laNhomTruong' => $thanhVien->vaiTro == 'Nhóm trưởng',
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

        $yeuCausChoTiepNhan = YeuCauCuuTro::with(['nguoiGui', 'diaDiem'])
            ->where('trangThai', 'Chờ tiếp nhận')
            ->orderByRaw("
                CASE 
                    WHEN mucDoKhanCap = 'Khẩn cấp' THEN 1
                    WHEN mucDoKhanCap = 'Cao' THEN 2
                    WHEN mucDoKhanCap = 'Trung bình' THEN 3
                    WHEN mucDoKhanCap = 'Thấp' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('idYeuCau', 'desc')
            ->get();

        $yeuCausDaTiepNhan = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.chienDich',
                'tiepNhans.nhom'
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
                'tiepNhans.nhom'
            ])
            ->findOrFail($idYeuCau);

        $daDuocNhomTiepNhan = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->exists();

        return view('nhom.yeu_cau_cuu_tro.show', compact(
            'nhom',
            'laNhomTruong',
            'yeuCau',
            'daDuocNhomTiepNhan'
        ));
    }

    public function createTiepNhan(int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];

        $yeuCau = YeuCauCuuTro::with(['nguoiGui', 'diaDiem'])->findOrFail($idYeuCau);

        if ($yeuCau->trangThai != 'Chờ tiếp nhận') {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Yêu cầu này đã được tiếp nhận hoặc không còn ở trạng thái chờ tiếp nhận.');
        }

        $daDuocNhomTiepNhan = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)
            ->where('idNhom', $idNhom)
            ->exists();

        if ($daDuocNhomTiepNhan) {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Nhóm đã tiếp nhận yêu cầu này trước đó.');
        }

        $chienDichs = ChienDichCuuTro::where('idNhom', $idNhom)
            ->whereIn('trangThai', ['Sắp diễn ra', 'Đang diễn ra', 'Đang hoạt động'])
            ->orderBy('idChienDich', 'desc')
            ->get();

        return view('nhom.yeu_cau_cuu_tro.tiep_nhan', compact(
            'nhom',
            'yeuCau',
            'chienDichs'
        ));
    }

    public function storeTiepNhan(Request $request, int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $yeuCau = YeuCauCuuTro::findOrFail($idYeuCau);

        if ($yeuCau->trangThai != 'Chờ tiếp nhận') {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Yêu cầu này đã được tiếp nhận hoặc không còn ở trạng thái chờ tiếp nhận.');
        }

        $request->validate([
            'idChienDich' => 'required|exists:ChienDichCuuTro,idChienDich',
            'thoiGianDuKienHoTro' => 'nullable|date',
            'noiDungDamNhan' => 'nullable|string',
            'trangThai' => 'required|string|max:255',
        ], [
            'idChienDich.required' => 'Vui lòng chọn chiến dịch tiếp nhận yêu cầu.',
            'idChienDich.exists' => 'Chiến dịch không hợp lệ.',
            'thoiGianDuKienHoTro.date' => 'Thời gian dự kiến hỗ trợ không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái tiếp nhận.',
        ]);

        $chienDich = ChienDichCuuTro::where('idNhom', $idNhom)
            ->where('idChienDich', $request->idChienDich)
            ->firstOrFail();

        $daDuocTiepNhan = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)->exists();

        if ($daDuocTiepNhan) {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Yêu cầu này đã được nhóm khác tiếp nhận.');
        }

        TiepNhanYeuCau::create([
            'idYeuCau' => $yeuCau->idYeuCau,
            'idChienDich' => $chienDich->idChienDich,
            'idNhom' => $idNhom,
            'thoiGianTiepNhan' => now(),
            'thoiGianDuKienHoTro' => $request->thoiGianDuKienHoTro,
            'noiDungDamNhan' => $request->noiDungDamNhan,
            'trangThai' => $request->trangThai,
        ]);

        $yeuCau->update([
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
            ->with('success', 'Tiếp nhận yêu cầu cứu trợ thành công.');
    }

    public function createChienDichTuYeuCau(int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Chỉ nhóm trưởng mới có quyền tạo chiến dịch từ yêu cầu cứu trợ.');
        }

        $nhom = $kiemTra['nhom'];

        $yeuCau = YeuCauCuuTro::with(['nguoiGui', 'diaDiem'])->findOrFail($idYeuCau);

        if ($yeuCau->trangThai != 'Chờ tiếp nhận') {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Yêu cầu này đã được tiếp nhận hoặc không còn ở trạng thái chờ tiếp nhận.');
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

    public function storeChienDichTuYeuCau(Request $request, int $idNhom, int $idYeuCau)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Chỉ nhóm trưởng mới có quyền tạo chiến dịch từ yêu cầu cứu trợ.');
        }

        $yeuCau = YeuCauCuuTro::with('diaDiem')->findOrFail($idYeuCau);

        if ($yeuCau->trangThai != 'Chờ tiếp nhận') {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Yêu cầu này đã được tiếp nhận hoặc không còn ở trạng thái chờ tiếp nhận.');
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
            'noiDungDamNhan' => 'nullable|string',
            'trangThaiTiepNhan' => 'required|string|max:255',
        ], [
            'idSuKien.required' => 'Vui lòng chọn sự kiện cứu trợ.',
            'idSuKien.exists' => 'Sự kiện cứu trợ không hợp lệ.',
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'ngayBatDau.required' => 'Vui lòng chọn ngày bắt đầu.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThaiChienDich.required' => 'Vui lòng chọn trạng thái chiến dịch.',
            'trangThaiTiepNhan.required' => 'Vui lòng chọn trạng thái tiếp nhận.',
        ]);

        $daDuocTiepNhan = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)->exists();

        if ($daDuocTiepNhan) {
            return redirect('/nhom/' . $idNhom . '/yeu-cau-cuu-tro/' . $idYeuCau)
                ->with('error', 'Yêu cầu này đã được nhóm khác tiếp nhận.');
        }

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

        TiepNhanYeuCau::create([
            'idYeuCau' => $yeuCau->idYeuCau,
            'idChienDich' => $chienDich->idChienDich,
            'idNhom' => $idNhom,
            'thoiGianTiepNhan' => now(),
            'thoiGianDuKienHoTro' => $request->thoiGianDuKienHoTro,
            'noiDungDamNhan' => $request->noiDungDamNhan,
            'trangThai' => $request->trangThaiTiepNhan,
        ]);

        $yeuCau->update([
            'trangThai' => $request->trangThaiTiepNhan,
        ]);

        return redirect('/nhom/' . $idNhom . '/chien-dich/' . $chienDich->idChienDich)
            ->with('success', 'Tạo chiến dịch từ yêu cầu cứu trợ thành công.');
    }
}