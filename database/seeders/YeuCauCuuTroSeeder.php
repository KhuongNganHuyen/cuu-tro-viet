<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class YeuCauCuuTroSeeder extends Seeder
{
    public function run(): void
    {
        $layNguoiDung = function (string $tenDangNhap) {
            return DB::table('NguoiDung')
                ->where('tenDangNhap', $tenDangNhap)
                ->value('idNguoiDung');
        };

        $layDiaDiem = function (string $tinhThanh, string $phuongXa) {
            return DB::table('DiaDiem')
                ->where('tinhThanh', $tinhThanh)
                ->where('phuongXa', $phuongXa)
                ->value('idDiaDiem');
        };

        $yeuCaus = [
            [
                'nguoiGui' => 'HoaiAnLe',
                'diaDiem' => ['TP. Huế', 'Đan Điền'],
                'tieuDeYeuCau' => 'Cần hỗ trợ lương thực cho các hộ dân vùng ngập',
                'moTa' => 'Một số hộ dân bị ngập sâu, việc đi lại khó khăn, cần hỗ trợ mì ăn liền, gạo và nước uống trong vài ngày tới.',
                'soNguoi' => 24,
                'mucDoKhanCap' => 'Cao',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau1.jpg',
                'trangThai' => 'Chờ tiếp nhận',
                'thoiGianGui' => Carbon::now()->subDays(6)->setTime(8, 20),
            ],
            [
                'nguoiGui' => 'ThanhNhanVo',
                'diaDiem' => ['TP. Đà Nẵng', 'Thanh Khê'],
                'tieuDeYeuCau' => 'Hỗ trợ nước uống cho khu dân cư bị thiếu nước sạch',
                'moTa' => 'Sau mưa lớn, nguồn nước sinh hoạt bị ảnh hưởng, người dân cần nước uống đóng chai và bình nước sạch.',
                'soNguoi' => 35,
                'mucDoKhanCap' => 'Cao',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau2.jpg',
                'trangThai' => 'Đã tiếp nhận',
                'thoiGianGui' => Carbon::now()->subDays(5)->setTime(14, 10),
            ],
            [
                'nguoiGui' => 'GiaHuyDang',
                'diaDiem' => ['TP. Đà Nẵng', 'Phú Thuận'],
                'tieuDeYeuCau' => 'Cần áo phao và đèn pin tại khu vực ngập sâu',
                'moTa' => 'Một số tuyến đường còn ngập, người dân cần áo phao và đèn pin để di chuyển an toàn vào buổi tối.',
                'soNguoi' => 16,
                'mucDoKhanCap' => 'Khẩn cấp',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau3.jpg',
                'trangThai' => 'Đang hỗ trợ',
                'thoiGianGui' => Carbon::now()->subDays(4)->setTime(18, 35),
            ],
            [
                'nguoiGui' => 'KhanhLinhBui',
                'diaDiem' => ['Tỉnh Quảng Ngãi', 'Trường Giang'],
                'tieuDeYeuCau' => 'Cần thuốc và vật tư sơ cứu cơ bản',
                'moTa' => 'Một số người dân có biểu hiện sốt, cảm lạnh và xây xát nhẹ sau khi dọn dẹp nhà cửa, cần thuốc hạ sốt, sát khuẩn và bông băng.',
                'soNguoi' => 19,
                'mucDoKhanCap' => 'Trung bình',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau4.jpg',
                'trangThai' => 'Đã tiếp nhận',
                'thoiGianGui' => Carbon::now()->subDays(3)->setTime(9, 15),
            ],
            [
                'nguoiGui' => 'TanPhatH',
                'diaDiem' => ['TP. Huế', 'A Lưới 2'],
                'tieuDeYeuCau' => 'Hỗ trợ chăn mền cho hộ dân vùng cao',
                'moTa' => 'Thời tiết lạnh, một số hộ dân vùng cao cần chăn mền và đồ dùng giữ ấm tạm thời.',
                'soNguoi' => 28,
                'mucDoKhanCap' => 'Trung bình',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau5.jpg',
                'trangThai' => 'Hoàn thành',
                'thoiGianGui' => Carbon::now()->subDays(9)->setTime(10, 45),
            ],
            [
                'nguoiGui' => 'KhangNguyen',
                'diaDiem' => ['Tỉnh Gia Lai', 'KBang'],
                'tieuDeYeuCau' => 'Cần bạt che mưa cho điểm trú tạm',
                'moTa' => 'Điểm trú tạm cần bổ sung bạt che mưa nắng để bảo vệ khu vực sinh hoạt chung của người dân.',
                'soNguoi' => 22,
                'mucDoKhanCap' => 'Thấp',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau6.jpg',
                'trangThai' => 'Chờ tiếp nhận',
                'thoiGianGui' => Carbon::now()->subDays(2)->setTime(7, 50),
            ],
            [
                'nguoiGui' => 'BaoPhamQ',
                'diaDiem' => ['Tỉnh Quảng Ngãi', 'Cà Đam'],
                'tieuDeYeuCau' => 'Cần hỗ trợ nhu yếu phẩm cho hộ dân bị cô lập',
                'moTa' => 'Đường vào thôn bị sạt lở nhẹ, một số hộ dân cần lương khô, nước uống và thực phẩm đóng hộp.',
                'soNguoi' => 31,
                'mucDoKhanCap' => 'Cao',
                'hinhAnh' => 'yeu-cau-cuu-tro/yeucau7.jpg',
                'trangThai' => 'Đang hỗ trợ',
                'thoiGianGui' => Carbon::now()->subDay()->setTime(16, 25),
            ],
        ];

        foreach ($yeuCaus as $item) {
            $idNguoiGui = $layNguoiDung($item['nguoiGui']);
            $idDiaDiem = $layDiaDiem($item['diaDiem'][0], $item['diaDiem'][1]);

            if (!$idNguoiGui || !$idDiaDiem) {
                continue;
            }

            DB::table('YeuCauCuuTro')->updateOrInsert(
                [
                    'tieuDeYeuCau' => $item['tieuDeYeuCau'],
                ],
                [
                    'idNguoiGui' => $idNguoiGui,
                    'idDiaDiem' => $idDiaDiem,
                    'tieuDeYeuCau' => $item['tieuDeYeuCau'],
                    'moTa' => $item['moTa'],
                    'soNguoi' => $item['soNguoi'],
                    'mucDoKhanCap' => $item['mucDoKhanCap'],
                    'hinhAnh' => $item['hinhAnh'],
                    'trangThai' => $item['trangThai'],
                    'thoiGianGui' => $item['thoiGianGui'],
                ]
            );
        }
    }
}