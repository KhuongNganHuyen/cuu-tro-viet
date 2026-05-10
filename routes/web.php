<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\ThienTaiController;
use App\Http\Controllers\DanhMucHangController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NhomTinhNguyenController;
use App\Http\Controllers\ThanhVienNhomController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::resource('/admin/dia-diem', DiaDiemController::class);
Route::resource('/admin/thien-tai', ThienTaiController::class);
Route::resource('/admin/danh-muc-hang', DanhMucHangController::class);
Route::resource('/admin/nguoi-dung', NguoiDungController::class);
Route::resource('/admin/nhom-tinh-nguyen', NhomTinhNguyenController::class);
Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'index']);
Route::get('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien/create', [ThanhVienNhomController::class, 'create']);
Route::post('/admin/nhom-tinh-nguyen/{idNhom}/thanh-vien', [ThanhVienNhomController::class, 'store']);
Route::delete('/admin/thanh-vien-nhom/{idThanhVien}', [ThanhVienNhomController::class, 'destroy']);