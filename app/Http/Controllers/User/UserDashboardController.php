<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Tạm thời dùng dữ liệu giả để dựng giao diện trước.
        // Sau khi làm đăng nhập, các số này sẽ lấy theo người dùng đang đăng nhập.
        $thongKe = [
            'soYeuCau' => 0,
            'soDongGop' => 0,
            'soNhomThamGia' => 0,
            'soNhomChoDuyet' => 0,
        ];

        return view('user.dashboard', compact('thongKe'));
    }
}