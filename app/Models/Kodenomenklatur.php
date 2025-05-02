<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomenklatur extends Model
{
    use HasFactory;

    protected $table = 'kode_nomenklatur';

    protected $fillable = [
        'jenis_nomenklatur',
        'nomor_kode',
        'nomenklatur',
        'id_bidang_urusan',
        'id_program',
        'id_kegiatan',
        'id_subkegiatan',
    ];

    // Relasi ke detail (satu nomenklatur bisa punya banyak detail)
    public function details()
    {
        return $this->hasMany(KodeNomenklaturDetail::class, 'id_nomenklatur');
    }

    // Relasi ke detail berdasarkan ID (opsional, tergantung kebutuhan)
    public function bidangUrusan()
    {
        return $this->belongsTo(KodeNomenklaturDetail::class, 'id_bidang_urusan');
    }

    public function program()
    {
        return $this->belongsTo(KodeNomenklaturDetail::class, 'id_program');
    }

    public function kegiatan()
    {
        return $this->belongsTo(KodeNomenklaturDetail::class, 'id_kegiatan');
    }

    public function subkegiatan()
    {
        return $this->belongsTo(KodeNomenklaturDetail::class, 'id_subkegiatan');
    }
}
