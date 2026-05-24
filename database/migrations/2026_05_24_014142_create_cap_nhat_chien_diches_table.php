<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CapNhatChienDich', function (Blueprint $table) {
            $table->increments('idCapNhat');

            $table->unsignedInteger('idChienDich');
            $table->unsignedInteger('idThanhVien');

            $table->text('noiDung');
            $table->string('hinhAnh')->nullable();
            $table->dateTime('thoiGianCapNhat')->nullable();

            $table->foreign('idChienDich')->references('idChienDich')->on('ChienDichCuuTro');
            $table->foreign('idThanhVien')->references('idThanhVien')->on('ThanhVienNhom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CapNhatChienDich');
    }
};