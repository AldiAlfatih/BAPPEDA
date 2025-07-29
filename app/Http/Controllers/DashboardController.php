<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(Request $request)
    {
        // Get today's date for initial activity logs
        $today = Carbon::today()->format('Y-m-d');
        
        // Get activity logs for today
        $todayActivities = UserActivityService::getDashboardActivities($today);
        
        // Format activities for frontend
        $formattedActivities = $todayActivities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'user_name' => $activity->user->name ?? 'Unknown User',
                'description' => $activity->activity_description,
                'activity_type' => $activity->activity_type,
                'module' => $activity->module,
                'created_at' => $activity->created_at->format('H:i:s'),
                'created_at_formatted' => $activity->created_at->format('d M Y H:i:s'),
                'activity_data' => $activity->activity_data
            ];
        });

        return Inertia::render('Dashboard', [
            'initialActivities' => $formattedActivities,
            'initialDate' => $today
        ]);
    }

    /**
     * Get activity logs by date (for AJAX requests without API)
     */
    public function getActivitiesByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->date;
        $activities = UserActivityService::getDashboardActivities($date);

        // Format activities untuk frontend
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'user_name' => $activity->user->name ?? 'Unknown User',
                'description' => $activity->activity_description,
                'activity_type' => $activity->activity_type,
                'module' => $activity->module,
                'created_at' => $activity->created_at->format('H:i:s'),
                'created_at_formatted' => $activity->created_at->format('d M Y H:i:s'),
                'activity_data' => $activity->activity_data
            ];
        });

        return response()->json([
            'activities' => $formattedActivities,
            'date' => $date,
            'total' => $formattedActivities->count()
        ]);
    }

    /**
     * Log user activity via API (for AJAX requests)
     */
    public function logActivity(Request $request)
    {
        $request->validate([
            'activity_type' => 'required|string',
            'module' => 'required|string', 
            'description' => 'required|string',
            'activity_data' => 'nullable|array'
        ]);

        // Route the logging to appropriate UserActivityService method based on activity_type
        $activityType = $request->activity_type;
        $description = $request->description;
        $activityData = $request->activity_data ?? [];

        $logEntry = null;

        switch ($activityType) {
            case 'anggaran':
                $logEntry = UserActivityService::logManajemenAnggaran($description, $activityData);
                break;
            case 'rencana_awal':
                $logEntry = UserActivityService::logRencanaAwal($description, $activityData);
                break;
            case 'triwulan':
                $logEntry = UserActivityService::logTriwulan($description, $activityData);
                break;
            case 'pdf_download':
                $pdfType = $activityData['pdf_type'] ?? 'Document';
                $logEntry = UserActivityService::logPdfDownload($pdfType, $activityData);
                break;
            default:
                // Generic logging for other types
                $logEntry = UserActivityService::log(
                    $activityType,
                    $description,
                    $request->module,
                    $activityData
                );
                break;
        }

        if ($logEntry) {
            return response()->json([
                'success' => true,
                'message' => 'Activity logged successfully',
                'log_id' => $logEntry->id
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log activity'
            ], 422);
        }
    }
}
