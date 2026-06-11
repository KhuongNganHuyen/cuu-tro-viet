<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaDiemSeeder extends Seeder
{
    public function run(): void
    {
        $diaDiems = [
            ['tinhThanh' => 'TP. Huế', 'phuongXa' => 'Mỹ Thượng', 'chiTietDiaDiem' => 'Số 01 Đường Võ Nguyên Giáp', 'viDo' => 16.4712, 'kinhDo' => 107.6185],
            ['tinhThanh' => 'TP. Huế', 'phuongXa' => 'Hương Thủy', 'chiTietDiaDiem' => 'Số 124 Đường Nguyễn Tất Thành', 'viDo' => 16.4255, 'kinhDo' => 107.6530],
            ['tinhThanh' => 'TP. Huế', 'phuongXa' => 'Phú Xuân', 'chiTietDiaDiem' => 'Số 394 Đường Đinh Tiên Hoàng', 'viDo' => 16.4764, 'kinhDo' => 107.5792],
            ['tinhThanh' => 'TP. Huế', 'phuongXa' => 'Đan Điền', 'chiTietDiaDiem' => 'Số 45 Đường Đan Điền', 'viDo' => 16.5518, 'kinhDo' => 107.5124],
            ['tinhThanh' => 'TP. Huế', 'phuongXa' => 'A Lưới 2', 'chiTietDiaDiem' => 'Số 250 Đường Hồ Chí Minh', 'viDo' => 16.2305, 'kinhDo' => 107.2811],

            ['tinhThanh' => 'TP. Đà Nẵng', 'phuongXa' => 'Hải Châu', 'chiTietDiaDiem' => 'Số 48 Cao Thắng', 'viDo' => 16.0765777, 'kinhDo' => 108.2136447],
            ['tinhThanh' => 'TP. Đà Nẵng', 'phuongXa' => 'Thanh Khê', 'chiTietDiaDiem' => 'Số 503 Đường Trần Cao Vân', 'viDo' => 16.0683, 'kinhDo' => 108.1887],
            ['tinhThanh' => 'TP. Đà Nẵng', 'phuongXa' => 'An Hải', 'chiTietDiaDiem' => 'Số 289 Đường Nguyễn Công Trứ', 'viDo' => 16.0694, 'kinhDo' => 108.2325],
            ['tinhThanh' => 'TP. Đà Nẵng', 'phuongXa' => 'Phú Thuận', 'chiTietDiaDiem' => 'Số 88 Đường Phú Thuận', 'viDo' => 15.9622, 'kinhDo' => 108.2514],
            ['tinhThanh' => 'TP. Đà Nẵng', 'phuongXa' => 'Quế Sơn', 'chiTietDiaDiem' => 'Số 10 Đường Hùng Vương', 'viDo' => 15.7531, 'kinhDo' => 108.1568],

            ['tinhThanh' => 'Tỉnh Quảng Ngãi', 'phuongXa' => 'An Phú', 'chiTietDiaDiem' => 'Số 15 Đường An Phú', 'viDo' => 15.1189, 'kinhDo' => 108.8242],
            ['tinhThanh' => 'Tỉnh Quảng Ngãi', 'phuongXa' => 'Trường Giang', 'chiTietDiaDiem' => 'Thôn Đồng Nhơn Bắc', 'viDo' => 15.1745, 'kinhDo' => 108.7312],
            ['tinhThanh' => 'Tỉnh Quảng Ngãi', 'phuongXa' => 'Sơn Tịnh', 'chiTietDiaDiem' => 'Số 220 Đường Võ Nguyên Giáp', 'viDo' => 15.1524, 'kinhDo' => 108.7758],
            ['tinhThanh' => 'Tỉnh Quảng Ngãi', 'phuongXa' => 'Mộ Đức', 'chiTietDiaDiem' => 'Số 45 Đường Quy Nghĩa', 'viDo' => 15.0125, 'kinhDo' => 108.8752],
            ['tinhThanh' => 'Tỉnh Quảng Ngãi', 'phuongXa' => 'Cà Đam', 'chiTietDiaDiem' => 'Thôn Cà Đam', 'viDo' => 15.1214, 'kinhDo' => 108.4521],

            ['tinhThanh' => 'Tỉnh Gia Lai', 'phuongXa' => 'Quy Nhơn', 'chiTietDiaDiem' => 'Số 21 Đường Mai Xuân Thưởng', 'viDo' => 13.7742, 'kinhDo' => 109.2274],
            ['tinhThanh' => 'Tỉnh Gia Lai', 'phuongXa' => 'Vạn Đức', 'chiTietDiaDiem' => 'Thôn Vạn Đức', 'viDo' => 14.3541, 'kinhDo' => 108.9852],
            ['tinhThanh' => 'Tỉnh Gia Lai', 'phuongXa' => 'An Toàn', 'chiTietDiaDiem' => 'Thôn 1, Huyện An Lão', 'viDo' => 14.5824, 'kinhDo' => 108.8521],
            ['tinhThanh' => 'Tỉnh Gia Lai', 'phuongXa' => 'Pleiku', 'chiTietDiaDiem' => 'Số 81 Đường Hùng Vương', 'viDo' => 13.9789, 'kinhDo' => 107.9945],
            ['tinhThanh' => 'Tỉnh Gia Lai', 'phuongXa' => 'KBang', 'chiTietDiaDiem' => 'Số 12 Đường Ngô Mây', 'viDo' => 14.1352, 'kinhDo' => 108.6145],
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