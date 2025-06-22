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
    public function tugas()
    {
        return $this->belongsTo(SkpdTugas::class, 'skpd_tugas_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function anggaran()
    {
        return $this->hasMany(MonitoringAnggaran::class);
    }

    public function target()
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
