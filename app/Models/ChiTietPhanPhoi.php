<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietPhanPhoi extends Model
{
    protected $table = 'ChiTietPhanPhoi';
    protected $primaryKey = 'idChiTietPhanPhoi';

    public $timestamps = false;

    protected $fillable = [
        'idDotPhanPhoi',
        'idNguonLuc',
        'idDiaDiem',
        'idTiepNhan',
        'loaiPhanPhoi',
        'nguoiNhan',
        'soLuongGiao',
        'thoiGianGiao',
        'trangThai',
    ];

    public function dotPhanPhoi()
    {
        return $this->belongsTo(DotPhanPhoi::class, 'idDotPhanPhoi', 'idDotPhanPhoi');
    }

    public function nguonLuc()
    {
        return $this->belongsTo(NguonLucChienDich::class, 'idNguonLuc', 'idNguonLuc');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'idDiaDiem', 'idDiaDiem');
    }

    public function tiepNhan()
    {
        return $this->belongsTo(TiepNhanYeuCau::class, 'idTiepNhan', 'idTiepNhan');
    }
}