<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Đổi bảng ThienTai thành SuKienCuuTro
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('ThienTai') && !Schema::hasTable('SuKienCuuTro')) {
            Schema::rename('ThienTai', 'SuKienCuuTro');
        }

        if (Schema::hasTable('SuKienCuuTro')) {
            Schema::table('SuKienCuuTro', function (Blueprint $table) {
                if (Schema::hasColumn('SuKienCuuTro', 'idThienTai')) {
                    $table->renameColumn('idThienTai', 'idSuKien');
                }

                if (Schema::hasColumn('SuKienCuuTro', 'tenThienTai')) {
                    $table->renameColumn('tenThienTai', 'tenSuKien');
                }

                if (Schema::hasColumn('SuKienCuuTro', 'namXayRa')) {
                    $table->dropColumn('namXayRa');
                }
            });

            Schema::table('SuKienCuuTro', function (Blueprint $table) {
                if (!Schema::hasColumn('SuKienCuuTro', 'loaiSuKien')) {
                    $table->string('loaiSuKien', 50)->default('Thường nhật')->after('tenSuKien');
                }

                if (!Schema::hasColumn('SuKienCuuTro', 'moTa')) {
                    $table->text('moTa')->nullable()->after('loaiSuKien');
                }

                if (!Schema::hasColumn('SuKienCuuTro', 'trangThai')) {
                    $table->string('trangThai', 50)->default('Đang diễn ra')->after('moTa');
                }

                if (!Schema::hasColumn('SuKienCuuTro', 'ngayTao')) {
                    $table->dateTime('ngayTao')->nullable()->after('trangThai');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Cập nhật bảng ChienDichCuuTro
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('ChienDichCuuTro')) {
            Schema::table('ChienDichCuuTro', function (Blueprint $table) {
                if (Schema::hasColumn('ChienDichCuuTro', 'idThienTai')) {
                    $table->renameColumn('idThienTai', 'idSuKien');
                }

                if (Schema::hasColumn('ChienDichCuuTro', 'daThongBaoUBND')) {
                    $table->renameColumn('daThongBaoUBND', 'daXacNhanCuuTro');
                }

                if (Schema::hasColumn('ChienDichCuuTro', 'ghiChuUBND')) {
                    $table->renameColumn('ghiChuUBND', 'ghiChuXacNhan');
                }
            });

            // Chuyển kiểu ghi chú sang TEXT nếu cần.
            // Một số DB có thể không hỗ trợ change() nếu thiếu doctrine/dbal.
            // Nếu lỗi ở dòng này, báo mình để đổi sang DB::statement.
            Schema::table('ChienDichCuuTro', function (Blueprint $table) {
                if (Schema::hasColumn('ChienDichCuuTro', 'ghiChuXacNhan')) {
                    $table->text('ghiChuXacNhan')->nullable()->change();
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Cập nhật bảng YeuCauCuuTro
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('YeuCauCuuTro')) {
            Schema::table('YeuCauCuuTro', function (Blueprint $table) {
                if (Schema::hasColumn('YeuCauCuuTro', 'loaiYeuCau')) {
                    $table->renameColumn('loaiYeuCau', 'tieuDeYeuCau');
                }

                if (Schema::hasColumn('YeuCauCuuTro', 'soHoDan')) {
                    $table->renameColumn('soHoDan', 'soNguoi');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Cập nhật bảng NguonLucChienDich
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('NguonLucChienDich')) {
            Schema::table('NguonLucChienDich', function (Blueprint $table) {
                if (!Schema::hasColumn('NguonLucChienDich', 'soLuongCanKeuGoi')) {
                    $table->decimal('soLuongCanKeuGoi', 15, 2)->default(0)->after('idHangHoa');
                }

                if (!Schema::hasColumn('NguonLucChienDich', 'soLuongDaNhan')) {
                    $table->decimal('soLuongDaNhan', 15, 2)->default(0)->after('soLuongCanKeuGoi');
                }
            });

            // Gán dữ liệu cũ: coi số lượng hiện có cũ là đã nhận.
            DB::table('NguonLucChienDich')->update([
                'soLuongDaNhan' => DB::raw('soLuongHienCo'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Cập nhật bảng HangHoa
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('HangHoa')) {
            Schema::table('HangHoa', function (Blueprint $table) {
                if (!Schema::hasColumn('HangHoa', 'idNhom')) {
                    $table->integer('idNhom')->nullable()->after('idDanhMucHang');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Cập nhật bảng NhomTinhNguyen
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('NhomTinhNguyen')) {
            Schema::table('NhomTinhNguyen', function (Blueprint $table) {
                if (!Schema::hasColumn('NhomTinhNguyen', 'anhDaiDien')) {
                    $table->string('anhDaiDien')->nullable()->after('moTa');
                }
            });

            Schema::table('NhomTinhNguyen', function (Blueprint $table) {
                if (Schema::hasColumn('NhomTinhNguyen', 'moTa')) {
                    $table->text('moTa')->nullable()->change();
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Chỉnh TEXT cho các ghi chú nếu cần
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('DongGop')) {
            Schema::table('DongGop', function (Blueprint $table) {
                if (Schema::hasColumn('DongGop', 'ghiChu')) {
                    $table->text('ghiChu')->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('DotPhanPhoi')) {
            Schema::table('DotPhanPhoi', function (Blueprint $table) {
                if (Schema::hasColumn('DotPhanPhoi', 'ghiChu')) {
                    $table->text('ghiChu')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {

    }
};