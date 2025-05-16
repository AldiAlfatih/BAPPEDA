<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $table = 'program';
    protected $fillable = ['nama', 'kode'];

    protected $dates = ['deleted_at'];

    public function kegiatan():HasMany
    {
        return $this->hasMany(Kegiatan::class, 'kegiatan_id');
    }
}

