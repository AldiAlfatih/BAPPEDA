<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomenklaturDetail extends Model
{
    use HasFactory;

    protected $table = 'kode_nomenklatur_detail';
    protected $fillable = [
        'id_nomenklatur',
        'id_urusan',
        'id_bidang_urusan',
        'id_program',
        'id_kegiatan',
        'id_sub_kegiatan',
    ];

    public function kodeNomenklatur()
{
    return $this->belongsTo(KodeNomenklatur::class, 'id_nomenklatur');
}


    public function urusan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_urusan');
    }
    public function bidangUrusan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_bidang_urusan');
    }
    public function program()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_program');
    }
    public function kegiatan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_kegiatan');
    }
    public function subkegiatan()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_sub_kegiatan');
    }
}
