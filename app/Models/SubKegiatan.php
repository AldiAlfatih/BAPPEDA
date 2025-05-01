<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    use HasFactory;
    protected $table = 'sub_kegiatan';
    protected $fillable = [
        'id_kegiatan', 'nama'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}

