<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiepNhanYeuCau extends Model
{
    protected $table = 'TiepNhanYeuCau';
    protected $primaryKey = 'idTiepNhan';

    public $timestamps = false;

    protected $fillable = [
        'idYeuCau',
        'idChienDich',
        'idNhom',
        'thoiGianTiepNhan',
        'thoiGianDuKienHoTro',
        'noiDungDamNhan',
        'trangThai',
    ];

    public function yeuCau()
    {
        return $this->belongsTo(YeuCauCuuTro::class, 'idYeuCau', 'idYeuCau');
    }

    public function chienDich()
    {
        return $this->belongsTo(ChienDichCuuTro::class, 'idChienDich', 'idChienDich');
    }

    public function nhom()
    {
        return $this->belongsTo(NhomTinhNguyen::class, 'idNhom', 'idNhom');
    }
}