<?php

namespace App\Http\Controllers;

use App\Models\SuKienCuuTro;
use App\Models\ThongBao;
use Illuminate\Http\Request;

class SuKienCuuTroController extends Controller
{
    public function index(Request $request)
    {
        $loaiDangChon = $request->input('loai', 'Khẩn cấp');

        if (!in_array($loaiDangChon, ['Khẩn cấp', 'Thường nhật'])) {
            $loaiDangChon = 'Khẩn cấp';
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $suKiens = SuKienCuuTro::where('loaiSuKien', $loaiDangChon)->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $suKiens = $suKiens->filter(function ($suKien) use ($tuKhoa, $tuKhoaKhongDau) {
                $noiDungTimKiem = implode(' ', [
                    $suKien->idSuKien,
                    $suKien->tenSuKien,
                    $suKien->loaiSuKien,
                    $suKien->moTa,
                    $suKien->trangThai,
                    $suKien->ngayTao,
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem, 'UTF-8'), mb_strtolower($tuKhoa, 'UTF-8'))
                    || str_contains(mb_strtolower($noiDungKhongDau, 'UTF-8'), mb_strtolower($tuKhoaKhongDau, 'UTF-8'));
            })->values();
        }

        if ($loaiDangChon === 'Khẩn cấp') {
            $suKiens = $suKiens->sortByDesc('idSuKien')->values();
        } else {
            $suKiens = $suKiens->sortBy('idSuKien')->values();

            if ($tuKhoa === '' && session()->has('suKienMoi')) {
                $idMoi = session('suKienMoi');

                $suKiens = $suKiens->sortBy(function ($suKien) use ($idMoi) {
                    return $suKien->idSuKien == $idMoi ? -1 : $suKien->idSuKien;
                })->values();
            }
        }

        $soKhanCap = SuKienCuuTro::where('loaiSuKien', 'Khẩn cấp')->count();
        $soThuongNhat = SuKienCuuTro::where('loaiSuKien', 'Thường nhật')->count();

        return view('admin.su_kien_cuu_tro.index', compact(
            'suKiens',
            'loaiDangChon',
            'tuKhoa',
            'soKhanCap',
            'soThuongNhat'
        ));
    }

    public function create()
    {
        return view('admin.su_kien_cuu_tro.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenSuKien' => 'required|string|max:255',
            'loaiSuKien' => 'required|string|in:Khẩn cấp,Thường nhật',
            'moTa' => 'nullable|string',
            'trangThai' => 'required|string|max:50',
        ], [
            'tenSuKien.required' => 'Vui lòng nhập tên sự kiện cứu trợ.',
            'loaiSuKien.required' => 'Vui lòng chọn loại sự kiện.',
            'loaiSuKien.in' => 'Loại sự kiện không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $this->kiemTraTrangThaiTheoLoai($request->loaiSuKien, $request->trangThai);

        $suKien = SuKienCuuTro::create([
            'tenSuKien' => trim($request->tenSuKien),
            'loaiSuKien' => $request->loaiSuKien,
            'moTa' => $request->moTa,
            'trangThai' => $request->trangThai,
            'ngayTao' => now(),
        ]);

        if ($suKien->loaiSuKien === 'Khẩn cấp') {
            ThongBao::create([
                'tieuDe' => 'Sự kiện khẩn cấp mới: ' . $suKien->tenSuKien,
                'noiDung' => $suKien->moTa,
                'doiTuong' => 'Tất cả',
                'nguoiTao' => 'Quản trị viên',
                'idNguoiNhan' => null,
                'anhDaiDien' => null,
                'hinhAnh' => null,
                'duongDan' => '/thong-bao',
                'thoiGianTao' => now(),
                'trangThai' => 'Hiển thị',
            ]);
        }

        return redirect('/admin/su-kien-cuu-tro?loai=' . urlencode($request->loaiSuKien))
            ->with('success', 'Thêm sự kiện cứu trợ thành công.')
            ->with('suKienMoi', $suKien->idSuKien);
    }

    public function edit(int $id)
    {
        $suKien = SuKienCuuTro::findOrFail($id);

        return view('admin.su_kien_cuu_tro.edit', compact('suKien'));
    }

    public function update(Request $request, int $id)
    {
        $suKien = SuKienCuuTro::findOrFail($id);

        $request->validate([
            'tenSuKien' => 'required|string|max:255',
            'loaiSuKien' => 'required|string|in:Khẩn cấp,Thường nhật',
            'moTa' => 'nullable|string',
            'trangThai' => 'required|string|max:50',
        ], [
            'tenSuKien.required' => 'Vui lòng nhập tên sự kiện cứu trợ.',
            'loaiSuKien.required' => 'Vui lòng chọn loại sự kiện.',
            'loaiSuKien.in' => 'Loại sự kiện không hợp lệ.',
            'trangThai.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $this->kiemTraTrangThaiTheoLoai($request->loaiSuKien, $request->trangThai);

        $suKien->update([
            'tenSuKien' => trim($request->tenSuKien),
            'loaiSuKien' => $request->loaiSuKien,
            'moTa' => $request->moTa,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/admin/su-kien-cuu-tro?loai=' . urlencode($request->loaiSuKien))
            ->with('success', 'Cập nhật sự kiện cứu trợ thành công.');
    }

    public function destroy(int $id)
    {
        $suKien = SuKienCuuTro::findOrFail($id);

        $loaiSuKien = $suKien->loaiSuKien;

        $dangDuocSuDung = \App\Models\ChienDichCuuTro::where('idSuKien', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/su-kien-cuu-tro?loai=' . urlencode($loaiSuKien))
                ->with('error', 'Không thể xóa sự kiện này vì đang được sử dụng trong chiến dịch cứu trợ. Bạn có thể chuyển trạng thái sang Ẩn.');
        }

        $suKien->delete();

        return redirect('/admin/su-kien-cuu-tro?loai=' . urlencode($loaiSuKien))
            ->with('success', 'Xóa sự kiện cứu trợ thành công.');
    }

    private function kiemTraTrangThaiTheoLoai(string $loaiSuKien, string $trangThai): void
    {
        $trangThaiHopLe = [
            'Khẩn cấp' => ['Sắp diễn ra', 'Đang diễn ra', 'Đã kết thúc', 'Ẩn'],
            'Thường nhật' => ['Đang diễn ra', 'Ẩn'],
        ];

        if (!in_array($trangThai, $trangThaiHopLe[$loaiSuKien] ?? [])) {
            abort(422, 'Trạng thái không hợp lệ với loại sự kiện đã chọn.');
        }
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