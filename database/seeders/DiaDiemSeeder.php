<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        $diaDiems = [
            [
                'tinhThanh' => 'Đà Nẵng',
                'phuongXa' => 'Hải Châu',
                'chiTietDiaDiem' => '48 Cao Thắng',
                'viDo' => 16.0765777,
                'kinhDo' => 108.2136447,
            ],
            [
                'tinhThanh' => 'Đà Nẵng',
                'phuongXa' => 'Hòa Khánh Bắc',
                'chiTietDiaDiem' => 'Đường Nguyễn Lương Bằng',
                'viDo' => 16.073372,
                'kinhDo' => 108.149728,
            ],
            [
                'tinhThanh' => 'Đà Nẵng',
                'phuongXa' => 'Hòa Minh',
                'chiTietDiaDiem' => 'Khu vực trung tâm phường Hòa Minh',
                'viDo' => 16.071365,
                'kinhDo' => 108.168363,
            ],
            [
                'tinhThanh' => 'Đà Nẵng',
                'phuongXa' => 'An Hải Bắc',
                'chiTietDiaDiem' => 'Khu vực cầu Sông Hàn',
                'viDo' => 16.078308,
                'kinhDo' => 108.229200,
            ],
            [
                'tinhThanh' => 'Đà Nẵng',
                'phuongXa' => 'Mỹ An',
                'chiTietDiaDiem' => 'Khu vực biển Mỹ Khê',
                'viDo' => 16.054407,
                'kinhDo' => 108.247422,
            ],
            [
                'tinhThanh' => 'Thừa Thiên Huế',
                'phuongXa' => 'Phú Hội',
                'chiTietDiaDiem' => 'Khu vực trung tâm phường Phú Hội',
                'viDo' => 16.463713,
                'kinhDo' => 107.590866,
            ],
            [
                'tinhThanh' => 'Thừa Thiên Huế',
                'phuongXa' => 'Thuận Hòa',
                'chiTietDiaDiem' => 'Khu vực Đại Nội Huế',
                'viDo' => 16.469112,
                'kinhDo' => 107.577462,
            ],
            [
                'tinhThanh' => 'Quảng Nam',
                'phuongXa' => 'Tam Kỳ',
                'chiTietDiaDiem' => 'Khu vực trung tâm thành phố Tam Kỳ',
                'viDo' => 15.573640,
                'kinhDo' => 108.474026,
            ],
            [
                'tinhThanh' => 'Quảng Nam',
                'phuongXa' => 'Hội An',
                'chiTietDiaDiem' => 'Khu vực phố cổ Hội An',
                'viDo' => 15.880058,
                'kinhDo' => 108.338047,
            ],
            [
                'tinhThanh' => 'Quảng Ngãi',
                'phuongXa' => 'Trần Phú',
                'chiTietDiaDiem' => 'Khu vực trung tâm thành phố Quảng Ngãi',
                'viDo' => 15.120853,
                'kinhDo' => 108.792184,
            ],
            [
                'tinhThanh' => 'Quảng Ngãi',
                'phuongXa' => 'Nghĩa Chánh',
                'chiTietDiaDiem' => 'Khu vực phường Nghĩa Chánh',
                'viDo' => 15.116106,
                'kinhDo' => 108.806954,
            ],
        ];

        foreach ($diaDiems as $diaDiem) {
            DB::table('DiaDiem')->updateOrInsert(
                [
                    'tinhThanh' => $diaDiem['tinhThanh'],
                    'phuongXa' => $diaDiem['phuongXa'],
                    'chiTietDiaDiem' => $diaDiem['chiTietDiaDiem'],
                ],
                $diaDiem
            );
        }
    }
}