<?php

namespace App\Http\Controllers;

use App\Models\HangHoa;
use App\Models\DanhMucHang;
use Illuminate\Http\Request;

class HangHoaController extends Controller
{
    public function index(Request $request, int $idDanhMucHang)
    {
        $danhMucHang = DanhMucHang::findOrFail($idDanhMucHang);

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $hangHoas = HangHoa::where('idDanhMucHang', $idDanhMucHang)
            ->whereNull('idNhom')
            ->orderBy('idHangHoa', 'asc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $hangHoas = $hangHoas->filter(function ($hangHoa) use ($tuKhoa, $tuKhoaKhongDau) {
                $noiDungTimKiem = implode(' ', [
                    $hangHoa->idHangHoa,
                    $hangHoa->tenHangHoa,
                    $hangHoa->donViTinh,
                    $hangHoa->trangThai,
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem, 'UTF-8'), mb_strtolower($tuKhoa, 'UTF-8'))
                    || str_contains(mb_strtolower($noiDungKhongDau, 'UTF-8'), mb_strtolower($tuKhoaKhongDau, 'UTF-8'));
            })->values();
        }

        if ($tuKhoa === '' && session()->has('hangHoaMoi')) {
            $idMoi = session('hangHoaMoi');

            $hangHoas = $hangHoas->sortBy(function ($hangHoa) use ($idMoi) {
                return $hangHoa->idHangHoa == $idMoi ? -1 : $hangHoa->idHangHoa;
            })->values();
        }

        return view('admin.hang_hoa.index', compact('danhMucHang', 'hangHoas'));
    }

    public function create(int $idDanhMucHang)
    {
        $danhMucHang = DanhMucHang::findOrFail($idDanhMucHang);

        return view('admin.hang_hoa.create', compact('danhMucHang'));
    }

    public function store(Request $request, int $idDanhMucHang)
    {
        $danhMucHang = DanhMucHang::findOrFail($idDanhMucHang);

        $request->validate([
            'tenHangHoa' => 'required|string|max:255',
            'donViTinh' => 'required|string|max:100',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenHangHoa.required' => 'Vui lòng nhập tên hàng hóa.',
            'donViTinh.required' => 'Vui lòng nhập đơn vị tính.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $daTonTai = HangHoa::where('idDanhMucHang', $idDanhMucHang)
            ->whereNull('idNhom')
            ->where('tenHangHoa', trim($request->tenHangHoa))
            ->where('donViTinh', trim($request->donViTinh))
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with('error', 'Hàng hóa này đã tồn tại trong danh mục.');
        }

        $hangHoa = HangHoa::create([
            'idDanhMucHang' => $danhMucHang->idDanhMucHang,
            'idNhom' => null,
            'tenHangHoa' => trim($request->tenHangHoa),
            'donViTinh' => trim($request->donViTinh),
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/danh-muc-hang/' . $idDanhMucHang . '/hang-hoa')
            ->with('success', 'Thêm hàng hóa thành công.')
            ->with('hangHoaMoi', $hangHoa->idHangHoa);
    }

    public function edit(int $idHangHoa)
    {
        $hangHoa = HangHoa::with('danhMucHang')
            ->whereNull('idNhom')
            ->findOrFail($idHangHoa);

        return view('admin.hang_hoa.edit', compact('hangHoa'));
    }

    public function update(Request $request, int $idHangHoa)
    {
        $hangHoa = HangHoa::whereNull('idNhom')
            ->findOrFail($idHangHoa);

        $request->validate([
            'tenHangHoa' => 'required|string|max:255',
            'donViTinh' => 'required|string|max:100',
            'trangThai' => 'required|string|max:255',
        ], [
            'tenHangHoa.required' => 'Vui lòng nhập tên hàng hóa.',
            'donViTinh.required' => 'Vui lòng nhập đơn vị tính.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $daTonTai = HangHoa::where('idDanhMucHang', $hangHoa->idDanhMucHang)
            ->whereNull('idNhom')
            ->where('tenHangHoa', trim($request->tenHangHoa))
            ->where('donViTinh', trim($request->donViTinh))
            ->where('idHangHoa', '!=', $hangHoa->idHangHoa)
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with('error', 'Hàng hóa này đã tồn tại trong danh mục.');
        }

        $hangHoa->update([
            'tenHangHoa' => trim($request->tenHangHoa),
            'donViTinh' => trim($request->donViTinh),
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/danh-muc-hang/' . $hangHoa->idDanhMucHang . '/hang-hoa')
            ->with('success', 'Cập nhật hàng hóa thành công.');
    }

    public function doiTrangThai(int $idHangHoa)
    {
        $hangHoa = HangHoa::whereNull('idNhom')
            ->findOrFail($idHangHoa);

        $dangHoatDong = in_array($hangHoa->trangThai, [
            'Đang sử dụng'
        ]);

        if ($dangHoatDong) {
            $hangHoa->update([
                'trangThai' => 'Ngừng sử dụng',
            ]);

            return redirect('/admin/danh-muc-hang/' . $hangHoa->idDanhMucHang . '/hang-hoa')
                ->with('success', 'Ngừng sử dụng hàng hóa thành công.');
        }

        $hangHoa->update([
            'trangThai' => 'Đang sử dụng',
        ]);

        return redirect('/admin/danh-muc-hang/' . $hangHoa->idDanhMucHang . '/hang-hoa')
            ->with('success', 'Mở sử dụng hàng hóa thành công.');
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($coDau, $khongDau, $chuoi);
    }
}