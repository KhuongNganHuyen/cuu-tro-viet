<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    protected $table = 'NguoiDung';
    protected $primaryKey = 'idNguoiDung';

    public $timestamps = false;

    protected $fillable = [
        'hoTen',
        'tenDangNhap',
        'matKhau',
        'gioiTinh',
        'anhDaiDien',
        'ngaySinh',
        'sdt',
        'email',
        'vaiTro',
        'trangThai',
        'ngayTao',
    ];
}