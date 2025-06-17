<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SumberAnggaran extends Model
{
    use HasFactory;

    protected $table = 'sumber_anggaran';

    protected $fillable = [
        'skpd_tugas_id',
        'periode_id',
        'dak',
        'dak_peruntukan',
        'dak_fisik',
        'dak_non_fisik',
        'blud',
        'nilai_dak',
        'nilai_dak_peruntukan',
        'nilai_dak_fisik',
        'nilai_dak_non_fisik',
        'nilai_blud'
    ];

    protected $casts = [
        'dak' => 'boolean',
        'dak_peruntukan' => 'boolean',
        'dak_fisik' => 'boolean',
        'dak_non_fisik' => 'boolean',
        'blud' => 'boolean',
        'nilai_dak' => 'double',
        'nilai_dak_peruntukan' => 'double',
        'nilai_dak_fisik' => 'double',
        'nilai_dak_non_fisik' => 'double',
        'nilai_blud' => 'double'
    ];

    public function skpdTugas()
    {
        return $this->belongsTo(SkpdTugas::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }
}
