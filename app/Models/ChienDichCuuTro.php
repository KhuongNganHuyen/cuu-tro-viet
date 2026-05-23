<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChienDichCuuTro extends Model
{
    protected $table = 'ChienDichCuuTro';
    protected $primaryKey = 'idChienDich';

    public $timestamps = false;

    protected $fillable = [
        'idNhom',
        'idThienTai',
        'idDiaDiem',
        'tenChienDich',
        'moTa',
        'ngayTao',
        'ngayBatDau',
        'ngayKetThuc',
        'daThongBaoUBND',
        'ghiChuUBND',
        'trangThai',
    ];

    public function nhom()
    {
        return $this->belongsTo(NhomTinhNguyen::class, 'idNhom', 'idNhom');
    }

    public function thienTai()
    {
        return $this->belongsTo(ThienTai::class, 'idThienTai', 'idThienTai');
    }

    public function diaDiem()
    {
        return $this->belongsTo(DiaDiem::class, 'idDiaDiem', 'idDiaDiem');
    }
}