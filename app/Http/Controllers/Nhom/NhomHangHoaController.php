<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\DanhMucHang;
use App\Models\HangHoa;
use App\Models\NhomTinhNguyen;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;

class NhomHangHoaController extends Controller
{
    private function kiemTraThanhVien(int $idNhom): array
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')
                    ->with('error', 'Vui lòng đăng nhập để tiếp tục.'),
            ];
        }

        $nhom = NhomTinhNguyen::with([
                'nhomTruong',
                'diaDiem',
            ])
            ->findOrFail($idNhom);

        if ($nhom->trangThai !== 'Đang hoạt động') {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with(
                        'error',
                        'Nhóm này chưa được phép hoạt động hoặc đã bị khóa.'
                    ),
            ];
        }

        $thanhVien = ThanhVienNhom::where('idNhom', $idNhom)
            ->where('idNguoiDung', $idNguoiDung)
            ->first();

        if (!$thanhVien) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/user/nhom-cua-toi')
                    ->with(
                        'error',
                        'Bạn không thuộc nhóm tình nguyện này.'
                    ),
            ];
        }

        $laNhomTruong =
            $thanhVien->vaiTro === 'Nhóm trưởng'
            || $nhom->idNhomTruong == $idNguoiDung;

        return [
            'hopLe' => true,
            'nhom' => $nhom,
            'thanhVien' => $thanhVien,
            'laNhomTruong' => $laNhomTruong,
        ];
    }

    public function index(Request $request, int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];
        $tuKhoa = trim((string) $request->input('tuKhoa'));

        $idDanhMucDangChon = $request->filled('idDanhMucHang')
            ? (int) $request->input('idDanhMucHang')
            : null;

        $donViTinhDangChon = trim(
            (string) $request->input('donViTinh')
        );

        $phamViDangChon = trim(
            (string) $request->input('phamVi')
        );

        $trangThaiDangChon = trim(
            (string) $request->input('trangThai')
        );

        $hangHoaQuery = HangHoa::with('danhMucHang')
            ->where(function ($query) use ($idNhom) {
                $query->whereNull('idNhom')
                    ->orWhere('idNhom', $idNhom);
            });

        if ($idDanhMucDangChon) {
            $hangHoaQuery->where(
                'idDanhMucHang',
                $idDanhMucDangChon
            );
        }

        if ($donViTinhDangChon !== '') {
            $hangHoaQuery->where(
                'donViTinh',
                $donViTinhDangChon
            );
        }

        if ($phamViDangChon === 'he-thong') {
            $hangHoaQuery->whereNull('idNhom');
        }

        if ($phamViDangChon === 'nhom') {
            $hangHoaQuery->where(
                'idNhom',
                $idNhom
            );
        }

        if ($trangThaiDangChon !== '') {
            $hangHoaQuery->where(
                'trangThai',
                $trangThaiDangChon
            );
        }

        $hangHoas = $hangHoaQuery
            ->orderByRaw(
                'CASE WHEN idNhom = ? THEN 0 ELSE 1 END',
                [$idNhom]
            )
            ->orderBy('idHangHoa', 'asc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaThuong = mb_strtolower(
                $tuKhoa,
                'UTF-8'
            );

            $tuKhoaKhongDau = $this->boDauTiengViet(
                $tuKhoa
            );

            $hangHoas = $hangHoas
                ->filter(function ($hangHoa) use (
                    $tuKhoaThuong,
                    $tuKhoaKhongDau
                ) {
                    $phamVi = is_null($hangHoa->idNhom)
                        ? 'Hệ thống dùng chung'
                        : 'Hàng hóa của nhóm';

                    $noiDung = implode(' ', [
                        $hangHoa->idHangHoa,
                        $hangHoa->tenHangHoa,
                        $hangHoa->donViTinh,
                        $hangHoa->trangThai,
                        $hangHoa->danhMucHang->tenDanhMucHang ?? '',
                        $phamVi,
                    ]);

                    return str_contains(
                        mb_strtolower($noiDung, 'UTF-8'),
                        $tuKhoaThuong
                    ) || str_contains(
                        $this->boDauTiengViet($noiDung),
                        $tuKhoaKhongDau
                    );
                })
                ->values();
        }

        $hangHoas = $hangHoas
            ->sortBy(function ($hangHoa) use ($idNhom) {
                $laHangMoi = session('hangHoaMoi') == $hangHoa->idHangHoa
                    ? 0
                    : 1;

                $thuTuPhamVi = (int) $hangHoa->idNhom === (int) $idNhom
                    ? 0
                    : 1;

                return sprintf(
                    '%d-%d-%010d',
                    $laHangMoi,
                    $thuTuPhamVi,
                    (int) $hangHoa->idHangHoa
                );
            })
            ->values();

        $danhMucHangs = DanhMucHang::orderBy(
                'tenDanhMucHang',
                'asc'
            )
            ->get();

        /*
        * Lấy danh sách đơn vị tính từ các hàng mà nhóm được phép nhìn thấy.
        */
        $donViTinhs = HangHoa::where(function ($query) use ($idNhom) {
                $query->whereNull('idNhom')
                    ->orWhere('idNhom', $idNhom);
            })
            ->whereNotNull('donViTinh')
            ->where('donViTinh', '!=', '')
            ->select('donViTinh')
            ->distinct()
            ->orderBy('donViTinh', 'asc')
            ->pluck('donViTinh');

        return view('nhom.hang_hoa.index', compact(
            'nhom',
            'laNhomTruong',
            'hangHoas',
            'danhMucHangs',
            'donViTinhs',
            'idDanhMucDangChon',
            'donViTinhDangChon',
            'phamViDangChon',
            'trangThaiDangChon'
        ));
    }

    public function create(int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/hang-hoa')
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền thêm hàng hóa.'
                );
        }

        $nhom = $kiemTra['nhom'];

        $danhMucHangs = DanhMucHang::orderBy(
                'tenDanhMucHang',
                'asc'
            )
            ->get();

        return view('nhom.hang_hoa.create', compact(
            'nhom',
            'danhMucHangs'
        ));
    }

    public function store(Request $request, int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/hang-hoa')
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền thêm hàng hóa.'
                );
        }

        $request->validate([
            'idDanhMucHang' => 'required|exists:DanhMucHang,idDanhMucHang',
            'tenHangHoa' => 'required|string|max:255',
            'donViTinh' => 'required|string|max:100',
        ], [
            'idDanhMucHang.required' => 'Vui lòng chọn danh mục hàng.',
            'idDanhMucHang.exists' => 'Danh mục hàng không hợp lệ.',
            'tenHangHoa.required' => 'Vui lòng nhập tên hàng hóa.',
            'donViTinh.required' => 'Vui lòng nhập đơn vị tính.',
        ]);

        $tenHangHoa = trim($request->tenHangHoa);
        $donViTinh = trim($request->donViTinh);

        $daTonTai = HangHoa::where('idNhom', $idNhom)
            ->where(
                'idDanhMucHang',
                $request->idDanhMucHang
            )
            ->whereRaw(
                'LOWER(tenHangHoa) = ?',
                [mb_strtolower($tenHangHoa, 'UTF-8')]
            )
            ->whereRaw(
                'LOWER(donViTinh) = ?',
                [mb_strtolower($donViTinh, 'UTF-8')]
            )
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Hàng hóa này đã tồn tại trong danh mục của nhóm.'
                );
        }

        $hangHoa = HangHoa::create([
            'idDanhMucHang' => $request->idDanhMucHang,
            'idNhom' => $idNhom,
            'tenHangHoa' => $tenHangHoa,
            'donViTinh' => $donViTinh,
            'trangThai' => 'Đang sử dụng',
        ]);

        return redirect('/nhom/' . $idNhom . '/hang-hoa')
            ->with('success', 'Thêm hàng hóa của nhóm thành công.')
            ->with('hangHoaMoi', $hangHoa->idHangHoa);
    }

    public function edit(int $idNhom, int $idHangHoa)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/hang-hoa')
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền sửa hàng hóa.'
                );
        }

        $nhom = $kiemTra['nhom'];

        /*
         * Chỉ lấy hàng hóa thuộc chính nhóm hiện tại.
         * Không thể sửa hàng chung hoặc hàng của nhóm khác.
         */
        $hangHoa = HangHoa::with('danhMucHang')
            ->where('idHangHoa', $idHangHoa)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        $danhMucHangs = DanhMucHang::orderBy(
                'tenDanhMucHang',
                'asc'
            )
            ->get();

        return view('nhom.hang_hoa.edit', compact(
            'nhom',
            'hangHoa',
            'danhMucHangs'
        ));
    }

    public function update(
        Request $request,
        int $idNhom,
        int $idHangHoa
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/hang-hoa')
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền sửa hàng hóa.'
                );
        }

        $hangHoa = HangHoa::where('idHangHoa', $idHangHoa)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        $request->validate([
            'idDanhMucHang' =>
                'required|exists:DanhMucHang,idDanhMucHang',
            'tenHangHoa' =>
                'required|string|max:255',
            'donViTinh' =>
                'required|string|max:100',
            'trangThai' =>
                'required|string|in:Đang sử dụng,Ngừng sử dụng',
        ], [
            'idDanhMucHang.required' =>
                'Vui lòng chọn danh mục hàng.',
            'idDanhMucHang.exists' =>
                'Danh mục hàng không hợp lệ.',
            'tenHangHoa.required' =>
                'Vui lòng nhập tên hàng hóa.',
            'donViTinh.required' =>
                'Vui lòng nhập đơn vị tính.',
            'trangThai.required' =>
                'Vui lòng chọn trạng thái.',
            'trangThai.in' =>
                'Trạng thái hàng hóa không hợp lệ.',
        ]);

        $tenHangHoa = trim($request->tenHangHoa);
        $donViTinh = trim($request->donViTinh);

        $daTonTai = HangHoa::where('idNhom', $idNhom)
            ->where(
                'idDanhMucHang',
                $request->idDanhMucHang
            )
            ->whereRaw(
                'LOWER(tenHangHoa) = ?',
                [mb_strtolower($tenHangHoa, 'UTF-8')]
            )
            ->whereRaw(
                'LOWER(donViTinh) = ?',
                [mb_strtolower($donViTinh, 'UTF-8')]
            )
            ->where('idHangHoa', '!=', $idHangHoa)
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Hàng hóa này đã tồn tại trong danh mục của nhóm.'
                );
        }

        $hangHoa->update([
            'idDanhMucHang' => $request->idDanhMucHang,
            'tenHangHoa' => $tenHangHoa,
            'donViTinh' => $donViTinh,
            'trangThai' => $request->trangThai,
        ]);

        return redirect('/nhom/' . $idNhom . '/hang-hoa')
            ->with(
                'success',
                'Cập nhật hàng hóa của nhóm thành công.'
            );
    }

    public function doiTrangThai(
        int $idNhom,
        int $idHangHoa
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect('/nhom/' . $idNhom . '/hang-hoa')
                ->with(
                    'error',
                    'Chỉ nhóm trưởng mới có quyền thay đổi trạng thái hàng hóa.'
                );
        }

        $hangHoa = HangHoa::where('idHangHoa', $idHangHoa)
            ->where('idNhom', $idNhom)
            ->firstOrFail();

        $trangThaiMoi = $hangHoa->trangThai === 'Đang sử dụng'
            ? 'Ngừng sử dụng'
            : 'Đang sử dụng';

        $hangHoa->update([
            'trangThai' => $trangThaiMoi,
        ]);

        $thongBao = $trangThaiMoi === 'Đang sử dụng'
            ? 'Mở sử dụng hàng hóa thành công.'
            : 'Ngừng sử dụng hàng hóa thành công.';

        return redirect('/nhom/' . $idNhom . '/hang-hoa')
            ->with('success', $thongBao);
    }

    private function boDauTiengViet(string $chuoi): string
    {
        $chuoi = mb_strtolower($chuoi, 'UTF-8');

        $coDau = [
            'à', 'á', 'ạ', 'ả', 'ã',
            'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ',
            'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ',
            'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ',
            'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ',
            'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ',
            'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];

        $khongDau = [
            'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e',
            'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u',
            'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($coDau, $khongDau, $chuoi);
    }
}