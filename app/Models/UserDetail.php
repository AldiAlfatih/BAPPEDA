<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    
    protected $table = 'user_detail';
    protected $fillable = [
        'user_id',
        'alamat',
        'nip',
        'no_hp',
        'jenis_kelamin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skpd()
    {
        return $this->hasManyThrough(
            Skpd::class, 
            SkpdKepala::class,
            'user_id',  // Foreign key on skpd_kepala table
            'id',       // Foreign key on skpd table
            'user_id',  // Local key on user_detail table
            'skpd_id'   // Local key on skpd_kepala table
        );
    }
    

}