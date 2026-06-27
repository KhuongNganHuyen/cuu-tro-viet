<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ThongBao', function (Blueprint $table) {
            $table->id('idThongBao');

            $table->string('tieuDe');
            $table->text('noiDung')->nullable();

            $table->string('doiTuong')->nullable(); 
            $table->unsignedBigInteger('idNguoiNhan')->nullable();

            $table->string('nguoiTao')->nullable();
            $table->string('anhDaiDien')->nullable();
            $table->string('hinhAnh')->nullable();
            $table->string('duongDan')->nullable();

            $table->dateTime('thoiGianTao')->nullable();
            $table->string('trangThai')->default('Hiển thị');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ThongBao');
    }
};