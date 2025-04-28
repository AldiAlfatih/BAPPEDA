<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'alamat', 'nip', 'no_hp', 'jenis_kelamin', 'tanggal_lahir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
