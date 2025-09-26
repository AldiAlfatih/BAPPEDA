<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringAnggaran extends Model
{
    use HasFactory;

    protected $table = 'monitoring_anggaran';

    protected $fillable = [
        'sumber_anggaran_id',
        'monitoring_id',
    ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(Monitoring::class, 'monitoring_id');
    }

    public function sumberAnggaran(): BelongsTo
    {
        return $this->belongsTo(SumberAnggaran::class, 'sumber_anggaran_id');
    }

    public function pagu(): HasMany
    {
        return $this->hasMany(MonitoringPagu::class, 'monitoring_anggaran_id');
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(MonitoringRealisasi::class, 'monitoring_anggaran_id');
    }

    public function target(): HasMany
    {
        return $this->hasMany(MonitoringTarget::class, 'monitoring_anggaran_id');
    }
}
