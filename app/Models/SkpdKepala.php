<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkpdKepala extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skpd_kepala';

    protected $fillable = [
        'skpd_id',
        'user_id',
        'is_aktif',
    ];
    
    protected $dates = ['deleted_at'];

    public function skpd(): BelongsTo
    {
        return $this->belongsTo(Skpd::class, 'skpd_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function userDetail()
    {
        return $this->hasOneThrough(
            \App\Models\UserDetail::class,
            \App\Models\User::class,
            'id', 
            'user_id',
            'user_id', 
            'id' 
);
}
}
