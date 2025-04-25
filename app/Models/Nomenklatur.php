<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomenklatur extends Model
{
    use HasFactory;

    protected $table = 'kode_nomenklatur';
    protected $fillable = ['nama_kode','nomenklatur','urusan','bidang_urusan','program','kegiatan','subkegiatan'];

}
