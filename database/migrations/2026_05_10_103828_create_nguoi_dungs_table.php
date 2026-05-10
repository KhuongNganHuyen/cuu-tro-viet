<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('NguoiDung', function (Blueprint $table) {
            $table->increments('idNguoiDung');
            $table->string('hoTen');
            $table->string('tenDangNhap')->unique();
            $table->string('matKhau');
            $table->string('gioiTinh')->nullable();
            $table->string('anhDaiDien')->nullable();
            $table->date('ngaySinh')->nullable();
            $table->string('sdt', 10)->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('vaiTro')->default('Người dùng');
            $table->string('trangThai')->default('Hoạt động');
            $table->dateTime('ngayTao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('NguoiDung');
    }
};