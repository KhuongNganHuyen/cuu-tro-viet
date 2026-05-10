<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('DiaDiem', function (Blueprint $table) {
            $table->increments('idDiaDiem');
            $table->string('tinhThanh');
            $table->string('phuongXa')->nullable();
            $table->string('chiTietDiaDiem')->nullable();
            $table->double('viDo')->nullable();
            $table->double('kinhDo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DiaDiem');
    }
};
