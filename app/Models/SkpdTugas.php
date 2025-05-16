<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkpdTugas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skpd_tugas';

    protected $fillable = [
        'skpd_id',
        'user_id',
        'kode_nomenklatur_id',
        'is_aktif',
    ];

    protected $dates = ['deleted_at'];

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kodeNomenklatur()
    {
        return $this->belongsTo(KodeNomenklatur::class);
    }
}