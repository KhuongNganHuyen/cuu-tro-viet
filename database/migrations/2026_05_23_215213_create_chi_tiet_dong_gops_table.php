<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ChiTietDongGop', function (Blueprint $table) {
            $table->increments('idChiTietDongGop');

            $table->unsignedInteger('idDongGop');
            $table->unsignedInteger('idHangHoa');

            $table->decimal('soLuong', 15, 2);
            $table->date('hanSuDung')->nullable();
            $table->string('trangThai')->default('Chờ xác nhận');

            $table->foreign('idDongGop')->references('idDongGop')->on('DongGop');
            $table->foreign('idHangHoa')->references('idHangHoa')->on('HangHoa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ChiTietDongGop');
    }
};