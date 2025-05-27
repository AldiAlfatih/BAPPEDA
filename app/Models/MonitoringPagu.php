<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function monitoringAnggaran()
    {
        return $this->belongsTo(MonitoringAnggaran::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
