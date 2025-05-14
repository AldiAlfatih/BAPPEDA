<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangUrusan extends Model
{
    use HasFactory;

    protected $table = 'bid_urusan';
    protected $fillable = [
        'id_urusan',
        'kode_bidang_urusan',
        'nama',
     ];
    // Relasi ke Urusan
    public function urusan()
    {
        return $this->belongsTo(Urusan::class, 'id_urusan');
    }

    // Relasi ke Program
    public function program()
    {
        return $this->hasMany(Program::class, 'id_bid_urusan');
    }
}
