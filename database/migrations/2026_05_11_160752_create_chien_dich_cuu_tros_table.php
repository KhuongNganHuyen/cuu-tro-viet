<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ChienDichCuuTro', function (Blueprint $table) {
            $table->increments('idChienDich');

            $table->unsignedInteger('idNhom');
            $table->unsignedInteger('idThienTai');
            $table->unsignedInteger('idDiaDiem');

            $table->string('tenChienDich');
            $table->text('moTa')->nullable();

            $table->dateTime('ngayTao')->nullable();
            $table->date('ngayBatDau')->nullable();
            $table->date('ngayKetThuc')->nullable();

            $table->boolean('daThongBaoUBND')->default(false);
            $table->string('ghiChuUBND')->nullable();

            $table->string('trangThai')->default('Đang diễn ra');

            $table->foreign('idNhom')->references('idNhom')->on('NhomTinhNguyen');
            $table->foreign('idThienTai')->references('idThienTai')->on('ThienTai');
            $table->foreign('idDiaDiem')->references('idDiaDiem')->on('DiaDiem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ChienDichCuuTro');
    }
};