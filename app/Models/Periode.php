<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_admin',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}
