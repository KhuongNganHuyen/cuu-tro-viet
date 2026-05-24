<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DongGop', function (Blueprint $table) {
            $table->increments('idDongGop');

            $table->unsignedInteger('idChienDich');
            $table->unsignedInteger('idNguoiUngHo');
            $table->unsignedInteger('idNguoiTiepNhan')->nullable();

            $table->string('ghiChu')->nullable();
            $table->dateTime('thoiGianDongGop')->nullable();

            $table->foreign('idChienDich')->references('idChienDich')->on('ChienDichCuuTro');
            $table->foreign('idNguoiUngHo')->references('idNguoiDung')->on('NguoiDung');
            $table->foreign('idNguoiTiepNhan')->references('idThanhVien')->on('ThanhVienNhom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DongGop');
    }
};