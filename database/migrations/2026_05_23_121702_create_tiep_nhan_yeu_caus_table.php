<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TiepNhanYeuCau', function (Blueprint $table) {
            $table->increments('idTiepNhan');

            $table->unsignedInteger('idYeuCau');
            $table->unsignedInteger('idChienDich');
            $table->unsignedInteger('idNhom');

            $table->text('noiDungDamNhan')->nullable();
            $table->dateTime('thoiGianTiepNhan')->nullable();
            $table->date('thoiGianDuKienHoTro')->nullable();
            $table->string('trangThai')->default('Đã tiếp nhận');

            $table->foreign('idYeuCau')->references('idYeuCau')->on('YeuCauCuuTro');
            $table->foreign('idChienDich')->references('idChienDich')->on('ChienDichCuuTro');
            $table->foreign('idNhom')->references('idNhom')->on('NhomTinhNguyen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TiepNhanYeuCau');
    }
};