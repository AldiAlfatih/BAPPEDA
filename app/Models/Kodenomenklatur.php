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
    ];

    public function details()
    {
        return $this->hasMany(KodeNomenklaturDetail::class, 'id_nomenklatur');
    }
    public function skpdTugas()
    {
        return $this->hasMany(SkpdTugas::class); 
    }
    

    public function bidang_urusan_by_urusan()
    {
        if ($this->jenis_nomenklatur == 0) {
            return $this->hasMany(KodeNomenklatur::class, 'id_urusan');
        } else {
            return null;
        }
    }
    public function program_by_bidang_urusan()
    {
        if ($this->jenis_nomenklatur == 1) {
            return $this->hasMany(KodeNomenklatur::class, 'id_bidang_urusan');
        } else {
            return null;
        }
    }
    public function kegiatan_by_program()
    {
        if ($this->jenis_nomenklatur == 2) {
            return $this->hasMany(KodeNomenklatur::class, 'id_program');
        } else {
            return null;
        }
    }
    public function subkegiatan_by_kegiatan()
    {
        if ($this->jenis_nomenklatur == 3) {
            return $this->hasMany(KodeNomenklatur::class, 'id_kegiatan');
        } else {
            return null;
        }
    }

}