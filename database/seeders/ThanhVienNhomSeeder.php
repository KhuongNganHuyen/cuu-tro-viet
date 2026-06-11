<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhVienNhomSeeder extends Seeder
{
    public function run(): void
    {
        $thanhViens = [
            ['tenNhom' => 'Nhóm 123', 'tenDangNhap' => 'TranThiNo', 'vaiTro' => 'Nhóm trưởng'],
            ['tenNhom' => 'Nhóm 123', 'tenDangNhap' => 'HoaiAnLe', 'vaiTro' => 'Thành viên'],
            ['tenNhom' => 'Nhóm 123', 'tenDangNhap' => 'ThanhNhanVo', 'vaiTro' => 'Thành viên'],

            ['tenNhom' => 'Cầu Nối Yêu Thương Đà Nẵng', 'tenDangNhap' => 'KhangNguyen', 'vaiTro' => 'Nhóm trưởng'],
            ['tenNhom' => 'Cầu Nối Yêu Thương Đà Nẵng', 'tenDangNhap' => 'GiaHuyDang', 'vaiTro' => 'Thành viên'],
            ['tenNhom' => 'Cầu Nối Yêu Thương Đà Nẵng', 'tenDangNhap' => 'KhanhLinhBui', 'vaiTro' => 'Thành viên'],

            ['tenNhom' => 'Áo Ấm Miền Trung', 'tenDangNhap' => 'BaoPhamQ', 'vaiTro' => 'Nhóm trưởng'],
            ['tenNhom' => 'Áo Ấm Miền Trung', 'tenDangNhap' => 'TanPhatH', 'vaiTro' => 'Thành viên'],
            ['tenNhom' => 'Áo Ấm Miền Trung', 'tenDangNhap' => 'ThanhNhanVo', 'vaiTro' => 'Thành viên'],
        ];

        foreach ($thanhViens as $item) {
            $idNhom = DB::table('NhomTinhNguyen')
                ->where('tenNhom', $item['tenNhom'])
                ->value('idNhom');

            $idNguoiDung = DB::table('NguoiDung')
                ->where('tenDangNhap', $item['tenDangNhap'])
                ->value('idNguoiDung');

            if (!$idNhom || !$idNguoiDung) {
                continue;
            }

            DB::table('ThanhVienNhom')->updateOrInsert(
                [
                    'idNhom' => $idNhom,
                    'idNguoiDung' => $idNguoiDung,
                ],
                [
                    'idNhom' => $idNhom,
                    'idNguoiDung' => $idNguoiDung,
                    'vaiTro' => $item['vaiTro'],
                    'ngayThamGia' => now(),
                ]
            );
        }
    }
}