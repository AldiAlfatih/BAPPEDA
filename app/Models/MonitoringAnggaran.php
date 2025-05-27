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

    public function pagu()
    {
        return $this->hasMany(MonitoringPagu::class);
    }

    public function realisasi()
    {
        return $this->hasMany(MonitoringRealisasi::class);
    }

    public function target()
    {
        return $this->hasMany(MonitoringTarget::class);
    }
}
