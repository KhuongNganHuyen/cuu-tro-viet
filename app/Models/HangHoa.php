<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HangHoa extends Model
{
    protected $table = 'HangHoa';
    protected $primaryKey = 'idHangHoa';

    public $timestamps = false;

    protected $fillable = [
        'idDanhMucHang',
        'idNhom',
        'tenHangHoa',
        'donViTinh',
        'trangThai',
    ];

    public function danhMucHang()
    {
        return $this->belongsTo(DanhMucHang::class, 'idDanhMucHang', 'idDanhMucHang');
    }

    public function nhom()
    {
        return $this->belongsTo(NhomTinhNguyen::class, 'idNhom', 'idNhom');
    }
}