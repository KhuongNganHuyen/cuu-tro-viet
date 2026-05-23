<?php

namespace App\Http\Controllers;

use App\Models\TiepNhanYeuCau;
use App\Models\YeuCauCuuTro;
use App\Models\ChienDichCuuTro;
use App\Models\NhomTinhNguyen;
use Illuminate\Http\Request;

class TiepNhanYeuCauController extends Controller
{
    public function index()
    {
        $tiepNhans = TiepNhanYeuCau::with(['yeuCau.nguoiGui', 'yeuCau.diaDiem', 'chienDich', 'nhom'])
            ->orderBy('idTiepNhan', 'desc')
            ->get();

        return view('admin.tiep_nhan_yeu_cau.index', compact('tiepNhans'));
    }

    public function create(int $idYeuCau)
    {
        $yeuCau = YeuCauCuuTro::with(['nguoiGui', 'diaDiem'])->findOrFail($idYeuCau);

        $chienDichs = ChienDichCuuTro::whereIn('trangThai', ['Sắp diễn ra', 'Đang diễn ra'])
            ->orderBy('tenChienDich')
            ->get();

        $nhomTinhNguyens = NhomTinhNguyen::where('trangThai', 'Đang hoạt động')
            ->orderBy('tenNhom')
            ->get();

        return view('admin.tiep_nhan_yeu_cau.create', compact('yeuCau', 'chienDichs', 'nhomTinhNguyens'));
    }

    public function store(Request $request, int $idYeuCau)
    {
        $yeuCau = YeuCauCuuTro::findOrFail($idYeuCau);

        $request->validate([
            'idChienDich' => 'required|exists:ChienDichCuuTro,idChienDich',
            'idNhom' => 'required|exists:NhomTinhNguyen,idNhom',
            'noiDungDamNhan' => 'nullable|string',
            'thoiGianDuKienHoTro' => 'nullable|date',
            'trangThai' => 'required|string|max:255',
        ], [
            'idChienDich.required' => 'Vui lòng chọn chiến dịch.',
            'idNhom.required' => 'Vui lòng chọn nhóm tình nguyện.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        TiepNhanYeuCau::create([
            'idYeuCau' => $yeuCau->idYeuCau,
            'idChienDich' => $request->idChienDich,
            'idNhom' => $request->idNhom,
            'noiDungDamNhan' => $request->noiDungDamNhan,
            'thoiGianTiepNhan' => now(),
            'thoiGianDuKienHoTro' => $request->thoiGianDuKienHoTro,
            'trangThai' => $request->trangThai,
        ]);

        $yeuCau->update([
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/yeu-cau-cuu-tro/' . $idYeuCau)->with('success', 'Tiếp nhận yêu cầu cứu trợ thành công.');
    }

    public function destroy(int $idTiepNhan)
    {
        $tiepNhan = TiepNhanYeuCau::findOrFail($idTiepNhan);
        $tiepNhan->delete();

        return redirect('/admin/tiep-nhan-yeu-cau')->with('success', 'Xóa thông tin tiếp nhận thành công.');
    }
}