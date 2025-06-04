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
        'skpd_id',
        'sumber_dana',
        'periode_id',
        'tahun',
        'deskripsi',
        'nama_pptk',
        'pagu_anggaran',
        'pagu_pokok',
        'pagu_parsial',
        'pagu_perubahan',
        'is_finalized',
        'monitoring_anggaran_id',
    ];

    public function skpdTugas()
    {
        return $this->belongsTo(SkpdTugas::class, 'skpd_tugas_id');
    }

    public function tugas()
    {
        return $this->belongsTo(SkpdTugas::class, 'skpd_tugas_id');
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
