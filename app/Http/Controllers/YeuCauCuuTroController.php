<?php

namespace App\Http\Controllers;

use App\Models\YeuCauCuuTro;
use Illuminate\Http\Request;

class YeuCauCuuTroController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = trim((string) $request->input('tuKhoa'));
        $trangThaiDangChon = trim((string) $request->input('trangThai'));
        $mucDoDangChon = trim((string) $request->input('mucDoKhanCap'));

        $query = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
            ->orderBy('idYeuCau', 'desc');

        if ($trangThaiDangChon !== '') {
            $query->where('trangThai', $trangThaiDangChon);
        }

        if ($mucDoDangChon !== '') {
            $query->where('mucDoKhanCap', $mucDoDangChon);
        }

        $yeuCaus = $query->get();

        if ($tuKhoa !== '') {
            $tuKhoaThuong = mb_strtolower($tuKhoa, 'UTF-8');
            $tuKhoaKhongDau = $this->boDauTiengViet($tuKhoa);

            $yeuCaus = $yeuCaus->filter(function ($yeuCau) use ($tuKhoaThuong, $tuKhoaKhongDau) {
                $diaDiem = $yeuCau->diaDiem;
                $nguoiGui = $yeuCau->nguoiGui;

                $diaChi = implode(' ', [
                    $diaDiem->chiTietDiaDiem ?? '',
                    $diaDiem->phuongXa ?? '',
                    $diaDiem->tinhThanh ?? '',
                ]);

                $noiDungTimKiem = implode(' ', [
                    $yeuCau->idYeuCau,
                    $yeuCau->tieuDeYeuCau ?? '',
                    $yeuCau->moTa ?? '',
                    $yeuCau->soNguoi ?? '',
                    $yeuCau->soHoDan ?? '',
                    $yeuCau->mucDoKhanCap ?? '',
                    $yeuCau->trangThai ?? '',
                    $yeuCau->thoiGianGui ?? '',
                    $nguoiGui->hoTen ?? '',
                    $nguoiGui->tenDangNhap ?? '',
                    $nguoiGui->email ?? '',
                    $nguoiGui->sdt ?? '',
                    $diaChi,
                ]);

                $noiDungThuong = mb_strtolower($noiDungTimKiem, 'UTF-8');
                $noiDungKhongDau = $this->boDauTiengViet($noiDungTimKiem);

                return str_contains($noiDungThuong, $tuKhoaThuong)
                    || str_contains($noiDungKhongDau, $tuKhoaKhongDau);
            })->values();
        }

        return view('admin.yeu_cau_cuu_tro.index', compact(
            'yeuCaus',
            'tuKhoa',
            'trangThaiDangChon',
            'mucDoDangChon'
        ));
    }

    public function show(int $id)
    {
        $yeuCau = YeuCauCuuTro::with([
                'nguoiGui',
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
            ->findOrFail($id);

        $tiepNhans = $yeuCau->tiepNhans
            ->sortBy('idTiepNhan')
            ->values();

        return view('admin.yeu_cau_cuu_tro.show', compact(
            'yeuCau',
            'tiepNhans'
        ));
    }

    public function huyYeuCau(Request $request, int $id)
    {
        $yeuCau = YeuCauCuuTro::with('tiepNhans')->findOrFail($id);

        if ($yeuCau->trangThai === 'Hoàn thành') {
            return back()->with('error', 'Không thể hủy yêu cầu đã hoàn thành.');
        }

        if ($yeuCau->trangThai === 'Đã hủy') {
            return back()->with('error', 'Yêu cầu này đã được hủy trước đó.');
        }

        $request->validate([
            'lyDoHuy' => ['required', 'string', 'max:500'],
        ], [
            'lyDoHuy.required' => 'Vui lòng nhập lý do hủy yêu cầu.',
            'lyDoHuy.max' => 'Lý do hủy không được vượt quá 500 ký tự.',
        ]);

        $yeuCau->trangThai = 'Đã hủy';

        if (array_key_exists('lyDoHuy', $yeuCau->getAttributes()) || $yeuCau->isFillable('lyDoHuy')) {
            $yeuCau->lyDoHuy = $request->lyDoHuy;
        }

        $yeuCau->save();

        return redirect('/admin/yeu-cau-cuu-tro/' . $yeuCau->idYeuCau)
            ->with('success', 'Đã hủy yêu cầu cứu trợ.');
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