<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bantuan extends Model
{
    use HasFactory;

    protected $table = 'bantuan';

    protected $fillable = [
        'user_id',
        'judul',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function faqs()
    // {
    //     return $this->hasMany(BantuanFaq::class);
    // }

    public function faqs()
    {
        return $this->hasMany(

            BantuanFaq::class,
            'bantuan_id' 
        );
    }
}
