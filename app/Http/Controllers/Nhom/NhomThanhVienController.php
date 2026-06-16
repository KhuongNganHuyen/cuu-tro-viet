<?php

namespace App\Http\Controllers\Nhom;

use App\Http\Controllers\Controller;
use App\Models\NhomTinhNguyen;
use App\Models\NguoiDung;
use App\Models\ThanhVienNhom;
use Illuminate\Http\Request;

class NhomThanhVienController extends Controller
{
    private function kiemTraThanhVien(int $idNhom): array
    {
        $idNguoiDung = session('idNguoiDung');

        if (!$idNguoiDung) {
            return [
                'hopLe' => false,
                'redirect' => redirect('/login')
                    ->with(
                        'error',
                        'Vui lòng đăng nhập để tiếp tục.'
                    ),
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

        $thanhVien = ThanhVienNhom::where(
                'idNhom',
                $idNhom
            )
            ->where(
                'idNguoiDung',
                $idNguoiDung
            )
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
            'vaiTroTrongNhom' =>
                $thanhVien->vaiTro ?? 'Thành viên',
        ];
    }

    public function index(
        Request $request,
        int $idNhom
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        $nhom = $kiemTra['nhom'];
        $laNhomTruong = $kiemTra['laNhomTruong'];
        $vaiTroTrongNhom = $kiemTra['vaiTroTrongNhom'];

        $tuKhoa = trim(
            (string) $request->input('tuKhoa')
        );

        $thanhViens = ThanhVienNhom::with('nguoiDung')
            ->where('idNhom', $idNhom)
            ->orderBy('idThanhVien', 'asc')
            ->get();

        if ($tuKhoa !== '') {
            $tuKhoaThuong = mb_strtolower(
                $tuKhoa,
                'UTF-8'
            );

            $tuKhoaKhongDau =
                $this->boDauTiengViet($tuKhoa);

            $thanhViens = $thanhViens
                ->filter(function ($thanhVien) use (
                    $tuKhoaThuong,
                    $tuKhoaKhongDau
                ) {
                    $nguoiDung = $thanhVien->nguoiDung;

                    $noiDung = implode(' ', [
                        $thanhVien->idThanhVien,
                        $thanhVien->idNguoiDung,
                        $thanhVien->vaiTro,
                        $thanhVien->ngayThamGia,
                        $nguoiDung->hoTen ?? '',
                        $nguoiDung->tenDangNhap ?? '',
                        $nguoiDung->email ?? '',
                        $nguoiDung->sdt ?? '',
                    ]);

                    $noiDungThuong = mb_strtolower(
                        $noiDung,
                        'UTF-8'
                    );

                    $noiDungKhongDau =
                        $this->boDauTiengViet($noiDung);

                    return str_contains(
                        $noiDungThuong,
                        $tuKhoaThuong
                    ) || str_contains(
                        $noiDungKhongDau,
                        $tuKhoaKhongDau
                    );
                })
                ->values();
        }

        /*
         * Bình thường mã thành viên tăng dần.
         * Khi vừa thêm mới, bản ghi mới được đưa lên đầu.
         */
        if (
            $tuKhoa === ''
            && session()->has('thanhVienMoi')
        ) {
            $idThanhVienMoi = session('thanhVienMoi');

            $thanhViens = $thanhViens
                ->sortBy(function ($thanhVien) use (
                    $idThanhVienMoi
                ) {
                    return $thanhVien->idThanhVien
                        == $idThanhVienMoi
                            ? -1
                            : (int) $thanhVien->idThanhVien;
                })
                ->values();
        } else {
            $thanhViens = $thanhViens
                ->sortBy(function ($thanhVien) {
                    return (int) $thanhVien->idThanhVien;
                })
                ->values();
        }

        return view(
            'nhom.thanh_vien.index',
            compact(
                'nhom',
                'thanhViens',
                'laNhomTruong',
                'vaiTroTrongNhom'
            )
        );
    }

    public function create(int $idNhom)
    {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/thanh-vien'
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền thêm thành viên.'
            );
        }

        $nhom = $kiemTra['nhom'];

        $idThanhVienDaCo = ThanhVienNhom::where(
                'idNhom',
                $idNhom
            )
            ->pluck('idNguoiDung')
            ->toArray();

        $nguoiDungs = NguoiDung::where(
                'trangThai',
                'Hoạt động'
            )
            ->where(
                'vaiTro',
                '!=',
                'Quản trị viên'
            )
            ->whereNotIn(
                'idNguoiDung',
                $idThanhVienDaCo
            )
            ->orderBy('hoTen', 'asc')
            ->get();

