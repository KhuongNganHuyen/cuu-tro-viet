<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DotPhanPhoi', function (Blueprint $table) {
            $table->increments('idDotPhanPhoi');

            $table->unsignedInteger('idChienDich');

            $table->dateTime('ngayPhanPhoi')->nullable();
            $table->string('trangThai')->nullable();
            $table->string('ghiChu')->nullable();

            $table->foreign('idChienDich')->references('idChienDich')->on('ChienDichCuuTro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DotPhanPhoi');
    }
};