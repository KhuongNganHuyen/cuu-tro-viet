<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\YeuCauCuuTro;
use App\Models\DiaDiem;
use Illuminate\Http\Request;

class UserYeuCauCuuTroController extends Controller
{
    public function index(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $yeuCaus = YeuCauCuuTro::with(['diaDiem', 'tiepNhans.nhom', 'tiepNhans.chienDich'])
            ->where('idNguoiGui', $idNguoiDung)
            ->orderBy('idYeuCau', 'desc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $yeuCaus = $yeuCaus->filter(function ($yeuCau) use ($tuKhoa, $tuKhoaKhongDau) {
                $diaDiem = $yeuCau->diaDiem;

                $noiDungTimKiem = implode(' ', [
                    $yeuCau->idYeuCau,
                    $yeuCau->tieuDeYeuCau,
                    $yeuCau->moTa,
                    $yeuCau->soNguoi,
                    $yeuCau->mucDoKhanCap,
                    $yeuCau->trangThai,
                    $yeuCau->thoiGianGui,
                    $diaDiem->chiTietDiaDiem ?? '',
                    $diaDiem->phuongXa ?? '',
                    $diaDiem->tinhThanh ?? '',
                ]);

                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains(mb_strtolower($noiDungTimKiem, 'UTF-8'), mb_strtolower($tuKhoa, 'UTF-8'))
                    || str_contains(mb_strtolower($noiDungKhongDau, 'UTF-8'), mb_strtolower($tuKhoaKhongDau, 'UTF-8'));
            })->values();
        }

        return view('user.yeu_cau_cuu_tro.index', compact('yeuCaus'));
    }

    public function create()
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $diaDiems = DiaDiem::orderBy('tinhThanh')
            ->orderBy('phuongXa')
            ->orderBy('chiTietDiaDiem')
            ->get();

        $diaDiemJson = $diaDiems->map(function ($diaDiem) {
            return [
                'idDiaDiem' => $diaDiem->idDiaDiem,
                'tinhThanh' => $diaDiem->tinhThanh,
                'phuongXa' => $diaDiem->phuongXa,
                'chiTietDiaDiem' => $diaDiem->chiTietDiaDiem,
                'viDo' => $diaDiem->viDo,
                'kinhDo' => $diaDiem->kinhDo,
            ];
        })->values()->toJson();

        return view('user.yeu_cau_cuu_tro.create', compact('diaDiems', 'diaDiemJson'));
    }

    public function store(Request $request)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $request->validate([
            'tieuDeYeuCau' => 'required|string|max:255',
            'moTa' => 'required|string',
            'soNguoi' => 'nullable|integer|min:1',
            'mucDoKhanCap' => 'required|string|max:255',

            'tinhThanh' => 'required|string|max:255',
            'phuongXa' => 'required|string|max:255',
            'chiTietDiaDiem' => 'required|string|max:255',
            'viDo' => 'required|numeric',
            'kinhDo' => 'required|numeric',

            'hinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'tieuDeYeuCau.required' => 'Vui lòng nhập tiêu đề yêu cầu.',
            'moTa.required' => 'Vui lòng nhập mô tả tình hình cần hỗ trợ.',
            'soNguoi.integer' => 'Số người phải là số.',
            'soNguoi.min' => 'Số người phải lớn hơn 0.',
            'mucDoKhanCap.required' => 'Vui lòng chọn mức độ khẩn cấp.',
            'tinhThanh.required' => 'Vui lòng nhập tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'hinhAnh.image' => 'Tệp tải lên phải là hình ảnh.',
            'hinhAnh.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'hinhAnh.max' => 'Hình ảnh không được vượt quá 4MB.',
        ]);

        $diaDiem = $this->layHoacTaoDiaDiem($request);

        $duongDanHinhAnh = null;

        if ($request->hasFile('hinhAnh')) {
            $duongDanHinhAnh = $request->file('hinhAnh')->store('yeu-cau-cuu-tro', 'public');
        }

        YeuCauCuuTro::create([
            'idNguoiGui' => $idNguoiDung,
            'idDiaDiem' => $diaDiem->idDiaDiem,
            'tieuDeYeuCau' => trim($request->tieuDeYeuCau),
            'moTa' => trim($request->moTa),
            'soNguoi' => $request->soNguoi,
            'mucDoKhanCap' => $request->mucDoKhanCap,
            'hinhAnh' => $duongDanHinhAnh,
            'trangThai' => 'Chờ tiếp nhận',
            'thoiGianGui' => now(),
        ]);

        return redirect('/user/yeu-cau-cuu-tro')
            ->with('success', 'Gửi yêu cầu cứu trợ thành công.');
    }

    public function show(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $yeuCau = YeuCauCuuTro::with([
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich'
            ])
            ->where('idNguoiGui', $idNguoiDung)
            ->where('idYeuCau', $idYeuCau)
            ->firstOrFail();

        return view('user.yeu_cau_cuu_tro.show', compact('yeuCau'));
    }

    private function layHoacTaoDiaDiem(Request $request): DiaDiem
    {
        $tinhThanh = trim($request->tinhThanh);
        $phuongXa = trim($request->phuongXa);
        $chiTietDiaDiem = trim($request->chiTietDiaDiem);

        $diaDiem = DiaDiem::where('tinhThanh', $tinhThanh)
            ->where('phuongXa', $phuongXa)
            ->where('chiTietDiaDiem', $chiTietDiaDiem)
            ->first();

        if ($diaDiem) {
            return $diaDiem;
        }

        return DiaDiem::create([
            'tinhThanh' => $tinhThanh,
            'phuongXa' => $phuongXa,
            'chiTietDiaDiem' => $chiTietDiaDiem,
            'viDo' => $request->viDo,
            'kinhDo' => $request->kinhDo,
        ]);
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