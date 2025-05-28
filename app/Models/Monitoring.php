<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';

    protected $fillable = [
        'skpd_id',
        'sumber_dana',
        'periode_id',
        'tahun',
        'deskripsi',
        'pagu_anggaran',
        'pagu_pokok',
        'pagu_parsial',
        'pagu_perubahan',
        'is_finalized',
    ];

    public function skpdTugas()
    {
        return $this->belongsTo(SkpdTugas::class);
    }

    public function tugas()
    {
        return $this->belongsTo(SkpdTugas::class, 'tugas_id');
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
