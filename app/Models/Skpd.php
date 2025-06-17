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

    public function user()
    {
        // The relationship is through skpd_kepala, not direct
        return $this->belongsToMany(User::class, 'skpd_kepala', 'skpd_id', 'user_id');
    }
    
    public function userDetail()
    {
        // UserDetail is related through the User model via skpd_kepala
        return $this->hasManyThrough(
            UserDetail::class,
            SkpdKepala::class,
            'skpd_id', // Foreign key on skpd_kepala table
            'user_id', // Foreign key on user_detail table
            'id', // Local key on skpd table
            'user_id' // Local key on skpd_kepala table
        );
    }
    
    public function skpdKepala()
    {
        return $this->hasMany(SkpdKepala::class, 'skpd_id');
    }
    
    public function skpdTugas()
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
