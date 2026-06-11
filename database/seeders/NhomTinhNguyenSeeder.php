<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhomTinhNguyenSeeder extends Seeder
{
    public function run(): void
    {
        $idTranThiNo = DB::table('NguoiDung')->where('tenDangNhap', 'TranThiNo')->value('idNguoiDung');
        $idKhang = DB::table('NguoiDung')->where('tenDangNhap', 'KhangNguyen')->value('idNguoiDung');
        $idBao = DB::table('NguoiDung')->where('tenDangNhap', 'BaoPhamQ')->value('idNguoiDung');

        $idHaiChau = DB::table('DiaDiem')->where('tinhThanh', 'TP. Đà Nẵng')->where('phuongXa', 'Hải Châu')->value('idDiaDiem');
        $idThanhKhe = DB::table('DiaDiem')->where('tinhThanh', 'TP. Đà Nẵng')->where('phuongXa', 'Thanh Khê')->value('idDiaDiem');
        $idHue = DB::table('DiaDiem')->where('tinhThanh', 'TP. Huế')->where('phuongXa', 'Phú Xuân')->value('idDiaDiem');

        $nhoms = [
            [
                'tenNhom' => 'Nhóm 123',
                'moTa' => 'Nhóm tình nguyện hỗ trợ tiếp nhận yêu cầu cứu trợ, điều phối đóng góp và phân phối nhu yếu phẩm tại khu vực Đà Nẵng.',
                'anhDaiDien' => 'nhom-tinh-nguyen/group.jpg',
                'idNhomTruong' => $idTranThiNo,
                'idDiaDiem' => $idHaiChau,
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Cầu Nối Yêu Thương Đà Nẵng',
                'moTa' => 'Nhóm kết nối người ủng hộ với các hoàn cảnh khó khăn, ưu tiên hỗ trợ lương thực, nước uống và vật dụng sinh hoạt.',
                'anhDaiDien' => 'nhom-tinh-nguyen/group.jpg',
                'idNhomTruong' => $idKhang,
                'idDiaDiem' => $idThanhKhe,
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Áo Ấm Miền Trung',
                'moTa' => 'Nhóm tập trung vận động áo ấm, chăn màn, vật dụng thiết yếu cho người dân vùng cao và người yếu thế.',
                'anhDaiDien' => 'nhom-tinh-nguyen/group.jpg',
                'idNhomTruong' => $idBao,
                'idDiaDiem' => $idHue,
                'trangThai' => 'Đang hoạt động',
            ],
        ];

        foreach ($nhoms as $nhom) {
            DB::table('NhomTinhNguyen')->updateOrInsert(
                ['tenNhom' => $nhom['tenNhom']],
                array_merge($nhom, [
                    'ngayTao' => now(),
                ])
            );
        }
    }
}