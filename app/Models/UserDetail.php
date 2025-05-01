<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model
     */
    protected $table = 'user_detail';

    /**
     * Atribut yang bisa diisi secara massal
     */
    protected $fillable = [
        'user_id',
        'alamat',
        'nip',
        'no_hp',
        'jenis_kelamin',
        'tgl_lahir',
    ];

    /**
     * Mengubah otomatis tipe data untuk atribut tertentu
     */
    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    /**
     * Relasi balik ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}