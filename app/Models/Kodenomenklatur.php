<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeNomenklatur extends Model
{
    use HasFactory;

    protected $table = 'kode_nomenklatur';
    // Menyimpan kolom-kolom yang bisa diisi mass-assignment
    protected $fillable = ['nomor_kode', 'nomenklatur', 'jenis_nomenklatur'];

    // Relasi ke Urusan
    public function urusan()
    {
        return $this->hasMany(Urusan::class, 'kode_nomenklatur_id');
    }

    // Relasi ke Bidang Urusan (melalui Urusan)
    public function bidangUrusan()
    {
        return $this->hasManyThrough(BidangUrusan::class, Urusan::class, 'kode_nomenklatur_id', 'id_urusan');
    }

     // Relasi ke Program (melalui Urusan dan Bidang Urusan)
     public function program()
     {
         return $this->hasManyThrough(Program::class, Urusan::class, 'kode_nomenklatur_id', 'id_bid_urusan');
     }

     public function kegiatan()
     {
         return $this->hasManyThrough(Kegiatan::class, Urusan::class, 'kode_nomenklatur_id', 'id_program');
     }
     public function subKegiatan()
     {
         return $this->hasManyThrough(SubKegiatan::class, Urusan::class, 'kode_nomenklatur_id', 'id_kegiatan');
     }
}

