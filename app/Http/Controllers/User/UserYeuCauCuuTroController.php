<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ThongBao;
use App\Models\DiaDiem;
use App\Models\TiepNhanYeuCau;
use App\Models\YeuCauCuuTro;
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
        $mucDoDangChon = trim((string) $request->input('mucDoKhanCap'));
        $trangThaiDangChon = trim((string) $request->input('trangThai'));

        $yeuCaus = YeuCauCuuTro::with([
                'diaDiem',
                'tiepNhans.nhom',
                'tiepNhans.chienDich',
            ])
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

                return str_contains(
                    mb_strtolower($noiDungTimKiem, 'UTF-8'),
                    mb_strtolower($tuKhoa, 'UTF-8')
                ) || str_contains(
                    mb_strtolower($noiDungKhongDau, 'UTF-8'),
                    mb_strtolower($tuKhoaKhongDau, 'UTF-8')
                );
            })->values();
        }

        if ($mucDoDangChon !== '') {
            $yeuCaus = $yeuCaus->filter(function ($yeuCau) use ($mucDoDangChon) {
                return $yeuCau->mucDoKhanCap === $mucDoDangChon;
            })->values();
        }

        if ($trangThaiDangChon !== '') {
            $yeuCaus = $yeuCaus->filter(function ($yeuCau) use ($trangThaiDangChon) {
                return $yeuCau->trangThai === $trangThaiDangChon;
            })->values();
        }

        return view('user.yeu_cau_cuu_tro.index', compact(
            'yeuCaus',
            'mucDoDangChon',
            'trangThaiDangChon'
        ));
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

        return view('user.yeu_cau_cuu_tro.create', compact(
            'diaDiems',
            'diaDiemJson'
        ));
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
            'mucDoKhanCap' => 'required|string|in:Thấp,Trung bình,Cao,Khẩn cấp',

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
            'mucDoKhanCap.in' => 'Mức độ khẩn cấp không hợp lệ.',

            'tinhThanh.required' => 'Vui lòng chọn tỉnh/thành.',
            'phuongXa.required' => 'Vui lòng chọn phường/xã.',
            'chiTietDiaDiem.required' => 'Vui lòng nhập địa chỉ chi tiết.',

            'viDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy vĩ độ.',
            'viDo.numeric' => 'Vĩ độ phải là số.',
            'kinhDo.required' => 'Vui lòng chọn vị trí trên bản đồ để lấy kinh độ.',
            'kinhDo.numeric' => 'Kinh độ phải là số.',

            'hinhAnh.image' => 'Tệp tải lên phải là hình ảnh.',
            'hinhAnh.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'hinhAnh.max' => 'Hình ảnh không được vượt quá 4MB.',
        ]);

        $diaDiem = $this->layHoacTaoDiaDiem($request);

        $duongDanHinhAnh = null;

        if ($request->hasFile('hinhAnh')) {
            $duongDanHinhAnh = $request->file('hinhAnh')
                ->store('yeu-cau-cuu-tro', 'public');
        }

        $yeuCau = YeuCauCuuTro::create([
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

        $yeuCau->load('nguoiGui');
        
        ThongBao::create([
            'tieuDe' => 'Yêu cầu cứu trợ mới: ' . $yeuCau->tieuDeYeuCau,
            'noiDung' => implode("\n", [
                'Mức độ khẩn cấp: ' . $yeuCau->mucDoKhanCap,
                $yeuCau->moTa,
            ]),
            'doiTuong' => 'Tất cả',
            'nguoiTao' => $yeuCau->nguoiGui->hoTen ?? 'Người dân',
            'idNguoiNhan' => null,
            'anhDaiDien' => $yeuCau->nguoiGui->anhDaiDien ?? null,
            'hinhAnh' => $yeuCau->hinhAnh,
            'duongDan' => '/thong-bao',
            'thoiGianTao' => now(),
            'trangThai' => 'Hiển thị',
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
                'tiepNhans.chienDich',
            ])
            ->where('idNguoiGui', $idNguoiDung)
            ->where('idYeuCau', $idYeuCau)
            ->firstOrFail();

        return view('user.yeu_cau_cuu_tro.show', compact('yeuCau'));
    }

    /**
     * Người dùng chỉ được hủy khi yêu cầu chưa có nhóm tiếp nhận.
     */
    public function huyYeuCau(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $yeuCau = YeuCauCuuTro::where('idNguoiGui', $idNguoiDung)
            ->where('idYeuCau', $idYeuCau)
            ->firstOrFail();

        if ($yeuCau->trangThai !== 'Chờ tiếp nhận') {
            return back()->with(
                'error',
                'Chỉ có thể hủy yêu cầu đang chờ tiếp nhận.'
            );
        }

        $daCoNhomTiepNhan = TiepNhanYeuCau::where(
            'idYeuCau',
            $idYeuCau
        )->exists();

        if ($daCoNhomTiepNhan) {
            return back()->with(
                'error',
                'Yêu cầu đã có nhóm tiếp nhận nên không thể hủy.'
            );
        }

        $yeuCau->update([
            'trangThai' => 'Đã hủy',
        ]);

        return redirect('/user/yeu-cau-cuu-tro/' . $idYeuCau)
            ->with('success', 'Hủy yêu cầu cứu trợ thành công.');
    }

    public function canThemHoTro(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $yeuCau = YeuCauCuuTro::with('tiepNhans')
            ->where('idYeuCau', $idYeuCau)
            ->where('idNguoiGui', $idNguoiDung)
            ->firstOrFail();

        if (in_array($yeuCau->trangThai, ['Hoàn thành', 'Đã hủy'], true)) {
            return back()
                ->with('error', 'Yêu cầu này không thể chuyển sang trạng thái cần thêm hỗ trợ.');
        }

        $soDongCapNhat = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)
            ->where('trangThai', 'Đã tiếp nhận')
            ->update([
                'trangThai' => 'Cần thêm hỗ trợ',
            ]);

        if ($soDongCapNhat <= 0) {
            return back()
                ->with('error', 'Không có lượt tiếp nhận nào có thể chuyển sang cần thêm hỗ trợ.');
        }

        $yeuCau->update([
            'trangThai' => 'Cần thêm hỗ trợ',
        ]);

        return back()
            ->with('success', 'Đã báo yêu cầu cần thêm hỗ trợ.');
    }

    public function thuHoiCanThemHoTro(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $yeuCau = YeuCauCuuTro::with('tiepNhans')
            ->where('idYeuCau', $idYeuCau)
            ->where('idNguoiGui', $idNguoiDung)
            ->firstOrFail();

        if (in_array($yeuCau->trangThai, ['Hoàn thành', 'Đã hủy'], true)) {
            return back()
                ->with('error', 'Yêu cầu này không thể thu hồi trạng thái cần thêm hỗ trợ.');
        }

        $soDongCapNhat = TiepNhanYeuCau::where('idYeuCau', $idYeuCau)
            ->where('trangThai', 'Cần thêm hỗ trợ')
            ->update([
                'trangThai' => 'Đã tiếp nhận',
            ]);

        if ($soDongCapNhat <= 0) {
            return back()
                ->with('error', 'Không có lượt tiếp nhận nào đang ở trạng thái cần thêm hỗ trợ.');
        }

        $yeuCau->update([
            'trangThai' => 'Đã tiếp nhận',
        ]);

        return back()
            ->with('success', 'Đã thu hồi trạng thái cần thêm hỗ trợ.');
    }

    public function xacNhanHoanThanh(int $idYeuCau)
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập.');
        }

        $yeuCau = YeuCauCuuTro::where('idNguoiGui', $idNguoiDung)
            ->where('idYeuCau', $idYeuCau)
            ->firstOrFail();

        if (in_array($yeuCau->trangThai, ['Chờ tiếp nhận', 'Đã hủy', 'Hoàn thành'])) {
            return back()->with(
                'error',
                'Yêu cầu hiện không thể xác nhận hoàn thành.'
            );
        }

        $daCoNhomTiepNhan = TiepNhanYeuCau::where(
            'idYeuCau',
            $idYeuCau
        )->exists();

        if (!$daCoNhomTiepNhan) {
            return back()->with(
                'error',
                'Yêu cầu chưa có nhóm tiếp nhận.'
            );
        }

        $yeuCau->update([
            'trangThai' => 'Hoàn thành',
        ]);

        return redirect('/user/yeu-cau-cuu-tro/' . $idYeuCau)
            ->with('success', 'Đã xác nhận yêu cầu được hỗ trợ hoàn thành.');
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
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ',
            'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ',
            'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($coDau, $khongDau, $chuoi);
    }
}