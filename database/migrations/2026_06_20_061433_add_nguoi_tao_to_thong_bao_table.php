<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ThongBao', function (Blueprint $table) {
            if (!Schema::hasColumn('ThongBao', 'nguoiTao')) {
                $table->string('nguoiTao')->nullable()->after('doiTuong');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ThongBao', function (Blueprint $table) {
            if (Schema::hasColumn('ThongBao', 'nguoiTao')) {
                $table->dropColumn('nguoiTao');
            }
        });
    }
};
