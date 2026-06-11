<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HangHoaSeeder extends Seeder
{
    public function run(): void
    {
        $duLieu = [
            'Thực phẩm ăn liền & Đóng hộp' => [
                ['Mì ăn liền', 'Thùng'],
                ['Lương khô', 'Hộp'],
                ['Cá đóng hộp', 'Thùng'],
                ['Cháo gói ăn liền', 'Thùng'],
                ['Thịt heo xay đóng hộp', 'Thùng'],
            ],
            'Lương thực & Gia vị cơ bản' => [
                ['Gạo tẻ', 'Kg'],
                ['Gạo nếp', 'Kg'],
                ['Dầu ăn', 'Chai'],
                ['Nước mắm', 'Chai'],
                ['Muối i-ốt', 'Gói'],
            ],
            'Nước uống & Sữa dinh dưỡng' => [
                ['Nước tinh khiết 500ml', 'Thùng'],
                ['Nước uống đóng bình 20 lít', 'Bình'],
                ['Sữa hộp', 'Thùng'],
                ['Sữa bột', 'Hộp'],
                ['Nước bù khoáng điện giải', 'Thùng'],
            ],
            'Vật tư y tế & Sơ cứu thiết yếu' => [
                ['Thuốc hạ sốt Paracetamol 500mg', 'Hộp'],
                ['Thuốc trị tiêu chảy Berberin', 'Hộp'],
                ['Dung dịch sát khuẩn tay nhanh', 'Chai'],
                ['Bông băng, gạc y tế và băng dán cá nhân', 'Bộ'],
                ['Kem bôi chống côn trùng cắn', 'Tuýp'],
            ],
            'Đồ bảo hộ & Cứu sinh an toàn' => [
                ['Áo phao cứu hộ tiêu chuẩn', 'Chiếc'],
                ['Đèn pin', 'Chiếc'],
                ['Ủng cao su lội nước', 'Đôi'],
                ['Áo mưa', 'Chiếc'],
                ['Còi thổi cứu hộ khẩn cấp', 'Chiếc'],
            ],
            'Vật dụng vệ sinh & Trang phục cá nhân' => [
                ['Quần áo ấm trẻ em', 'Bộ'],
                ['Quần áo người lớn', 'Bộ'],
                ['Kem đánh răng và bàn chải', 'Bộ'],
                ['Xà phòng cục', 'Cục'],
            ],
            'Thiết bị hậu cần & Dựng trại tạm thời' => [
                ['Bạt che mưa nắng loại lớn', 'Tấm'],
                ['Nến', 'Hộp'],
                ['Bật lửa', 'Cái'],
                ['Chăn mền mỏng ấm', 'Chiếc'],
                ['Màn chống muỗi', 'Chiếc'],
            ],
        ];

        foreach ($duLieu as $tenDanhMuc => $hangHoas) {
            $idDanhMuc = DB::table('DanhMucHang')
                ->where('tenDanhMucHang', $tenDanhMuc)
                ->value('idDanhMucHang');

            if (!$idDanhMuc) {
                continue;
            }

            foreach ($hangHoas as [$tenHangHoa, $donViTinh]) {
                DB::table('HangHoa')->updateOrInsert(
                    [
                        'tenHangHoa' => $tenHangHoa,
                    ],
                    [
                        'idDanhMucHang' => $idDanhMuc,
                        'idNhom' => null,
                        'tenHangHoa' => $tenHangHoa,
                        'donViTinh' => $donViTinh,
                        'trangThai' => 'Đang sử dụng',
                    ]
                );
            }
        }
    }
}