<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skpd extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skpd';

    protected $fillable = [
        'nama_skpd',
        'kode_organisasi',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsToMany(User::class, 'skpd_kepala', 'skpd_id', 'user_id');
    }

    public function userPenanggungJawab()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userDetail()
    {

        return $this->hasManyThrough(
            UserDetail::class,
            SkpdKepala::class,
            'skpd_id',
            'user_id',
            'id',
            'user_id'
        );
    }

    public function kepala()
    {
        return $this->hasMany(SkpdKepala::class, 'skpd_id');
    }

    public function tugas()
    {
        return $this->hasMany(SkpdTugas::class, 'skpd_id');
    }

    public function timKerja()
    {
        return $this->hasMany(TimKerja::class, 'skpd_id');
    }

    public function kepalaAktif()
    {
        return $this->hasOne(SkpdKepala::class, 'skpd_id')->where('is_aktif', 1);
    }

    public function operatorAktif()
    {
        return $this->hasOne(TimKerja::class, 'skpd_id')->where('is_aktif', 1);
}
}
