<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SkpdKepala extends Model
{
    use HasFactory;

    protected $table = 'skpd_kepala';

    protected $fillable = [
        'skpd_id',
        'user_id',
        'is_aktif',
    ];

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userDetail()
    {
        return $this->hasOneThrough(
            \App\Models\UserDetail::class,
            \App\Models\User::class,
            'id', // User primary key
            'user_id', // UserDetail foreign key
            'user_id', // SkpdKepala foreign key
            'id' // User primary key
        );
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 012c04395253e81a93d673750c56d366e7cb168f
