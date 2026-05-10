<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhVienNhom extends Model
{
    protected $table = 'ThanhVienNhom';
    protected $primaryKey = 'idThanhVien';

    public $timestamps = false;

    protected $fillable = [
        'idNhom',
        'idNguoiDung',
        'vaiTro',
        'ngayThamGia',
    ];

    public function nhom()
    {
        return $this->belongsTo(NhomTinhNguyen::class, 'idNhom', 'idNhom');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'idNguoiDung', 'idNguoiDung');
    }
}