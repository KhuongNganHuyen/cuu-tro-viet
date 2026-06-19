<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChienDichCuuTro extends Model
{
    protected $table = 'ChienDichCuuTro';
    protected $primaryKey = 'idChienDich';

    public $timestamps = false;

    protected $fillable = [
        'idNhom',
        'idSuKien',
        'idDiaDiem',
        'tenChienDich',
        'moTa',
        'ngayTao',
        'ngayBatDau',
        'ngayKetThuc',
        'daXacNhanCuuTro',
        'ghiChuXacNhan',
        'trangThai',
    ];

    public function nhom()
    {
        return $this->belongsTo(NhomTinhNguyen::class, 'idNhom', 'idNhom');
    }

    public function suKien()
    {
        return $this->belongsTo(SuKienCuuTro::class, 'idSuKien', 'idSuKien');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'idDiaDiem', 'idDiaDiem');
    }

    public function nguonLucs()
    {
        return $this->hasMany(NguonLucChienDich::class, 'idChienDich', 'idChienDich');
    }
}