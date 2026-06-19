<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->dropColumn('hinhAnh');
        });
    }

    public function down(): void
    {
        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->string('hinhAnh')->nullable()->after('soLuongGiao');
        });
    }
};