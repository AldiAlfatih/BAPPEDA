<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SumberAnggaran extends Model
{
    use HasFactory;

    protected $table = 'sumber_anggaran';
    
    // Disable timestamps karena tabel tidak punya created_at dan updated_at
    public $timestamps = false;

    protected $fillable = [
        'nama'
    ];

    public function monitoringAnggaran()
    {
        return $this->hasMany(MonitoringAnggaran::class, 'sumber_anggaran_id');
    }
}
