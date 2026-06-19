<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('NguonLucChienDich', 'hanSuDung')) {
            Schema::table('NguonLucChienDich', function (Blueprint $table) {
                $table->dropColumn('hanSuDung');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('NguonLucChienDich', 'hanSuDung')) {
            Schema::table('NguonLucChienDich', function (Blueprint $table) {
                $table->date('hanSuDung')->nullable()->after('soLuongHienCo');
            });
        }
    }
};