<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DotPhanPhoi extends Model
{
    protected $table = 'DotPhanPhoi';
    protected $primaryKey = 'idDotPhanPhoi';

    public $timestamps = false;

    protected $fillable = [
        'idChienDich',
        'ngayPhanPhoi',
        'trangThai',
        'ghiChu',
    ];

    public function chienDich()
    {
        return $this->belongsTo(ChienDichCuuTro::class, 'idChienDich', 'idChienDich');
    }

    public function chiTietPhanPhois()
    {
        return $this->hasMany(ChiTietPhanPhoi::class, 'idDotPhanPhoi', 'idDotPhanPhoi');
    }
}