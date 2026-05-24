<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DongGop extends Model
{
    protected $table = 'DongGop';
    protected $primaryKey = 'idDongGop';

    public $timestamps = false;

    protected $fillable = [
        'idChienDich',
        'idNguoiUngHo',
        'idNguoiTiepNhan',
        'ghiChu',
        'thoiGianDongGop',
    ];

    public function chienDich()
    {
        return $this->belongsTo(ChienDichCuuTro::class, 'idChienDich', 'idChienDich');
    }

    public function nguoiUngHo()
    {
        return $this->belongsTo(NguoiDung::class, 'idNguoiUngHo', 'idNguoiDung');
    }

    public function thanhVienTiepNhan()
    {
        return $this->belongsTo(ThanhVienNhom::class, 'idNguoiTiepNhan', 'idThanhVien');
    }

    public function chiTietDongGops()
    {
        return $this->hasMany(ChiTietDongGop::class, 'idDongGop', 'idDongGop');
    }
}