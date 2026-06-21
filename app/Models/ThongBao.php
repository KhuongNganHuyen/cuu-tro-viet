<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    protected $table = 'ThongBao';
    protected $primaryKey = 'idThongBao';
    public $timestamps = false;

    protected $fillable = [
        'tieuDe',
        'noiDung',
        'doiTuong',
        'nguoiTao',
        'idNguoiNhan',
        'anhDaiDien',
        'hinhAnh',
        'duongDan',
        'thoiGianTao',
        'trangThai',
    ];
}