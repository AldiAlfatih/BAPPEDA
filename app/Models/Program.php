<?php

<<<<<<< HEAD
namespace App\Models;

use Illuminate\Databse\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    //
    use HasFactory;
    protected $table = 'program';
    protected $fillable = ['nama', 'kode'];

    public function kegiatan():HasMany
    {
        return $this->hasMany(Kegiatan::class, 'kegiatan_id');
    }
}
=======
// namespace App\Models;

// use Illuminate\Databse\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Program extends Model
// {
//     //
//     use HasFactory;
//     protected $table = 'program';
//     protected $fillable = ['nama', 'kode'];

//     public function kegiatan():HasMany
//     {
//         return $this->hasMany(Kegiatan::class, 'kegiatan_id');
//     }
// }
>>>>>>> bd447f1 (database pertama)

