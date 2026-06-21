<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ThongBao', function (Blueprint $table) {
            if (!Schema::hasColumn('ThongBao', 'anhDaiDien')) {
                $table->string('anhDaiDien')->nullable()->after('idNguoiNhan');
            }

            if (!Schema::hasColumn('ThongBao', 'hinhAnh')) {
                $table->string('hinhAnh')->nullable()->after('anhDaiDien');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ThongBao', function (Blueprint $table) {
            if (Schema::hasColumn('ThongBao', 'anhDaiDien')) {
                $table->dropColumn('anhDaiDien');
            }

            if (Schema::hasColumn('ThongBao', 'hinhAnh')) {
                $table->dropColumn('hinhAnh');
            }
        });
    }
};