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
            ],

            'Lương thực & Gia vị cơ bản' => [
                ['Gạo tẻ', 'Kg'],
                ['Dầu ăn', 'Chai'],
            ],

            'Nước uống & Sữa dinh dưỡng' => [
                ['Nước tinh khiết 500ml', 'Thùng'],
                ['Sữa hộp', 'Thùng'],
            ],

            'Vật tư y tế & Sơ cứu thiết yếu' => [
                ['Thuốc hạ sốt Paracetamol 500mg', 'Hộp'],
                ['Dung dịch sát khuẩn tay nhanh', 'Chai'],
                ['Bông băng, gạc y tế và băng dán cá nhân', 'Bộ'],
            ],

            'Đồ bảo hộ & Cứu sinh an toàn' => [
                ['Áo phao cứu hộ tiêu chuẩn', 'Chiếc'],
                ['Đèn pin', 'Chiếc'],
            ],

            'Vật dụng vệ sinh & Trang phục cá nhân' => [
                ['Kem đánh răng và bàn chải', 'Bộ'],
            ],

            'Thiết bị hậu cần & Dựng trại tạm thời' => [
                ['Bạt che mưa nắng loại lớn', 'Tấm'],
                ['Chăn mền mỏng ấm', 'Chiếc'],
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
                        'idNhom' => null,
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