<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skpd extends Model
{
    use HasFactory;
    protected $table = 'skpd';
    protected $fillable = [
        'user_id',
        'nama_skpd',
        'nama_dinas',
        'no_dpa',
        'kode_organisasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }
    public function skpdKepala()
    {
        return $this->hasMany(SkpdKepala::class, 'skpd_id')
            ->where('is_aktif', 1);
    }
    public function skpdTugas()
    {
        return $this->hasMany(SkpdTugas::class, 'skpd_id');
    }
    public function timKerja()
    {
        return $this->hasMany(TimKerja::class, 'skpd_id');
    }
}
