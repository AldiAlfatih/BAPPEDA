<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';

    protected $fillable = [
        'skpd_tugas_id',
        'periode_id',
        'tahun',
        'deskripsi',
        'nama_pptk',
    ];

    public function skpdTugas()
    {
        return $this->belongsTo(SkpdTugas::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function monitoringAnggaran()
    {
        return $this->hasMany(MonitoringAnggaran::class);
    }
}
