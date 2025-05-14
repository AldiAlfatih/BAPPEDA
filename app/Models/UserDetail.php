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
        return $this->hasOne(Skpd::class, 'user_id'); 
    }
    

}