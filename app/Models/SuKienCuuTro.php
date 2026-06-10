<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuKienCuuTro extends Model
{
    protected $table = 'SuKienCuuTro';
    protected $primaryKey = 'idSuKien';

    public $timestamps = false;

    protected $fillable = [
        'tenSuKien',
        'loaiSuKien',
        'moTa',
        'trangThai',
        'ngayTao',
    ];

    public function chienDichs()
    {
        return $this->hasMany(ChienDichCuuTro::class, 'idSuKien', 'idSuKien');
    }
}