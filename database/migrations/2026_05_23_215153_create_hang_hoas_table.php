<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('HangHoa', function (Blueprint $table) {
            $table->increments('idHangHoa');
            $table->unsignedInteger('idDanhMucHang');
            $table->string('tenHangHoa');
            $table->string('donViTinh');
            $table->string('trangThai')->default('Đang sử dụng');

            $table->foreign('idDanhMucHang')->references('idDanhMucHang')->on('DanhMucHang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('HangHoa');
    }
};