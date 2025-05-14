<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusBantuan extends Model
{
    protected $table = 'status_bantuan';
    protected $fillable = ['nama_status'];

    public function bantuan()
    {
        return $this->hasMany(Bantuan::class, 'status_bantuan_id');
    }
}
