<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('YeuCauCuuTro', function (Blueprint $table) {
            $table->increments('idYeuCau');

            $table->unsignedInteger('idNguoiGui');
            $table->unsignedInteger('idDiaDiem');

            $table->string('loaiYeuCau');
            $table->text('moTa');
            $table->integer('soHoDan')->nullable();
            $table->string('mucDoKhanCap')->nullable();
            $table->string('hinhAnh')->nullable();
            $table->string('trangThai')->default('Chờ tiếp nhận');
            $table->dateTime('thoiGianGui')->nullable();

            $table->foreign('idNguoiGui')->references('idNguoiDung')->on('NguoiDung');
            $table->foreign('idDiaDiem')->references('idDiaDiem')->on('DiaDiem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('YeuCauCuuTro');
    }
};