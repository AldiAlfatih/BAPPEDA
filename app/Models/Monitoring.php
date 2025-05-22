<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'pagu_pokok',
        'pagu_parsial',
        'pagu_perubahan',
    ];

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function targets()
    {
        return $this->hasMany(MonitoringTarget::class);
    }

    public function realisasi()
    {
        return $this->hasMany(MonitoringRealisasi::class);
    }
}
