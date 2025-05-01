<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kodenomenklatur extends Model
{
    use HasFactory;

    protected $table = 'kode_nomenklatur';
    protected $fillable = ['nomor_kode', 'nomenklatur', 'jenis_kode'];

    public function detail()
{
    return $this->hasOne(KodeNomenklaturDetail::class, 'id_nomenklatur');
}

}