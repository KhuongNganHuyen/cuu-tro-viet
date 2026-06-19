<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function timKhoaNgoaiIdDiaDiem(): ?string
    {
        $databaseName = DB::connection()->getDatabaseName();

        $ketQua = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = 'ChiTietPhanPhoi'
              AND COLUMN_NAME = 'idDiaDiem'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ", [$databaseName]);

        return $ketQua->CONSTRAINT_NAME ?? null;
    }

    public function up(): void
    {
        $tenKhoaNgoai = $this->timKhoaNgoaiIdDiaDiem();

        if ($tenKhoaNgoai) {
            DB::statement(
                'ALTER TABLE `ChiTietPhanPhoi` DROP FOREIGN KEY `' . $tenKhoaNgoai . '`'
            );
        }

        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->unsignedInteger('idDiaDiem')
                ->nullable()
                ->change();
        });

        if (!$this->timKhoaNgoaiIdDiaDiem()) {
            Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
                $table->foreign('idDiaDiem', 'fk_chi_tiet_phan_phoi_dia_diem')
                    ->references('idDiaDiem')
                    ->on('DiaDiem')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tenKhoaNgoai = $this->timKhoaNgoaiIdDiaDiem();

        if ($tenKhoaNgoai) {
            DB::statement(
                'ALTER TABLE `ChiTietPhanPhoi` DROP FOREIGN KEY `' . $tenKhoaNgoai . '`'
            );
        }

        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->unsignedInteger('idDiaDiem')
                ->nullable(false)
                ->change();
        });

        Schema::table('ChiTietPhanPhoi', function (Blueprint $table) {
            $table->foreign('idDiaDiem', 'fk_chi_tiet_phan_phoi_dia_diem')
                ->references('idDiaDiem')
                ->on('DiaDiem');
        });
    }
};