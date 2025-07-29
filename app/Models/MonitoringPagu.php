<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringPagu extends Model
{
    use HasFactory;

    protected $table = 'monitoring_pagu';

    protected $fillable = [
        'monitoring_anggaran_id',
        'periode_id',
        'kategori',
        'dana',
    ];

    public function anggaran(): BelongsTo
    {
        return $this->belongsTo(MonitoringAnggaran::class, 'monitoring_anggaran_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
}
}
