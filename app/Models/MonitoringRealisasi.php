<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function anggaran(): BelongsTo
    {
        return $this->belongsTo(MonitoringAnggaran::class, 'monitoring_anggaran_id');
    }

    public function periode(): BelongsTo
    {
        // Assuming you have a Periode model
        return $this->belongsTo(Periode::class, 'periode_id');
}
}
