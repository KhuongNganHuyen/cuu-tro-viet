<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChienDichCuuTroSeeder extends Seeder
{
    public function run(): void
    {
        $layNhom = function (string $tenNhom) {
            return DB::table('NhomTinhNguyen')
                ->where('tenNhom', $tenNhom)
                ->value('idNhom');
        };

        $laySuKien = function (string $tenSuKien) {
            return DB::table('SuKienCuuTro')
                ->where('tenSuKien', $tenSuKien)
                ->value('idSuKien');
        };

        $layDiaDiem = function (string $tinhThanh, string $phuongXa) {
            return DB::table('DiaDiem')
                ->where('tinhThanh', $tinhThanh)
                ->where('phuongXa', $phuongXa)
                ->value('idDiaDiem');
        };

        $chienDichs = [
            [
                'tenNhom' => 'Nhóm 123',
                'tenSuKien' => 'Ngập lụt đô thị Đà Nẵng',
                'diaDiem' => ['TP. Đà Nẵng', 'Thanh Khê'],
                'tenChienDich' => 'Tiếp sức lương thực sau ngập lụt Đà Nẵng',
                'moTa' => 'Kêu gọi mì ăn liền, gạo, nước uống và thực phẩm đóng hộp để hỗ trợ các hộ dân bị ảnh hưởng bởi ngập lụt.',
                'ngayTao' => Carbon::now()->subDays(7),
                'ngayBatDau' => Carbon::now()->subDays(6)->toDateString(),
                'ngayKetThuc' => Carbon::now()->addDays(10)->toDateString(),
                'daXacNhanCuuTro' => true,
                'ghiChuXacNhan' => 'Chiến dịch đã được xác nhận triển khai tại các khu vực ngập nặng.',
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Cầu Nối Yêu Thương Đà Nẵng',
                'tenSuKien' => 'Điều phối nước sạch sinh hoạt',
                'diaDiem' => ['TP. Đà Nẵng', 'Hải Châu'],
                'tenChienDich' => 'Điều phối nước sạch cho khu dân cư thiếu nước',
                'moTa' => 'Tiếp nhận và phân phối nước tinh khiết, sữa hộp và một số nhu yếu phẩm cho người dân thiếu nước sinh hoạt.',
                'ngayTao' => Carbon::now()->subDays(5),
                'ngayBatDau' => Carbon::now()->subDays(4)->toDateString(),
                'ngayKetThuc' => Carbon::now()->addDays(8)->toDateString(),
                'daXacNhanCuuTro' => true,
                'ghiChuXacNhan' => 'Ưu tiên các hộ dân có trẻ nhỏ, người già và khu vực chưa có nước sạch ổn định.',
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Áo Ấm Miền Trung',
                'tenSuKien' => 'Quyên góp đồ ấm mùa đông',
                'diaDiem' => ['TP. Huế', 'A Lưới 2'],
                'tenChienDich' => 'Áo ấm và chăn mền cho vùng cao A Lưới',
                'moTa' => 'Kêu gọi chăn mền, áo ấm và vật dụng thiết yếu nhằm hỗ trợ người dân vùng cao trong thời tiết lạnh.',
                'ngayTao' => Carbon::now()->subDays(12),
                'ngayBatDau' => Carbon::now()->subDays(10)->toDateString(),
                'ngayKetThuc' => Carbon::now()->subDays(1)->toDateString(),
                'daXacNhanCuuTro' => true,
                'ghiChuXacNhan' => 'Chiến dịch đã hoàn tất đợt phân phối chính cho các hộ dân đăng ký.',
                'trangThai' => 'Hoàn thành',
            ],
            [
                'tenNhom' => 'Nhóm 123',
                'tenSuKien' => 'Áp thấp nhiệt đới và lũ quét cục bộ Nam Đông',
                'diaDiem' => ['TP. Huế', 'Đan Điền'],
                'tenChienDich' => 'Hỗ trợ khẩn cấp khu vực ngập sâu tại Huế',
                'moTa' => 'Tổ chức tiếp nhận yêu cầu, kêu gọi áo phao, đèn pin, lương khô và nước uống cho các hộ dân bị cô lập tạm thời.',
                'ngayTao' => Carbon::now()->subDays(3),
                'ngayBatDau' => Carbon::now()->subDays(2)->toDateString(),
                'ngayKetThuc' => Carbon::now()->addDays(12)->toDateString(),
                'daXacNhanCuuTro' => true,
                'ghiChuXacNhan' => 'Chiến dịch đang ưu tiên hỗ trợ các điểm ngập sâu và khu vực có nguy cơ mất an toàn.',
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Cầu Nối Yêu Thương Đà Nẵng',
                'tenSuKien' => 'Bếp ăn thiện nguyện và suất ăn miễn phí',
                'diaDiem' => ['Tỉnh Quảng Ngãi', 'Trường Giang'],
                'tenChienDich' => 'Bếp ăn hỗ trợ người dân sau mưa lũ',
                'moTa' => 'Kết nối nguồn gạo, dầu ăn, thực phẩm đóng hộp và các nhu yếu phẩm để duy trì bếp ăn thiện nguyện tạm thời.',
                'ngayTao' => Carbon::now()->subDays(9),
                'ngayBatDau' => Carbon::now()->subDays(8)->toDateString(),
                'ngayKetThuc' => Carbon::now()->addDays(4)->toDateString(),
                'daXacNhanCuuTro' => true,
                'ghiChuXacNhan' => 'Chiến dịch hỗ trợ các suất ăn miễn phí cho người dân tại điểm tập kết.',
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Áo Ấm Miền Trung',
                'tenSuKien' => 'Sạt lở đất vùng núi Phước Sơn',
                'diaDiem' => ['Tỉnh Quảng Ngãi', 'Cà Đam'],
                'tenChienDich' => 'Tiếp tế nhu yếu phẩm cho khu vực bị sạt lở',
                'moTa' => 'Kêu gọi lương khô, cá đóng hộp, nước uống và bạt che để hỗ trợ các hộ dân bị ảnh hưởng bởi sạt lở.',
                'ngayTao' => Carbon::now()->subDays(4),
                'ngayBatDau' => Carbon::now()->subDays(3)->toDateString(),
                'ngayKetThuc' => Carbon::now()->addDays(9)->toDateString(),
                'daXacNhanCuuTro' => true,
                'ghiChuXacNhan' => 'Chiến dịch đang phối hợp với nhóm địa phương để xác minh nhu cầu thực tế.',
                'trangThai' => 'Đang hoạt động',
            ],
            [
                'tenNhom' => 'Nhóm 123',
                'tenSuKien' => 'Siêu bão áp sát đất liền Trung Bộ',
                'diaDiem' => ['TP. Đà Nẵng', 'An Hải'],
                'tenChienDich' => 'Chuẩn bị nguồn lực ứng phó bão Trung Bộ',
                'moTa' => 'Chuẩn bị trước nguồn lực gồm nước uống, lương khô, bạt che và đèn pin để sẵn sàng hỗ trợ khi bão ảnh hưởng trực tiếp.',
                'ngayTao' => Carbon::now(),
                'ngayBatDau' => Carbon::now()->addDay()->toDateString(),
                'ngayKetThuc' => Carbon::now()->addDays(15)->toDateString(),
                'daXacNhanCuuTro' => false,
                'ghiChuXacNhan' => 'Đang chờ xác nhận tình hình và phạm vi triển khai.',
                'trangThai' => 'Tạm ngưng',
            ],
        ];

        foreach ($chienDichs as $item) {
            $idNhom = $layNhom($item['tenNhom']);
            $idSuKien = $laySuKien($item['tenSuKien']);
            $idDiaDiem = $layDiaDiem($item['diaDiem'][0], $item['diaDiem'][1]);

            if (!$idNhom || !$idSuKien || !$idDiaDiem) {
                continue;
            }

            DB::table('ChienDichCuuTro')->updateOrInsert(
                [
                    'tenChienDich' => $item['tenChienDich'],
                ],
                [
                    'idNhom' => $idNhom,
                    'idSuKien' => $idSuKien,
                    'idDiaDiem' => $idDiaDiem,
                    'tenChienDich' => $item['tenChienDich'],
                    'moTa' => $item['moTa'],
                    'ngayTao' => $item['ngayTao'],
                    'ngayBatDau' => $item['ngayBatDau'],
                    'ngayKetThuc' => $item['ngayKetThuc'],
                    'daXacNhanCuuTro' => $item['daXacNhanCuuTro'],
                    'ghiChuXacNhan' => $item['ghiChuXacNhan'],
                    'trangThai' => $item['trangThai'],
                ]
            );
        }
    }
}