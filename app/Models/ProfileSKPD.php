<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSkpd extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model
     */
    protected $table = 'profile_skpd';

    /**
     * Atribut yang bisa diisi secara massal
     */
    protected $fillable = [
        'user_id',
        'nama_kepala_skpd',
        'kode_urusan',
        'nama_skpd',
        'kode_organisasi',
    ];

    /**
     * Relasi balik ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}