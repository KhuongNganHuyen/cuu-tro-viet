<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhMucHangSeeder extends Seeder
{
    public function run(): void
    {
        $danhMucHangs = [
            [
                'tenDanhMucHang' => 'Lương thực',
            ],
            [
                'tenDanhMucHang' => 'Nước uống',
            ],
            [
                'tenDanhMucHang' => 'Quần áo',
            ],
            [
                'tenDanhMucHang' => 'Đồ dùng cá nhân',
            ],
            [
                'tenDanhMucHang' => 'Thiết bị y tế',
            ],
            [
                'tenDanhMucHang' => 'Vật dụng cứu hộ',
            ],
            [
                'tenDanhMucHang' => 'Nhu yếu phẩm khác',
            ],
        ];

        foreach ($danhMucHangs as $danhMucHang) {
            DB::table('DanhMucHang')->updateOrInsert(
                [
                    'tenDanhMucHang' => $danhMucHang['tenDanhMucHang'],
                ],
                $danhMucHang
            );
        }
    }
}