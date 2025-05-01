<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomenklatur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kode', 'nomenklatur', 'jenis_nomenklatur', 'parent_id'
    ];

    // Relasi dengan urusan
    public function urusan()
    {
        return $this->hasMany(Urusan::class);
    }

    // Relasi dengan bidang urusan
    public function bidangUrusan()
    {
        return $this->hasMany(BidangUrusan::class);
    }

    // Relasi dengan program
    public function program()
    {
        return $this->hasMany(Program::class);
    }

    // Relasi dengan kegiatan
    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class);
    }

    // Relasi dengan sub kegiatan
    public function subKegiatan()
    {
        return $this->hasMany(SubKegiatan::class);
    }
}
