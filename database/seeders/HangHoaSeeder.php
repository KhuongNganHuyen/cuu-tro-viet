<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HangHoaSeeder extends Seeder
{
    public function run(): void
    {
        $hangHoasTheoDanhMuc = [
            'Lương thực' => [
                ['tenHangHoa' => 'Gạo', 'donViTinh' => 'kg'],
                ['tenHangHoa' => 'Mì tôm', 'donViTinh' => 'thùng'],
                ['tenHangHoa' => 'Cháo ăn liền', 'donViTinh' => 'thùng'],
                ['tenHangHoa' => 'Bánh mì khô', 'donViTinh' => 'gói'],
                ['tenHangHoa' => 'Sữa hộp', 'donViTinh' => 'thùng'],
            ],

            'Nước uống' => [
                ['tenHangHoa' => 'Nước uống đóng chai', 'donViTinh' => 'thùng'],
                ['tenHangHoa' => 'Nước lọc bình', 'donViTinh' => 'bình'],
                ['tenHangHoa' => 'Nước khoáng', 'donViTinh' => 'thùng'],
            ],

            'Quần áo' => [
                ['tenHangHoa' => 'Quần áo người lớn', 'donViTinh' => 'bộ'],
                ['tenHangHoa' => 'Quần áo trẻ em', 'donViTinh' => 'bộ'],
                ['tenHangHoa' => 'Áo ấm', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Áo mưa', 'donViTinh' => 'cái'],
            ],

            'Đồ dùng cá nhân' => [
                ['tenHangHoa' => 'Bàn chải đánh răng', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Kem đánh răng', 'donViTinh' => 'tuýp'],
                ['tenHangHoa' => 'Xà phòng', 'donViTinh' => 'cục'],
                ['tenHangHoa' => 'Khăn mặt', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Băng vệ sinh', 'donViTinh' => 'gói'],
            ],

            'Thiết bị y tế' => [
                ['tenHangHoa' => 'Khẩu trang y tế', 'donViTinh' => 'hộp'],
                ['tenHangHoa' => 'Nước sát khuẩn', 'donViTinh' => 'chai'],
                ['tenHangHoa' => 'Băng gạc y tế', 'donViTinh' => 'hộp'],
                ['tenHangHoa' => 'Thuốc hạ sốt', 'donViTinh' => 'hộp'],
                ['tenHangHoa' => 'Nước muối sinh lý', 'donViTinh' => 'chai'],
            ],

            'Vật dụng cứu hộ' => [
                ['tenHangHoa' => 'Áo phao', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Đèn pin', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Pin dự phòng', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Dây thừng cứu hộ', 'donViTinh' => 'cuộn'],
                ['tenHangHoa' => 'Chăn giữ nhiệt', 'donViTinh' => 'cái'],
            ],

            'Nhu yếu phẩm khác' => [
                ['tenHangHoa' => 'Chăn màn', 'donViTinh' => 'bộ'],
                ['tenHangHoa' => 'Chiếu', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Nồi niêu', 'donViTinh' => 'bộ'],
                ['tenHangHoa' => 'Bếp gas mini', 'donViTinh' => 'cái'],
                ['tenHangHoa' => 'Bình gas mini', 'donViTinh' => 'bình'],
            ],
        ];

        foreach ($hangHoasTheoDanhMuc as $tenDanhMuc => $hangHoas) {
            $danhMuc = DB::table('DanhMucHang')
                ->where('tenDanhMucHang', $tenDanhMuc)
                ->first();

            if (!$danhMuc) {
                continue;
            }

            foreach ($hangHoas as $hangHoa) {
                DB::table('HangHoa')->updateOrInsert(
                    [
                        'idDanhMucHang' => $danhMuc->idDanhMucHang,
                        'tenHangHoa' => $hangHoa['tenHangHoa'],
                        'donViTinh' => $hangHoa['donViTinh'],
                    ],
                    [
                        'idDanhMucHang' => $danhMuc->idDanhMucHang,
                        'tenHangHoa' => $hangHoa['tenHangHoa'],
                        'donViTinh' => $hangHoa['donViTinh'],
                        'trangThai' => 'Đang sử dụng',
                    ]
                );
            }
        }
    }
}