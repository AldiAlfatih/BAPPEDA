<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BantuanFaq extends Model
{
    use HasFactory;

    protected $table = 'bantuan_faqs';

    protected $fillable = [
        'bantuan_id',
        'user_id',
        'deskripsi',
        'balasan',
        'file',
    ];

    public function bantuan()
    {
        return $this->belongsTo(Bantuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}