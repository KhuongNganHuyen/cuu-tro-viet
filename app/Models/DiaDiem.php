<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaDiem extends Model
{
    protected $table = 'DiaDiem';
    protected $primaryKey = 'idDiaDiem';

    public $timestamps = false;

    protected $fillable = [
        'tinhThanh',
        'phuongXa',
        'chiTietDiaDiem',
        'viDo',
        'kinhDo',
    ];
}