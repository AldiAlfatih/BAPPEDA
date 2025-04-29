<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSkpd extends Model
{
    use HasFactory;
    
    protected $table = 'profile_skpd';

    protected $fillable = [
        'user_id', 'nama_kepala_skpd', 'kode_urusan', 'nama_skpd', 'kode_organisasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}