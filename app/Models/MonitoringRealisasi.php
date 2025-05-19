<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringRealisasi extends Model
{
    use HasFactory;

    protected $table = 'monitoring_realisasi';

    protected $fillable = [
        'monitoring_id',
        'periode_id',
        'kinerja_fisik',
        'keuangan',
    ];

    public function monitoring()
    {
        return $this->belongsTo(Monitoring::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
