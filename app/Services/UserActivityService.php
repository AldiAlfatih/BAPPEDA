<?php

namespace App\Services;

use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserActivityService
{
    /**
     * Log aktivitas user
     */
    public static function log(
        string $activityType,
        string $description,
        string $module,
        array $additionalData = [],
        ?int $userId = null
    ): ?UserActivityLog {
        $user = $userId ? $userId : Auth::id();
        $request = request();

        // Return null if no user is authenticated (untuk prevent error saat testing)
        if (!$user) {
            return null;
        }

        return UserActivityLog::create([
            'user_id' => $user,
            'activity_type' => $activityType,
            'activity_description' => $description,
            'module' => $module,
            'activity_data' => $additionalData,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
    }

    /**
     * Log aktivitas periode
     */
    public static function logPeriode(string $action, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_PERIODE,
            "User {$action} periode",
            UserActivityLog::MODULE_PERIODE,
            $data
        );
    }

    /**
     * Log aktivitas manajemen anggaran
     */
    public static function logManajemenAnggaran(string $action, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_ANGGARAN,
            "User {$action} data anggaran",
            UserActivityLog::MODULE_MANAJEMEN_ANGGARAN,
            $data
        );
    }

    /**
     * Log aktivitas rencana awal
     */
    public static function logRencanaAwal(string $action, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_RENCANA_AWAL,
            "User {$action} rencana awal",
            UserActivityLog::MODULE_RENCANA_AWAL,
            $data
        );
    }

    /**
     * Log aktivitas triwulan
     */
    public static function logTriwulan(string $action, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_TRIWULAN,
            "User {$action} data triwulan",
            UserActivityLog::MODULE_TRIWULAN,
            $data
        );
    }

    /**
     * Log download PDF
     */
    public static function logPdfDownload(string $pdfType, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_PDF_DOWNLOAD,
            "User mengunduh PDF {$pdfType}",
            UserActivityLog::MODULE_MONITORING,
            $data
        );
    }

    /**
     * Log upload file
     */
    public static function logFileUpload(string $fileName, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_FILE_UPLOAD,
            "User mengunggah file {$fileName}",
            UserActivityLog::MODULE_ARSIP,
            $data
        );
    }

    /**
     * Log export data
     */
    public static function logExportData(string $exportType, array $data = []): ?UserActivityLog
    {
        return self::log(
            UserActivityLog::TYPE_EXPORT,
            "User mengekspor data {$exportType}",
            UserActivityLog::MODULE_MONITORING,
            $data
        );
    }

    /**
     * Get activity logs untuk tanggal tertentu
     */
    public static function getActivitiesByDate(string $date, ?int $userId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = UserActivityLog::with('user:id,name')
            ->byDate($date)
            ->orderBy('created_at', 'desc');

        if ($userId) {
            $query->byUser($userId);
        }

        return $query->get();
    }

    /**
     * Get activity logs untuk user tertentu
     */
    public static function getUserActivities(int $userId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return UserActivityLog::byUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs untuk dashboard (semua user)
     */
    public static function getDashboardActivities(string $date, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return UserActivityLog::with('user:id,name')
            ->byDate($date)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Format description untuk user yang login
     */
    private static function formatDescription(string $baseDescription): string
    {
        $user = Auth::user();
        $userName = $user ? $user->name : 'Unknown User';
        
        return str_replace('User', $userName, $baseDescription);
    }
} 