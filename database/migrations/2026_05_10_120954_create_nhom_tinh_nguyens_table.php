<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('NhomTinhNguyen', function (Blueprint $table) {
            $table->increments('idNhom');
            $table->string('tenNhom');
            $table->string('moTa')->nullable();
            $table->unsignedInteger('idNhomTruong');
            $table->unsignedInteger('idDiaDiem');
            $table->string('trangThai')->default('Đang hoạt động');
            $table->dateTime('ngayTao')->nullable();

            $table->foreign('idNhomTruong')->references('idNguoiDung')->on('NguoiDung');
            $table->foreign('idDiaDiem')->references('idDiaDiem')->on('DiaDiem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('NhomTinhNguyen');
    }
};