<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NguonLucChienDichSeeder extends Seeder
{
    public function run(): void
    {
        $duLieu = [
            'Tiếp sức lương thực sau ngập lụt Đà Nẵng' => [
                ['Mì ăn liền', 80],
                ['Gạo tẻ', 120],
                ['Nước tinh khiết 500ml', 60],
                ['Cá đóng hộp', 40],
            ],

            'Điều phối nước sạch cho khu dân cư thiếu nước' => [
                ['Nước tinh khiết 500ml', 100],
                ['Sữa hộp', 50],
                ['Dung dịch sát khuẩn tay nhanh', 30],
            ],

            'Áo ấm và chăn mền cho vùng cao A Lưới' => [
                ['Chăn mền mỏng ấm', 70],
                ['Kem đánh răng và bàn chải', 50],
                ['Sữa hộp', 40],
            ],

            'Hỗ trợ khẩn cấp khu vực ngập sâu tại Huế' => [
                ['Áo phao cứu hộ tiêu chuẩn', 35],
                ['Đèn pin', 30],
                ['Lương khô', 60],
                ['Nước tinh khiết 500ml', 80],
                ['Bạt che mưa nắng loại lớn', 20],
            ],

            'Bếp ăn hỗ trợ người dân sau mưa lũ' => [
                ['Gạo tẻ', 150],
                ['Dầu ăn', 40],
                ['Cá đóng hộp', 50],
                ['Mì ăn liền', 70],
            ],

            'Tiếp tế nhu yếu phẩm cho khu vực bị sạt lở' => [
                ['Lương khô', 80],
                ['Cá đóng hộp', 60],
                ['Nước tinh khiết 500ml', 90],
                ['Bạt che mưa nắng loại lớn', 25],
            ],

            'Chuẩn bị nguồn lực ứng phó bão Trung Bộ' => [
                ['Nước tinh khiết 500ml', 120],
                ['Lương khô', 90],
                ['Đèn pin', 40],
                ['Bạt che mưa nắng loại lớn', 35],
                ['Áo phao cứu hộ tiêu chuẩn', 30],
            ],
        ];

        foreach ($duLieu as $tenChienDich => $hangHoas) {
            $idChienDich = DB::table('ChienDichCuuTro')
                ->where('tenChienDich', $tenChienDich)
                ->value('idChienDich');

            if (!$idChienDich) {
                continue;
            }

            foreach ($hangHoas as [$tenHangHoa, $soLuongCanKeuGoi]) {
                $idHangHoa = DB::table('HangHoa')
                    ->where('tenHangHoa', $tenHangHoa)
                    ->value('idHangHoa');

                if (!$idHangHoa) {
                    continue;
                }

                DB::table('NguonLucChienDich')->updateOrInsert(
                    [
                        'idChienDich' => $idChienDich,
                        'idHangHoa' => $idHangHoa,
                    ],
                    [
                        'idChienDich' => $idChienDich,
                        'idHangHoa' => $idHangHoa,
                        'soLuongCanKeuGoi' => $soLuongCanKeuGoi,
                        'soLuongDaNhan' => 0,
                        'soLuongHienCo' => 0,
                        'trangThai' => 'Đang kêu gọi',
                        'ngayCapNhat' => now(),
                    ]
                );
            }
        }
    }
}