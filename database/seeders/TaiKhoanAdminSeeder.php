<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TaiKhoanAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('NguoiDung')->updateOrInsert(
            [
                'tenDangNhap' => 'admin',
            ],
            [
                'hoTen' => 'Quản trị viên hệ thống',
                'tenDangNhap' => 'admin',
                'matKhau' => Hash::make('Admin@123456'),
                'gioiTinh' => 'Khác',
                'anhDaiDien' => null,
                'ngaySinh' => '2000-01-01',
                'sdt' => '0900000000',
                'email' => 'admin@cuutroviet.local',
                'vaiTro' => 'Quản trị viên',
                'trangThai' => 'Hoạt động',
                'ngayTao' => now(),
            ]
        );
    }
}