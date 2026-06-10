<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DongGop;
use App\Models\ChiTietDongGop;
use App\Models\ChienDichCuuTro;
use App\Models\HangHoa;
use Illuminate\Http\Request;

class UserDongGopController extends Controller
{
    public function index()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $dongGops = DongGop::with([
                'chienDich',
                'chienDich.nhom',
                'chiTietDongGops.hangHoa',
                'thanhVienTiepNhan.nguoiDung'
            ])
            ->where('idNguoiUngHo', $idNguoiDung)
            ->orderBy('idDongGop', 'desc')
            ->get();

        return view('user.dong_gop.index', compact('dongGops'));
    }

    public function create()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $chienDichs = ChienDichCuuTro::with(['nhom', 'suKien', 'diaDiem'])
            ->where('trangThai', 'Đang hoạt động')
            ->orderBy('idChienDich', 'desc')
            ->get();

        $hangHoas = HangHoa::with('danhMucHang')
            ->where(function ($query) {
                $query->where('trangThai', 'Đang sử dụng')
                    ->orWhere('trangThai', 'Hoạt động');
            })
            ->orderBy('tenHangHoa')
            ->get();

        return view('user.dong_gop.create', compact('chienDichs', 'hangHoas'));
    }

    public function store(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $request->validate([
            'idChienDich' => 'required|exists:ChienDichCuuTro,idChienDich',
            'ghiChu' => 'nullable|string|max:255',

            'hangHoas' => 'required|array|min:1',
            'hangHoas.*.idHangHoa' => 'required|exists:HangHoa,idHangHoa',
            'hangHoas.*.soLuong' => 'required|numeric|min:1',
            'hangHoas.*.hanSuDung' => 'nullable|date',
        ], [
            'idChienDich.required' => 'Vui lòng chọn chiến dịch.',
            'idChienDich.exists' => 'Chiến dịch không hợp lệ.',

            'hangHoas.required' => 'Vui lòng nhập ít nhất một loại hàng hóa đóng góp.',
            'hangHoas.*.idHangHoa.required' => 'Vui lòng chọn hàng hóa.',
            'hangHoas.*.idHangHoa.exists' => 'Hàng hóa không hợp lệ.',
            'hangHoas.*.soLuong.required' => 'Vui lòng nhập số lượng.',
            'hangHoas.*.soLuong.numeric' => 'Số lượng phải là số.',
            'hangHoas.*.soLuong.min' => 'Số lượng phải lớn hơn 0.',
            'hangHoas.*.hanSuDung.date' => 'Hạn sử dụng không hợp lệ.',
        ]);

        $chienDich = ChienDichCuuTro::where('idChienDich', $request->idChienDich)
            ->where('trangThai', 'Đang hoạt động')
            ->first();

        if (!$chienDich) {
            return back()
                ->withInput()
                ->with('error', 'Chỉ có thể đóng góp cho chiến dịch đang hoạt động.');
        }

        $dongGop = DongGop::create([
            'idChienDich' => $request->idChienDich,
            'idNguoiUngHo' => $idNguoiDung,
            'idNguoiTiepNhan' => null,
            'ghiChu' => $request->ghiChu,
            'thoiGianDongGop' => now(),
        ]);

        foreach ($request->hangHoas as $hangHoa) {
            ChiTietDongGop::create([
                'idDongGop' => $dongGop->idDongGop,
                'idHangHoa' => $hangHoa['idHangHoa'],
                'soLuong' => $hangHoa['soLuong'],
                'hanSuDung' => $hangHoa['hanSuDung'] ?? null,
                'trangThai' => 'Chờ xác nhận',
            ]);
        }

        return redirect('/user/dong-gop')
            ->with('success', 'Gửi đăng ký đóng góp thành công. Vui lòng chờ nhóm tình nguyện xác nhận.');
    }
}