        $nguoiDungJson = $nguoiDungs
            ->map(function ($nguoiDung) {
                return [
                    'idNguoiDung' =>
                        $nguoiDung->idNguoiDung,
                    'hoTen' =>
                        $nguoiDung->hoTen,
                    'tenDangNhap' =>
                        $nguoiDung->tenDangNhap,
                    'email' =>
                        $nguoiDung->email,
                    'sdt' =>
                        $nguoiDung->sdt,
                    'label' =>
                        $nguoiDung->hoTen
                        . ' - '
                        . $nguoiDung->tenDangNhap,
                ];
            })
            ->values()
            ->toJson();

        return view(
            'nhom.thanh_vien.create',
            compact(
                'nhom',
                'nguoiDungs',
                'nguoiDungJson'
            )
        );
    }

    public function store(
        Request $request,
        int $idNhom
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/thanh-vien'
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền thêm thành viên.'
            );
        }

        $request->validate([
            'idNguoiDung' =>
                'required|exists:NguoiDung,idNguoiDung',
            'vaiTro' =>
                'nullable|string|max:255',
        ], [
            'idNguoiDung.required' =>
                'Vui lòng chọn người dùng hợp lệ từ danh sách gợi ý.',
            'idNguoiDung.exists' =>
                'Người dùng không hợp lệ.',
            'vaiTro.max' =>
                'Vai trò trong nhóm không được vượt quá 255 ký tự.',
        ]);

        $daTonTai = ThanhVienNhom::where(
                'idNhom',
                $idNhom
            )
            ->where(
                'idNguoiDung',
                $request->idNguoiDung
            )
            ->exists();

        if ($daTonTai) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Người dùng này đã là thành viên của nhóm.'
                );
        }

        $vaiTroNhap = trim(
            (string) $request->vaiTro
        );

        if ($vaiTroNhap === '') {
            $vaiTroNhap = 'Thành viên';
        }

        $vaiTroKiemTra = $this->boDauTiengViet(
            $vaiTroNhap
        );

        if ($vaiTroKiemTra === 'nhom truong') {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Không thể thêm thành viên với vai trò Nhóm trưởng tại đây. Nếu muốn chuyển nhượng nhóm trưởng, vui lòng vào phần Sửa thông tin nhóm.'
                );
        }

        $thanhVienMoi = ThanhVienNhom::create([
            'idNhom' => $idNhom,
            'idNguoiDung' => $request->idNguoiDung,
            'vaiTro' => $vaiTroNhap,
            'ngayThamGia' => now(),
        ]);

        return redirect(
            '/nhom/' . $idNhom . '/thanh-vien'
        )
            ->with(
                'success',
                'Thêm thành viên nhóm thành công.'
            )
            ->with(
                'thanhVienMoi',
                $thanhVienMoi->idThanhVien
            );
    }

    public function destroy(
        int $idNhom,
        int $idThanhVien
    ) {
        $kiemTra = $this->kiemTraThanhVien($idNhom);

        if (!$kiemTra['hopLe']) {
            return $kiemTra['redirect'];
        }

        if (!$kiemTra['laNhomTruong']) {
            return redirect(
                '/nhom/' . $idNhom . '/thanh-vien'
            )->with(
                'error',
                'Chỉ nhóm trưởng mới có quyền xóa thành viên.'
            );
        }

        $thanhVien = ThanhVienNhom::where(
                'idNhom',
                $idNhom
            )
            ->where(
                'idThanhVien',
                $idThanhVien
            )
            ->firstOrFail();

        if ($thanhVien->vaiTro === 'Nhóm trưởng') {
            return redirect(
                '/nhom/' . $idNhom . '/thanh-vien'
            )->with(
                'error',
                'Không thể xóa nhóm trưởng khỏi nhóm tại đây.'
            );
        }

        $thanhVien->delete();

        return redirect(
            '/nhom/' . $idNhom . '/thanh-vien'
        )->with(
            'success',
            'Xóa thành viên khỏi nhóm thành công.'
        );
    }

    private function boDauTiengViet(
        string $chuoi
    ): string {
        $chuoi = mb_strtolower(
            $chuoi,
            'UTF-8'
        );

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

        return str_replace(
            $coDau,
            $khongDau,
            $chuoi
        );
    }
}