<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->increments('idChiTietPhanPhoi');

            $table->unsignedInteger('idDotPhanPhoi');
            $table->unsignedInteger('idNguonLuc');
            $table->unsignedInteger('idDiaDiem');
            $table->unsignedInteger('idTiepNhan')->nullable();

            $table->string('nguoiNhan')->nullable();
            $table->decimal('soLuongGiao', 15, 2);
            $table->string('hinhAnh')->nullable();
            $table->dateTime('thoiGianGiao')->nullable();
            $table->string('trangThai')->nullable();

            $table->foreign('idDotPhanPhoi')->references('idDotPhanPhoi')->on('DotPhanPhoi');
            $table->foreign('idNguonLuc')->references('idNguonLuc')->on('NguonLucChienDich');
            $table->foreign('idDiaDiem')->references('idDiaDiem')->on('DiaDiem');
            $table->foreign('idTiepNhan')->references('idTiepNhan')->on('TiepNhanYeuCau');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ChiTietPhanPhoi');
    }
};