<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $table = 'program';
    protected $fillable = [
        'id_bid_urusan', 'nama'
    ];

    public function bidangUrusan()
    {
        return $this->belongsTo(BidangUrusan::class);
    }

    // Relasi ke Kegiatan
    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'id_program');
    }
}

