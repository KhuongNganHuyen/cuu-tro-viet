<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauCuuTro extends Model
{
    protected $table = 'YeuCauCuuTro';
    protected $primaryKey = 'idYeuCau';

    public $timestamps = false;

    protected $fillable = [
        'idNguoiGui',
        'idDiaDiem',
        'loaiYeuCau',
        'moTa',
        'soHoDan',
        'mucDoKhanCap',
        'hinhAnh',
        'trangThai',
        'thoiGianGui',
    ];

    public function nguoiGui()
    {
        return $this->belongsTo(NguoiDung::class, 'idNguoiGui', 'idNguoiDung');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'idDiaDiem', 'idDiaDiem');
    }

    public function tiepNhans()
    {
        return $this->hasMany(TiepNhanYeuCau::class, 'idYeuCau', 'idYeuCau');
    }
}