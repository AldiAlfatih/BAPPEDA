<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemberitahuan extends Model
{
    use HasFactory;
    protected $table = 'pemberitahuan'; // Nama tabel yang sesuai dengan konvensi Laravel
    protected $fillable = ['judul', 'isi', 'tanggal_kirim'];

}
