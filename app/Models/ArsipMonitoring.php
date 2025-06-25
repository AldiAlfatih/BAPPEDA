<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArsipMonitoring extends Model
{
    use HasFactory;

    protected $table = 'arsip_monitoring';

    protected $fillable = [
        'skpd_tugas_id',
        'periode',
        'tahun',
        'nama_file',
        'path_file',
        'ukuran_file',
        'tipe_file',
        'tanggal_upload',
        'uploaded_by',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
    ];

    // Konstanta untuk periode
    const PERIODE_RENCANA_AWAL = 'rencana_awal';
    const PERIODE_TRIWULAN_1 = 'triwulan_1';
    const PERIODE_TRIWULAN_2 = 'triwulan_2';
    const PERIODE_TRIWULAN_3 = 'triwulan_3';
    const PERIODE_TRIWULAN_4 = 'triwulan_4';

    public static function getPeriodeOptions()
    {
        return [
            self::PERIODE_RENCANA_AWAL => 'Rencana Awal',
            self::PERIODE_TRIWULAN_1 => 'Triwulan 1',
            self::PERIODE_TRIWULAN_2 => 'Triwulan 2',
            self::PERIODE_TRIWULAN_3 => 'Triwulan 3',
            self::PERIODE_TRIWULAN_4 => 'Triwulan 4',
        ];
    }

    // Relasi
    public function skpdTugas(): BelongsTo
    {
        return $this->belongsTo(SkpdTugas::class, 'skpd_tugas_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessor untuk URL file
    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->path_file);
    }

    // Accessor untuk ukuran file dalam format readable
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->ukuran_file;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Method untuk cek apakah file exists
    public function fileExists()
    {
        return Storage::disk('public')->exists($this->path_file);
    }

    // Method untuk hapus file dari storage
    public function deleteFile()
    {
        if ($this->fileExists()) {
            Storage::disk('public')->delete($this->path_file);
        }
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    // Scope untuk filter berdasarkan SKPD tugas
    public function scopeBySkpdTugas($query, $skpdTugasId)
    {
        return $query->where('skpd_tugas_id', $skpdTugasId);
    }
} 