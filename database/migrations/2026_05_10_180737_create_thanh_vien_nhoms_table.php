<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ThanhVienNhom', function (Blueprint $table) {
            $table->increments('idThanhVien');
            $table->unsignedInteger('idNhom');
            $table->unsignedInteger('idNguoiDung');
            $table->string('vaiTro')->default('Thành viên');
            $table->dateTime('ngayThamGia')->nullable();

            $table->foreign('idNhom')->references('idNhom')->on('NhomTinhNguyen');
            $table->foreign('idNguoiDung')->references('idNguoiDung')->on('NguoiDung');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ThanhVienNhom');
    }
};