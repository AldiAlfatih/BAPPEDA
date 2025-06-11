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

    // Relasi ke targets melalui monitoring_anggaran
    public function targets()
    {
        return $this->hasManyThrough(
            MonitoringTarget::class,
            MonitoringAnggaran::class,
            'monitoring_id', // Foreign key di monitoring_anggaran
            'monitoring_anggaran_id', // Foreign key di monitoring_target
            'id', // Local key di monitoring
            'id' // Local key di monitoring_anggaran
        );
    }
}
