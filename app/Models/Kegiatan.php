<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';
    protected $fillable = ['nama', 'kode', 'program_id'];

    public function subkegiatan(): HasMany
    {
        return $this->hasMany(SubKegiatan::class, 'kegiatan_id');
    }

    public function program():BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
