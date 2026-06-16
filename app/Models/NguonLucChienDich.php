<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguonLucChienDich extends Model
{
    protected $table = 'NguonLucChienDich';
    protected $primaryKey = 'idNguonLuc';

    public $timestamps = false;

    protected $fillable = [
        'idChienDich',
        'idHangHoa',
        'soLuongCanKeuGoi',
        'soLuongDaNhan',
        'soLuongHienCo',
        'hanSuDung',
        'trangThai',
        'ngayCapNhat',
    ];

    public function chienDich()
    {
        return $this->belongsTo(
            ChienDichCuuTro::class,
            'idChienDich',
            'idChienDich'
        );
    }

    public function hangHoa()
    {
        return $this->belongsTo(
            HangHoa::class,
            'idHangHoa',
            'idHangHoa'
        );
    }
}