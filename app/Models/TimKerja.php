<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimKerja extends Model
{
    use HasFactory;

    protected $table = 'tim_kerja';

    protected $fillable = [
        'skpd_id',
        'user_id',
    ];

    public function skpd()
    {
        return $this->belongsTo(Skpd::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
