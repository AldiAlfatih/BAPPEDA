<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_description',
        'module',
        'activity_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'activity_data' => 'array',
        'created_at' => 'datetime'
    ];

    // Konstanta untuk jenis aktivitas
    const TYPE_PERIODE = 'periode';
    const TYPE_ANGGARAN = 'anggaran';
    const TYPE_RENCANA_AWAL = 'rencana_awal';
    const TYPE_TRIWULAN = 'triwulan';
    const TYPE_PDF_DOWNLOAD = 'pdf_download';
    const TYPE_FILE_UPLOAD = 'file_upload';
    const TYPE_EXPORT = 'export';

    // Konstanta untuk module
    const MODULE_MONITORING = 'monitoring';
    const MODULE_MANAJEMEN_ANGGARAN = 'manajemen_anggaran';
    const MODULE_PERIODE = 'periode';
    const MODULE_RENCANA_AWAL = 'rencana_awal';
    const MODULE_TRIWULAN = 'triwulan';
    const MODULE_ARSIP = 'arsip_monitoring';

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan activity type
     */
    public function scopeByActivityType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope untuk filter berdasarkan module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Get activity types dengan label
     */
    public static function getActivityTypes()
    {
        return [
            self::TYPE_PERIODE => 'Membuka Periode',
            self::TYPE_ANGGARAN => 'Mengisi Manajemen Anggaran',
            self::TYPE_RENCANA_AWAL => 'Mengisi Rencana Awal',
            self::TYPE_TRIWULAN => 'Mengisi Data Triwulan',
            self::TYPE_PDF_DOWNLOAD => 'Mengunduh File PDF',
            self::TYPE_FILE_UPLOAD => 'Mengunggah File ke Arsip',
            self::TYPE_EXPORT => 'Mengekspor Data'
        ];
    }
}
