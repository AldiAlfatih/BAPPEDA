<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bantuan extends Model
{
    use HasFactory;
    
    protected $table = 'bantuan';
    protected $fillable = ['jenis_bantuan', 'penerima', 'tanggal_disalurkan', 'status_bantuan_id'];

    public function status()
    {
        return $this->belongsTo(StatusBantuan::class, 'status_bantuan_id');
    }
}
