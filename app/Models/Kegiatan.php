<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kegiatan';
    protected $fillable = [
        'id_program', 'nama'
    ];

    protected $dates = ['deleted_at'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subKegiatan()
    {
        return $this->hasMany(SubKegiatan::class, 'id_kegiatan');
    }
}
