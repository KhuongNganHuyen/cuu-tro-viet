<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiaDiemController;
use App\Http\Controllers\ThienTaiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::resource('/admin/dia-diem', DiaDiemController::class);
Route::resource('/admin/thien-tai', ThienTaiController::class);