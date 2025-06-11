<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonitoringAnggaran extends Model
{
    use HasFactory;

    protected $table = 'monitoring_anggaran';

    protected $fillable = [
        'sumber_anggaran_id',
        'monitoring_id',
    ];

    public function sumberAnggaran()
    {
        return $this->belongsTo(SumberAnggaran::class);
    }

    public function monitoring()
    {
        return $this->belongsTo(Monitoring::class);
    }

    public function monitoringPagu()
    {
        return $this->hasMany(MonitoringPagu::class);
    }

    // Add pagu relationship as an alias for monitoringPagu
    public function pagu()
    {
        return $this->hasMany(MonitoringPagu::class);
    }

    public function monitoringRealisasi()
    {
        return $this->hasMany(MonitoringRealisasi::class);
    }
    // Alias untuk konsistensi
    public function monitoringTarget()
    {
        return $this->hasMany(MonitoringTarget::class);
    }

    public function targets()
    {
        return $this->hasMany(MonitoringTarget::class, 'monitoring_anggaran_id');
    }
}
