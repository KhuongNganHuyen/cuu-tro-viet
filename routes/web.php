<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\ThienTaiController;
use App\Http\Controllers\DanhMucHangController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NhomTinhNguyenController;
use App\Http\Controllers\ThanhVienNhomController;
use App\Http\Controllers\ChienDichCuuTroController;
use App\Http\Controllers\YeuCauCuuTroController;
use App\Http\Controllers\TiepNhanYeuCauController;

/*
|--------------------------------------------------------------------------
| Trang chính
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Admin - Tổng quan
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin - Danh mục hệ thống
|--------------------------------------------------------------------------
*/

Route::resource('/admin/dia-diem', DiaDiemController::class);
Route::resource('/admin/thien-tai', ThienTaiController::class);
Route::resource('/admin/danh-muc-hang', DanhMucHangController::class);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý người dùng
|--------------------------------------------------------------------------
*/

Route::patch('/admin/nguoi-dung/{id}/doi-trang-thai', [NguoiDungController::class, 'doiTrangThai']);
Route::resource('/admin/nguoi-dung', NguoiDungController::class);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý nhóm tình nguyện
|--------------------------------------------------------------------------
*/

Route::patch('/admin/nhom-tinh-nguyen/{id}/doi-trang-thai', [NhomTinhNguyenController::class, 'doiTrangThai']);
Route::resource('/admin/nhom-tinh-nguyen', NhomTinhNguyenController::class);

/*
|--------------------------------------------------------------------------
| Admin - Thành viên nhóm tình nguyện
|--------------------------------------------------------------------------
| Tạm giữ route này để xem/test dữ liệu.
| Về nghiệp vụ chính thức, chức năng thêm/xóa thành viên sẽ chuyển sang
| giao diện nhóm trưởng/tình nguyện viên sau này.
|--------------------------------------------------------------------------
*/

Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'index']);
Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien/create', [ThanhVienNhomController::class, 'create']);
Route::post('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'store']);
Route::delete('/admin/thanh-vien-nhom/{idThanhVien}', [ThanhVienNhomController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý chiến dịch cứu trợ
|--------------------------------------------------------------------------
*/

Route::resource('/admin/chien-dich', ChienDichCuuTroController::class);

/*
|--------------------------------------------------------------------------
| Admin - Quản lý yêu cầu cứu trợ
|--------------------------------------------------------------------------
*/

Route::resource('/admin/yeu-cau-cuu-tro', YeuCauCuuTroController::class);

/*
|--------------------------------------------------------------------------
| Admin - Tiếp nhận yêu cầu cứu trợ
|--------------------------------------------------------------------------
*/

Route::get('/admin/tiep-nhan-yeu-cau', [TiepNhanYeuCauController::class, 'index']);
Route::get('/admin/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [TiepNhanYeuCauController::class, 'create']);
Route::post('/admin/yeu-cau-cuu-tro/{idYeuCau}/tiep-nhan', [TiepNhanYeuCauController::class, 'store']);
Route::delete('/admin/tiep-nhan-yeu-cau/{idTiepNhan}', [TiepNhanYeuCauController::class, 'destroy']);