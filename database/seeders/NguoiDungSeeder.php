<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    public function run(): void
    {
        $matKhauChung = Hash::make('User@123456');

        $nguoiDungs = [
            [
                'hoTen' => 'Trần Thị Nở',
                'tenDangNhap' => 'TranThiNo',
                'gioiTinh' => 'Nữ',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '1998-04-12',
                'sdt' => '0912468135',
                'email' => 'trannothi@example.com',
            ],
            [
                'hoTen' => 'Nguyễn Minh Khang',
                'tenDangNhap' => 'KhangNguyen',
                'gioiTinh' => 'Nam',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '1995-09-21',
                'sdt' => '0935827461',
                'email' => 'minhkhang95@example.com',
            ],
            [
                'hoTen' => 'Lê Hoài An',
                'tenDangNhap' => 'HoaiAnLe',
                'gioiTinh' => 'Nữ',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '2001-02-18',
                'sdt' => '0973146285',
                'email' => 'hoaian.le@example.com',
            ],
            [
                'hoTen' => 'Phạm Quốc Bảo',
                'tenDangNhap' => 'BaoPhamQ',
                'gioiTinh' => 'Nam',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '1992-11-03',
                'sdt' => '0986712345',
                'email' => 'quocbao.pham@example.com',
            ],
            [
                'hoTen' => 'Võ Thanh Nhàn',
                'tenDangNhap' => 'ThanhNhanVo',
                'gioiTinh' => 'Nữ',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '1999-07-27',
                'sdt' => '0964281357',
                'email' => 'thanhnhan.vo@example.com',
            ],
            [
                'hoTen' => 'Đặng Gia Huy',
                'tenDangNhap' => 'GiaHuyDang',
                'gioiTinh' => 'Nam',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '1997-12-08',
                'sdt' => '0907352468',
                'email' => 'giahuy.dang@example.com',
            ],
            [
                'hoTen' => 'Bùi Khánh Linh',
                'tenDangNhap' => 'KhanhLinhBui',
                'gioiTinh' => 'Nữ',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '2000-05-30',
                'sdt' => '0946813572',
                'email' => 'khanhlinh.bui@example.com',
            ],
            [
                'hoTen' => 'Huỳnh Tấn Phát',
                'tenDangNhap' => 'TanPhatH',
                'gioiTinh' => 'Nam',
                'anhDaiDien' => 'nguoi-dung/avatar.jpg',
                'ngaySinh' => '1994-03-16',
                'sdt' => '0924681357',
                'email' => 'tanphat.huynh@example.com',
            ],
        ];

        foreach ($nguoiDungs as $nguoiDung) {
            DB::table('NguoiDung')->updateOrInsert(
                ['tenDangNhap' => $nguoiDung['tenDangNhap']],
                array_merge($nguoiDung, [
                    'matKhau' => $matKhauChung,
                    'vaiTro' => 'Người dùng',
                    'trangThai' => 'Hoạt động',
                    'ngayTao' => now(),
                ])
            );
        }
    }
}