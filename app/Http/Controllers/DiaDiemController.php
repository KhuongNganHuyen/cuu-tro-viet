<?php

namespace App\Http\Controllers;

use App\Models\DiaDiem;
use Illuminate\Http\Request;

class DiaDiemController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $diaDiems = DiaDiem::orderBy('idDiaDiem', 'asc')->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $diaDiems = $diaDiems->filter(function ($diaDiem) use ($tuKhoa, $tuKhoaKhongDau) {
                $noiDungTimKiem = implode(' ', [
                    $diaDiem->tinhThanh,
                    $diaDiem->phuongXa,
                    $diaDiem->chiTietDiaDiem,
                    $diaDiem->viDo,
                    $diaDiem->kinhDo,
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem), mb_strtolower($tuKhoa))
                    || str_contains(mb_strtolower($noiDungKhongDau), mb_strtolower($tuKhoaKhongDau));
            })->values();
        }

        if ($tuKhoa === '' && session()->has('diaDiemMoi')) {
            $idMoi = session('diaDiemMoi');

            $diaDiems = $diaDiems->sortBy(function ($diaDiem) use ($idMoi) {
                return $diaDiem->idDiaDiem == $idMoi ? -1 : $diaDiem->idDiaDiem;
            })->values();
        }

        return view('admin.dia_diem.index', compact('diaDiems'));
    }

    public function create()
    {
        return view('admin.dia_diem.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'nullable|string|max:255',
            'chiTietDiaDiem' => 'nullable|string|max:255',
            'viDo' => 'nullable|numeric',
            'kinhDo' => 'nullable|numeric',
        ], [
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
        ]);

        $diaDiem = DiaDiem::create([
            'tinhThanh' => $request->tinhThanh,
            'phuongXa' => $request->phuongXa,
            'chiTietDiaDiem' => $request->chiTietDiaDiem,
            'viDo' => $request->viDo,
            'kinhDo' => $request->kinhDo,
        ]);

        return redirect('/admin/dia-diem')
            ->with('success', 'Thêm địa điểm thành công.')
            ->with('diaDiemMoi', $diaDiem->idDiaDiem);
    }

    public function show(int $id)
    {
        $diaDiem = DiaDiem::findOrFail($id);

        return view('admin.dia_diem.show', compact('diaDiem'));
    }

    public function edit(int $id)
    {
        $diaDiem = DiaDiem::findOrFail($id);

        return view('admin.dia_diem.edit', compact('diaDiem'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'nullable|string|max:255',
            'chiTietDiaDiem' => 'nullable|string|max:255',
            'viDo' => 'nullable|numeric',
            'kinhDo' => 'nullable|numeric',
        ], [
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',
        ]);

        $diaDiem = DiaDiem::findOrFail($id);

        $diaDiem->update($request->only([
            'tinhThanh',
            'phuongXa',
            'chiTietDiaDiem',
            'viDo',
            'kinhDo',
        ]));

        return redirect('/admin/dia-diem')
            ->with('success', 'Cập nhật địa điểm thành công.');
    }

    public function destroy(int $id)
    {
        $diaDiem = DiaDiem::findOrFail($id);

        $dangDuocSuDung =
            \App\Models\NhomTinhNguyen::where('idDiaDiem', $id)->exists()
            || \App\Models\ChienDichCuuTro::where('idDiaDiem', $id)->exists()
            || \App\Models\YeuCauCuuTro::where('idDiaDiem', $id)->exists()
            || \App\Models\ChiTietPhanPhoi::where('idDiaDiem', $id)->exists();

        if ($dangDuocSuDung) {
            return redirect('/admin/dia-diem/' . $id)
                ->with('error', 'Không thể xóa địa điểm này vì đang được sử dụng trong dữ liệu khác.');
        }

        $diaDiem->delete();

        return redirect('/admin/dia-diem')
            ->with('success', 'Xóa địa điểm thành công.');
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