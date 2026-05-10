<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMucHang extends Model
{
    protected $table = 'DanhMucHang';
    protected $primaryKey = 'idDanhMucHang';

    public $timestamps = false;

    protected $fillable = [
        'tenDanhMucHang',
    ];
}