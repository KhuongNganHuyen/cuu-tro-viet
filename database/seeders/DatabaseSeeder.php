<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DiaDiemSeeder::class,
            DanhMucHangSeeder::class,
            HangHoaSeeder::class,
            SuKienCuuTroSeeder::class,

            TaiKhoanAdminSeeder::class,
            NguoiDungSeeder::class,
            NhomTinhNguyenSeeder::class,
            ThanhVienNhomSeeder::class,
        ]);
    }
}