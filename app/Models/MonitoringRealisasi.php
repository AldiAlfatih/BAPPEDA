<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitoringRealisasi extends Model
{
    use HasFactory;

    protected $table = 'monitoring_realisasi';

    protected $fillable = [
        'monitoring_anggaran_id',
        'periode_id',
        'kinerja_fisik',
        'keuangan',
    ];

    public function monitoringAnggaran()
    {
        return $this->belongsTo(MonitoringAnggaran::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
