<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThienTai extends Model
{
    protected $table = 'ThienTai';
    protected $primaryKey = 'idThienTai';

    public $timestamps = false;

    protected $fillable = [
        'tenThienTai',
        'namXayRa',
    ];
}