<?php

namespace App\Models;

use Illuminate\Databse\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan2 extends Model
{
    use HasFactory;
    protected $table = 'kegiatan2';
    protected $fillable = ['nama', 'kode','kegiatan_id'];

    public function kegiatan2():HasMany
    {
        return $this->hasMany(Kegiatan2::class, 'kegiatan_id');
    }
}
