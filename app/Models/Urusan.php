<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urusan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_nomenklatur_id', 'nama'
    ];

    public function kodeNomenklatur()
    {
        return $this->belongsTo(KodeNomenklatur::class);
    }
}

