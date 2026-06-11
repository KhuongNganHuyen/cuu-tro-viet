<?php

namespace App\Http\Controllers;

use App\Models\DanhMucHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhMucHangController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $danhMucHangs = DanhMucHang::orderBy('idDanhMucHang', 'asc')->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $danhMucHangs = $danhMucHangs->filter(function ($danhMucHang) use ($tuKhoa, $tuKhoaKhongDau) {
                $noiDungTimKiem = implode(' ', [
                    $danhMucHang->idDanhMucHang,
                    $danhMucHang->tenDanhMucHang,
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem, 'UTF-8'), mb_strtolower($tuKhoa, 'UTF-8'))
                    || str_contains(mb_strtolower($noiDungKhongDau, 'UTF-8'), mb_strtolower($tuKhoaKhongDau, 'UTF-8'));
            })->values();
        }

        if ($tuKhoa === '' && session()->has('danhMucHangMoi')) {
            $idMoi = session('danhMucHangMoi');

            $danhMucHangs = $danhMucHangs->sortBy(function ($danhMucHang) use ($idMoi) {
                return $danhMucHang->idDanhMucHang == $idMoi ? -1 : $danhMucHang->idDanhMucHang;
            })->values();
        }

        $soHangHoaTheoDanhMuc = DB::table('HangHoa')
            ->select('idDanhMucHang', DB::raw('COUNT(*) as tong'))
            ->groupBy('idDanhMucHang')
            ->pluck('tong', 'idDanhMucHang');

        return view('admin.danh_muc_hang.index', compact(
            'danhMucHangs',
            'soHangHoaTheoDanhMuc'
        ));
    }

    public function create()
    {
        return view('admin.danh_muc_hang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenDanhMucHang' => 'required|string|max:255',
        ], [
            'tenDanhMucHang.required' => 'Vui lòng nhập tên danh mục hàng.',
        ]);

        $danhMucHang = DanhMucHang::create([
            'tenDanhMucHang' => trim($request->tenDanhMucHang),
        ]);

        return redirect('/admin/danh-muc-hang')
            ->with('success', 'Thêm danh mục hàng thành công.')
            ->with('danhMucHangMoi', $danhMucHang->idDanhMucHang);
    }

    public function edit(int $id)
    {
        $danhMucHang = DanhMucHang::findOrFail($id);

        return view('admin.danh_muc_hang.edit', compact('danhMucHang'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tenDanhMucHang' => 'required|string|max:255',
        ], [
            'tenDanhMucHang.required' => 'Vui lòng nhập tên danh mục hàng.',
        ]);

        $danhMucHang = DanhMucHang::findOrFail($id);

        $danhMucHang->update([
            'tenDanhMucHang' => trim($request->tenDanhMucHang),
        ]);

        return redirect('/admin/danh-muc-hang')
            ->with('success', 'Cập nhật danh mục hàng thành công.');
    }

    public function destroy(int $id)
    {
        $danhMucHang = DanhMucHang::findOrFail($id);

        $dangDuocSuDung = \App\Models\HangHoa::where('idDanhMucHang', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/danh-muc-hang')
                ->with('error', 'Không thể xóa danh mục hàng này vì đang được sử dụng trong hàng hóa.');
        }

        $danhMucHang->delete();

        return redirect('/admin/danh-muc-hang')
            ->with('success', 'Xóa danh mục hàng thành công.');
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