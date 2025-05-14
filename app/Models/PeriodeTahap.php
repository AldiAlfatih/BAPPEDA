<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PeriodeTahap extends Model
{
    use HasFactory;
    
    protected $table = 'periode_tahap';
    
    public $timestamps = false;
    
    protected $fillable = [
        'tahap',
    ];
    
    public function periode()
    {
        return $this->hasMany(Periode::class, 'tahap_id');
    }
}