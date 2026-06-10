<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuKienCuuTroSeeder extends Seeder
{
    public function run(): void
    {
        $suKiens = [
            [
                'tenSuKien' => 'Bão lũ miền Trung',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Sự kiện cứu trợ khẩn cấp nhằm hỗ trợ người dân bị ảnh hưởng bởi bão lũ tại khu vực miền Trung.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
            [
                'tenSuKien' => 'Sạt lở đất khu vực miền núi',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Sự kiện hỗ trợ các hộ dân bị ảnh hưởng bởi sạt lở đất, cần nhu yếu phẩm, nơi ở tạm và vật dụng thiết yếu.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
            [
                'tenSuKien' => 'Hỗ trợ hộ nghèo',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Sự kiện hỗ trợ các hộ gia đình có hoàn cảnh khó khăn thông qua việc quyên góp lương thực, quần áo và đồ dùng sinh hoạt.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
            [
                'tenSuKien' => 'Chương trình áo ấm vùng cao',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Sự kiện kêu gọi đóng góp áo ấm, chăn màn và vật dụng cần thiết cho người dân, trẻ em ở vùng sâu vùng xa.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
            [
                'tenSuKien' => 'Hỗ trợ trẻ em khó khăn',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Sự kiện hỗ trợ trẻ em có hoàn cảnh khó khăn bằng sách vở, đồ dùng học tập, quần áo và các nhu yếu phẩm cần thiết.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
            [
                'tenSuKien' => 'Hỗ trợ bệnh nhân khó khăn',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Sự kiện hỗ trợ bệnh nhân có hoàn cảnh khó khăn thông qua việc quyên góp nhu yếu phẩm, vật tư y tế và các phần quà hỗ trợ.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
            [
                'tenSuKien' => 'Hỗ trợ người già neo đơn',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Sự kiện hỗ trợ người già neo đơn, người yếu thế bằng lương thực, thuốc men, quần áo và đồ dùng sinh hoạt.',
                'trangThai' => 'Đang diễn ra',
                'ngayTao' => now(),
            ],
        ];

        foreach ($suKiens as $suKien) {
            DB::table('SuKienCuuTro')->updateOrInsert(
                [
                    'tenSuKien' => $suKien['tenSuKien'],
                ],
                $suKien
            );
        }
    }
}