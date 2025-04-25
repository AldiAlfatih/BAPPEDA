<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    //
    protected $table = 'subkegiatan';
    protected $fillable = ['nama', 'kode', 'kegiatan_id'];

    public function kegiatan():BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}

