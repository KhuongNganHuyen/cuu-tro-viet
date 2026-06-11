<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhomTinhNguyen extends Model
{
    protected $table = 'NhomTinhNguyen';
    protected $primaryKey = 'idNhom';

    public $timestamps = false;

    protected $fillable = [
        'tenNhom',
        'moTa',
        'anhDaiDien',
        'idNhomTruong',
        'idDiaDiem',
        'trangThai',
        'ngayTao',
    ];

    public function nhomTruong()
    {
        return $this->belongsTo(NguoiDung::class, 'idNhomTruong', 'idNguoiDung');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'idDiaDiem', 'idDiaDiem');
    }

    public function thanhViens()
    {
        return $this->hasMany(ThanhVienNhom::class, 'idNhom', 'idNhom');
    }
}