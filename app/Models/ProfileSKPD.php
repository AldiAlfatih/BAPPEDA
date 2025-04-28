<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSKPD extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_kepala_skpd',
        'kode_urusan',
        'nama_skpd',
        'kode_organisasi',
        'nip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
