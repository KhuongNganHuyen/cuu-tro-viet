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
                'tenSuKien' => 'Tiếp tế nhu yếu phẩm khẩn cấp',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Tập trung gom thực phẩm ăn liền, nước uống và vật dụng thiết yếu để chuyển đến các điểm tập kết an toàn khi có tình huống cần hỗ trợ.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Điều phối nước sạch sinh hoạt',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Vận chuyển nước đóng chai, bình nước 20 lít hoặc hỗ trợ nguồn nước sinh hoạt cho khu vực thiếu nước sạch.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Tiếp sức học cụ đến trường',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Gom và tặng sách vở, cặp sách, xe đạp và đồ dùng học tập cho học sinh có hoàn cảnh khó khăn.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Quyên góp đồ ấm mùa đông',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Thu gom chăn màn, áo khoác, mũ len, tất chân để gửi tặng người dân vùng cao hoặc người vô gia cư khi chuyển mùa.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Bếp ăn thiện nguyện và suất ăn miễn phí',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Kết nối nguồn lực cung cấp gạo, rau củ và nhu yếu phẩm cho các bếp ăn từ thiện phục vụ người khó khăn.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Tiếp tế vật dụng mái ấm và trại trẻ mồ côi',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Quyên góp sữa, tã bỉm, đồ chơi, thực phẩm dinh dưỡng và vật dụng cần thiết cho các cơ sở bảo trợ trẻ em.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Trợ giúp nhu yếu phẩm người già neo đơn',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Hỗ trợ gạo, dầu ăn, nước mắm, mì chính và các vật dụng sinh hoạt cho người già neo đơn, người yếu thế.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Hỗ trợ vật dụng cho bệnh nhân nghèo',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Tặng các vật dụng hậu cần phi y tế như quạt mini, phích nước, khăn mặt, suất ăn cho bệnh nhân nghèo.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Trao tặng sinh kế và công cụ lao động',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Hỗ trợ hạt giống, cây giống, con giống hoặc công cụ lao động cơ bản cho hộ nghèo tự ổn định sinh kế.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Hỗ trợ tái thiết sau sự cố và hỏa hoạn',
                'loaiSuKien' => 'Thường nhật',
                'moTa' => 'Gom quần áo, đồ gia dụng và vật dụng sinh hoạt giúp các gia đình ổn định lại cuộc sống sau sự cố.',
                'trangThai' => 'Đang diễn ra',
            ],

            [
                'tenSuKien' => 'Bão số 3 và lũ quét Thừa Thiên Huế',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Hoàn lưu bão gây mưa lớn, ngập sâu vùng hạ lưu sông Hương và sạt lở cô lập một số khu vực miền núi.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Ngập lụt đô thị Đà Nẵng',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Mưa lớn gây ngập sâu tại các vùng trũng, nhiều hộ dân cần hỗ trợ lương thực, nước uống và vật dụng thiết yếu.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Lũ hạ lưu sông Thu Bồn',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Nước lũ dâng cao tại khu vực hạ lưu, ảnh hưởng sinh hoạt và nhu cầu tiếp tế của nhiều hộ dân.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Sạt lở đất vùng núi Phước Sơn',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Sạt lở gây chia cắt giao thông, một số hộ dân bị cô lập và thiếu thốn lương thực tạm thời.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Rét đậm và lốc xoáy ven biển Huế',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Không khí lạnh kèm lốc xoáy làm ảnh hưởng nhà cửa, sinh kế và nhu cầu lương thực của người dân ven biển.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Hạn hán và xâm nhập mặn hạ lưu sông Cầu Đỏ',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Mặn xâm nhập sâu làm thiếu nước sinh hoạt tại một số khu vực cuối nguồn, cần điều phối nước sạch.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Cháy lớn khu dân cư lao động nghèo Thanh Khê',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Hỏa hoạn gây thiệt hại nhà cửa, nhiều gia đình cần hỗ trợ nhu yếu phẩm và vật dụng sinh hoạt ban đầu.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Dịch sốt xuất huyết tại Điện Bàn',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Dịch bệnh bùng phát tại khu dân cư, cần hỗ trợ vật tư vệ sinh, màn chống muỗi và đồ dùng phòng dịch.',
                'trangThai' => 'Đã kết thúc',
            ],
            [
                'tenSuKien' => 'Áp thấp nhiệt đới và lũ quét cục bộ Nam Đông',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Mưa lớn kéo dài gây chia cắt đập tràn, nước dâng nhanh vào nhà dân, cần tiếp tế lương thực khẩn cấp.',
                'trangThai' => 'Đang diễn ra',
            ],
            [
                'tenSuKien' => 'Siêu bão áp sát đất liền Trung Bộ',
                'loaiSuKien' => 'Khẩn cấp',
                'moTa' => 'Dự báo bão mạnh ảnh hưởng vùng biển Huế - Đà Nẵng, cần chuẩn bị nguồn lực hỗ trợ di dời và tiếp tế.',
                'trangThai' => 'Sắp diễn ra',
            ],
        ];

        foreach ($suKiens as $suKien) {
            DB::table('SuKienCuuTro')->updateOrInsert(
                ['tenSuKien' => $suKien['tenSuKien']],
                array_merge($suKien, [
                    'ngayTao' => now(),
                ])
            );
        }
    }
}