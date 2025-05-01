<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kegiatan', 'nama'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
