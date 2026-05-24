<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDongGop extends Model
{
    protected $table = 'ChiTietDongGop';
    protected $primaryKey = 'idChiTietDongGop';

    public $timestamps = false;

    protected $fillable = [
        'idDongGop',
        'idHangHoa',
        'soLuong',
        'hanSuDung',
        'trangThai',
    ];

    public function dongGop()
    {
        return $this->belongsTo(DongGop::class, 'idDongGop', 'idDongGop');
    }

    public function hangHoa()
    {
        return $this->belongsTo(HangHoa::class, 'idHangHoa', 'idHangHoa');
    }
}