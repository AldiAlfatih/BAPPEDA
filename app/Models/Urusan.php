<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urusan extends Model
{
    use HasFactory;
    protected $table = 'urusan';
    protected $fillable = [
        'kode_nomenklatur_id',
        'nama',
        'created_at',
        'updated_at'
    ];
    // Relasi ke KodeNomenklatur
    public function kodeNomenklatur()
    {
        return $this->belongsTo(KodeNomenklatur::class, 'kode_nomenklatur_id');
    }

    // Relasi ke BidangUrusan
    public function bidangUrusan()
    {
        return $this->hasMany(BidangUrusan::class, 'id_urusan');
    }
}

