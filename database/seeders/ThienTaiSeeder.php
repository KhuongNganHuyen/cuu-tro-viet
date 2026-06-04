<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThienTaiSeeder extends Seeder
{
    public function run(): void
    {
        $thienTais = [
            [
                'tenThienTai' => 'Bão Ragasa',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Bão Bualoi',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Bão Matmo',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Bão Kalmaegi',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Bão Koto',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Mưa lũ miền Trung',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Lũ quét, sạt lở đất miền núi',
                'namXayRa' => 2025,
            ],
            [
                'tenThienTai' => 'Ngập lụt đô thị Đà Nẵng',
                'namXayRa' => 2025,
            ],

            [
                'tenThienTai' => 'Bão, áp thấp nhiệt đới trên Biển Đông',
                'namXayRa' => 2026,
            ],
            [
                'tenThienTai' => 'Mưa lớn diện rộng miền Trung',
                'namXayRa' => 2026,
            ],
            [
                'tenThienTai' => 'Lũ quét, sạt lở đất khu vực miền núi',
                'namXayRa' => 2026,
            ],
            [
                'tenThienTai' => 'Ngập lụt đô thị do mưa lớn',
                'namXayRa' => 2026,
            ],
        ];

        foreach ($thienTais as $thienTai) {
            DB::table('ThienTai')->updateOrInsert(
                [
                    'tenThienTai' => $thienTai['tenThienTai'],
                    'namXayRa' => $thienTai['namXayRa'],
                ],
                $thienTai
            );
        }
    }
}