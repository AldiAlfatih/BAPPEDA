<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\UserDetail;
use App\Models\Skpd;
use App\Models\SkpdKepala;
use App\Models\SkpdTugas;
use App\Models\TimKerja;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Memastikan relasi ke userDetail bekerja dengan benar
     */
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * Memastikan relasi ke profileSkpd bekerja dengan benar
     */
    public function Skpd()
    {
        return $this->hasOne(Skpd::class);
    }
    public function skpdKepala()
    {
        return $this->hasMany(SkpdKepala::class);
    }
    public function skpdTugas()
    {
        return $this->hasMany(SkpdTugas::class);
    }
    public function timKerja()
    {
        return $this->hasMany(TimKerja::class);
    }
    public function skpds()
    {
        return $this->belongsTo(Skpd::class);
    }


}