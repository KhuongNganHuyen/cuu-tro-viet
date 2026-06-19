<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->string('loaiPhanPhoi', 50)
                ->nullable()
                ->after('idTiepNhan');
        });

        DB::table('ChiTietPhanPhoi')
            ->whereNotNull('idDiaDiem')
            ->whereNull('idTiepNhan')
            ->update([
                'loaiPhanPhoi' => 'Địa điểm',
            ]);

        DB::table('ChiTietPhanPhoi')
            ->whereNotNull('idDiaDiem')
            ->whereNotNull('idTiepNhan')
            ->update([
                'loaiPhanPhoi' => 'Địa điểm và yêu cầu',
            ]);

        DB::table('ChiTietPhanPhoi')
            ->whereNull('idDiaDiem')
            ->whereNotNull('idTiepNhan')
            ->update([
                'loaiPhanPhoi' => 'Yêu cầu',
            ]);
    }

    public function down(): void
    {
        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->dropColumn('loaiPhanPhoi');
        });
    }
};