<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapNhatChienDich extends Model
{
    protected $table = 'CapNhatChienDich';
    protected $primaryKey = 'idCapNhat';

    public $timestamps = false;

    protected $fillable = [
        'idChienDich',
        'idThanhVien',
        'noiDung',
        'hinhAnh',
        'thoiGianCapNhat',
    ];

    public function chienDich()
    {
        return $this->belongsTo(ChienDichCuuTro::class, 'idChienDich', 'idChienDich');
    }

    public function thanhVien()
    {
        return $this->belongsTo(ThanhVienNhom::class, 'idThanhVien', 'idThanhVien');
    }
}