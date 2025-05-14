<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;
    protected $table = 'kegiatan';
    protected $fillable = [
        'id_program', 'nama'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subKegiatan()
    {
        return $this->hasMany(SubKegiatan::class, 'id_kegiatan');
    }
}
