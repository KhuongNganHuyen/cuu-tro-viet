<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('NguonLucChienDich', function (Blueprint $table) {
            $table->increments('idNguonLuc');

            $table->unsignedInteger('idChienDich');
            $table->unsignedInteger('idHangHoa');

            $table->decimal('soLuongHienCo', 15, 2)->default(0);
            $table->date('hanSuDung')->nullable();
            $table->string('trangThai')->nullable();
            $table->dateTime('ngayCapNhat')->nullable();

            $table->foreign('idChienDich')->references('idChienDich')->on('ChienDichCuuTro');
            $table->foreign('idHangHoa')->references('idHangHoa')->on('HangHoa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('NguonLucChienDich');
    }
};