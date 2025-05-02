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
        'urusan',
        'bidang_urusan',
        'program',
        'kegiatan',
        'subkegiatan',
    ];

    public function nomenklatur()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'id_nomenklatur');
    }
}
