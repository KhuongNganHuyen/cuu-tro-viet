<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhMucHangSeeder extends Seeder
{
    public function run(): void
    {
        $danhMucs = [
            'Thực phẩm ăn liền & Đóng hộp',
            'Lương thực & Gia vị cơ bản',
            'Nước uống & Sữa dinh dưỡng',
            'Vật tư y tế & Sơ cứu thiết yếu',
            'Đồ bảo hộ & Cứu sinh an toàn',
            'Vật dụng vệ sinh & Trang phục cá nhân',
            'Thiết bị hậu cần & Dựng trại tạm thời',
        ];

        foreach ($danhMucs as $tenDanhMuc) {
            DB::table('DanhMucHang')->updateOrInsert(
                ['tenDanhMucHang' => $tenDanhMuc],
                ['tenDanhMucHang' => $tenDanhMuc]
            );
        }
    }
}