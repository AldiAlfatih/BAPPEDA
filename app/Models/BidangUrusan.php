<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangUrusan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_urusan', 'nama'
    ];

    public function urusan()
    {
        return $this->belongsTo(Urusan::class);
    }
}
