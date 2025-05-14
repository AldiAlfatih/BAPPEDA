<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;
    
    protected $table = 'periode';
    
    protected $fillable = [
        'tahap_id',
        'tahun_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status', // Tambahkan status: 0 = Tutup, 1 = Buka, 2 = Selesai
    ];
    
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
    
    public function tahap()
    {
        return $this->belongsTo(PeriodeTahap::class, 'tahap_id');
    }
    
    public function tahun()
    {
        return $this->belongsTo(PeriodeTahun::class, 'tahun_id');
    }
}