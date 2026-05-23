<?php

namespace App\Http\Controllers;

use App\Models\ChienDichCuuTro;
use App\Models\NhomTinhNguyen;
use App\Models\ThienTai;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class ChienDichCuuTroController extends Controller
{
    public function index()
    {
        $chienDichs = ChienDichCuuTro::with(['nhom', 'thienTai', 'diaDiem'])
            ->orderBy('idChienDich', 'desc')
            ->get();

        return view('admin.chien_dich.index', compact('chienDichs'));
    }

    public function create()
    {
        $nhomTinhNguyens = NhomTinhNguyen::where('trangThai', 'Đang hoạt động')
            ->orderBy('tenNhom')
            ->get();

        $thienTais = ThienTai::orderBy('namXayRa', 'desc')
            ->orderBy('tenThienTai')
            ->get();

        $diaDiems = DiaDiem::orderBy('tinhThanh')->get();

        return view('admin.chien_dich.create', compact('nhomTinhNguyens', 'thienTais', 'diaDiems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenChienDich' => 'required|string|max:255',
            'idNhom' => 'required|exists:NhomTinhNguyen,idNhom',
            'idThienTai' => 'required|exists:ThienTai,idThienTai',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daThongBaoUBND' => 'nullable',
            'ghiChuUBND' => 'nullable|string|max:255',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'idNhom.required' => 'Vui lòng chọn nhóm tình nguyện.',
            'idNhom.exists' => 'Nhóm tình nguyện không hợp lệ.',
            'idThienTai.required' => 'Vui lòng chọn thiên tai.',
            'idThienTai.exists' => 'Thiên tai không hợp lệ.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'idDiaDiem.exists' => 'Địa điểm không hợp lệ.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        ChienDichCuuTro::create([
            'idNhom' => $request->idNhom,
            'idThienTai' => $request->idThienTai,
            'idDiaDiem' => $request->idDiaDiem,
            'tenChienDich' => $request->tenChienDich,
            'moTa' => $request->moTa,
            'ngayTao' => now(),
            'ngayBatDau' => $request->ngayBatDau,
            'ngayKetThuc' => $request->ngayKetThuc,
            'daThongBaoUBND' => $request->has('daThongBaoUBND'),
            'ghiChuUBND' => $request->ghiChuUBND,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/chien-dich')->with('success', 'Thêm chiến dịch cứu trợ thành công.');
    }

    public function edit(int $id)
    {
        $chienDich = ChienDichCuuTro::findOrFail($id);

        $nhomTinhNguyens = NhomTinhNguyen::orderBy('tenNhom')->get();
        $thienTais = ThienTai::orderBy('namXayRa', 'desc')->orderBy('tenThienTai')->get();
        $diaDiems = DiaDiem::orderBy('tinhThanh')->get();

        return view('admin.chien_dich.edit', compact('chienDich', 'nhomTinhNguyens', 'thienTais', 'diaDiems'));
    }

    public function update(Request $request, int $id)
    {
        $chienDich = ChienDichCuuTro::findOrFail($id);

        $request->validate([
            'tenChienDich' => 'required|string|max:255',
            'idNhom' => 'required|exists:NhomTinhNguyen,idNhom',
            'idThienTai' => 'required|exists:ThienTai,idThienTai',
            'idDiaDiem' => 'required|exists:DiaDiem,idDiaDiem',
            'moTa' => 'nullable|string',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after_or_equal:ngayBatDau',
            'daThongBaoUBND' => 'nullable',
            'ghiChuUBND' => 'nullable|string|max:255',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenChienDich.required' => 'Vui lòng nhập tên chiến dịch.',
            'idNhom.required' => 'Vui lòng chọn nhóm tình nguyện.',
            'idThienTai.required' => 'Vui lòng chọn thiên tai.',
            'idDiaDiem.required' => 'Vui lòng chọn địa điểm.',
            'ngayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $chienDich->update([
            'idNhom' => $request->idNhom,
            'idThienTai' => $request->idThienTai,
            'idDiaDiem' => $request->idDiaDiem,
            'tenChienDich' => $request->tenChienDich,
            'moTa' => $request->moTa,
            'ngayBatDau' => $request->ngayBatDau,
            'ngayKetThuc' => $request->ngayKetThuc,
            'daThongBaoUBND' => $request->has('daThongBaoUBND'),
            'ghiChuUBND' => $request->ghiChuUBND,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/chien-dich')->with('success', 'Cập nhật chiến dịch cứu trợ thành công.');
    }

    public function destroy(int $id)
    {
        $chienDich = ChienDichCuuTro::findOrFail($id);
        $chienDich->delete();

        return redirect('/admin/chien-dich')->with('success', 'Xóa chiến dịch cứu trợ thành công.');
    }
}