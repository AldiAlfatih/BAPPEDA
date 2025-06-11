<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitoringTarget extends Model
{
    use HasFactory;

    protected $table = 'monitoring_target';

    protected $fillable = [
        'monitoring_anggaran_id',
        'periode_id',
        'kinerja_fisik',
        'keuangan'
    ];

    protected $casts = [
        'kinerja_fisik' => 'float',
        'keuangan' => 'integer'
    ];

    public function monitoringAnggaran()
    {
        return $this->belongsTo(MonitoringAnggaran::class, 'monitoring_anggaran_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
