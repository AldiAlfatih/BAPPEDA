<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeTahun extends Model
{
    use HasFactory;

    protected $table = 'periode_tahun';

    protected $fillable = [
        'status',
        'tahun',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function periode()
    {
        return $this->hasMany(Periode::class, 'tahun_id');
    }
}
