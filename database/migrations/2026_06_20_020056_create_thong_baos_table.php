<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ThongBao')) {
            Schema::create('ThongBao', function (Blueprint $table) {
                $table->id('idThongBao');
                $table->string('tieuDe');
                $table->text('noiDung')->nullable();
                $table->string('doiTuong')->default('Tất cả');
                $table->unsignedBigInteger('idNguoiNhan')->nullable();
                $table->string('duongDan')->nullable();
                $table->dateTime('thoiGianTao')->nullable();
                $table->string('trangThai')->default('Hiển thị');

                $table->foreign('idNguoiNhan')
                    ->references('idNguoiDung')
                    ->on('NguoiDung')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ThongBao');
    }
};