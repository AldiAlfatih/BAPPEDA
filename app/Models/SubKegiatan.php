<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubKegiatan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sub_kegiatan';
    protected $fillable = [
        'id_kegiatan', 'nama'
    ];

    protected $dates = ['deleted_at'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}

