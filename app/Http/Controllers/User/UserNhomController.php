<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class UserNhomController extends Controller
{
    public function index()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // 1. Nhóm tôi tham gia: gồm cả nhóm trưởng và thành viên
        $nhomThamGias = ThanhVienNhom::with(['nhom.diaDiem', 'nhom.nhomTruong'])
            ->where('idNguoiDung', $idNguoiDung)
            ->orderBy('idThanhVien', 'desc')
            ->get();

        // 2. Nhóm tôi đăng ký tạo đang chờ duyệt
        $nhomChoDuyets = NhomTinhNguyen::with(['diaDiem', 'nhomTruong'])
            ->where('idNhomTruong', $idNguoiDung)
            ->where('trangThai', 'Chờ duyệt')
            ->orderBy('idNhom', 'desc')
            ->get();

        return view('user.nhom_cua_toi.index', compact(
            'nhomThamGias',
            'nhomChoDuyets'
        ));
    }

    public function show(int $id)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $nhom = NhomTinhNguyen::with(['diaDiem', 'nhomTruong'])
            ->where('idNhom', $id)
            ->where('idNhomTruong', $idNguoiDung)
            ->where('trangThai', 'Chờ duyệt')
            ->firstOrFail();

        return view('user.nhom_cua_toi.show', compact('nhom'));
    }

    public function create()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

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

        return view('user.nhom_cua_toi.create', compact(
            'diaDiems',
            'diaDiemJson'
        ));
    }

    public function store(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $request->validate([
            'tenNhom' => 'required|string|max:255',
            'moTa' => 'nullable|string|max:255',

            'idDiaDiemCoSan' => 'nullable|exists:DiaDiem,idDiaDiem',
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',
        ], [
            'tenNhom.required' => 'Vui lòng nhập tên nhóm tình nguyện.',
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

        // Tạo địa điểm cho nhóm đăng ký
        $diaDiem = DiaDiem::create([
            'tinhThanh' => $request->tinhThanh,
            'phuongXa' => $request->phuongXa,
            'chiTietDiaDiem' => $request->chiTietDiaDiem,
            'viDo' => $request->viDo,
            'kinhDo' => $request->kinhDo,
        ]);

        // Tạo nhóm ở trạng thái chờ duyệt
        NhomTinhNguyen::create([
            'tenNhom' => $request->tenNhom,
            'moTa' => $request->moTa,
            'idNhomTruong' => $idNguoiDung,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'trangThai' => 'Chờ duyệt',
            'ngayTao' => now(),
        ]);

        return redirect('/user/nhom-cua-toi')
            ->with('success', 'Đăng ký tạo nhóm thành công. Vui lòng chờ quản trị viên duyệt.');
    }
}