<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomenklaturDetail extends Model
{
    use HasFactory;

    protected $table = 'kode_nomenklatur_detail';
    protected $fillable = [
        'id_kode_nomenklatur',
        'urusan',
        'bidang_urusan',
        'program',
        'kegiatan',
        'subkegiatan'
    ];

    public function kode()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_kode_nomenklatur');
    }

    public function urusan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'urusan');
    }

    public function bidangUrusan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'bidang_urusan');
    }

    public function program()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'program');
    }

    public function kegiatan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'kegiatan');
    }

    public function subkegiatan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'subkegiatan');
    }
}
