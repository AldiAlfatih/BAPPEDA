<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SumberAnggaran extends Model
{
    use HasFactory;

    protected $table = 'sumber_anggaran';

    protected $fillable = ['nama'];

    public function monitoringAnggaran()
    {
        return $this->hasMany(MonitoringAnggaran::class);
    }
}
