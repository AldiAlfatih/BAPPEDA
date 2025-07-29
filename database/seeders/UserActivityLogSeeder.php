<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserActivityLog;
use App\Models\User;
use Carbon\Carbon;

class UserActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user for testing (assuming users exist)
        $user = User::first();
        
        if (!$user) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $activitiesData = [
            [
                'activity_type' => UserActivityLog::TYPE_PERIODE,
                'activity_description' => 'User membuat periode baru',
                'module' => UserActivityLog::MODULE_PERIODE,
                'activity_data' => [
                    'periode_id' => 1,
                    'tahap' => 'Rencana Awal',
                    'tahun_id' => 1,
                    'tanggal_mulai' => '2025-01-01',
                    'tanggal_selesai' => '2025-01-31'
                ]
            ],
            [
                'activity_type' => UserActivityLog::TYPE_ANGGARAN,
                'activity_description' => 'User menyimpan data monitoring anggaran',
                'module' => UserActivityLog::MODULE_MANAJEMEN_ANGGARAN,
                'activity_data' => [
                    'monitoring_id' => 1,
                    'skpd_id' => 1,
                    'tahun' => 2025,
                    'pagu_pokok' => 1000000000,
                    'pagu_parsial' => 0,
                    'pagu_perubahan' => 0,
                    'jumlah_targets' => 4
                ]
            ],
            [
                'activity_type' => UserActivityLog::TYPE_RENCANA_AWAL,
                'activity_description' => 'User menyimpan data rencana awal',
                'module' => UserActivityLog::MODULE_RENCANA_AWAL,
                'activity_data' => [
                    'monitoring_id' => 1,
                    'tugas_id' => 1,
                    'tahun' => 2025,
                    'pagu_pokok' => 500000000,
                    'pagu_parsial' => 0,
                    'pagu_perubahan' => 0,
                    'jumlah_targets' => 4,
                    'jenis_nomenklatur' => 4
                ]
            ],
            [
                'activity_type' => UserActivityLog::TYPE_TRIWULAN,
                'activity_description' => 'User menyimpan data Triwulan 1',
                'module' => UserActivityLog::MODULE_TRIWULAN,
                'activity_data' => [
                    'tid' => 1,
                    'triwulan' => 'Triwulan 1',
                    'tugas_id' => 1,
                    'tahun' => 2025,
                    'monitoring_id' => 1,
                    'sumber_anggaran_id' => 1,
                    'realisasi_fisik' => 25.5,
                    'realisasi_keuangan' => 125000000,
                    'periode_id' => 2
                ]
            ],
            [
                'activity_type' => UserActivityLog::TYPE_PDF_DOWNLOAD,
                'activity_description' => 'User mengunduh PDF Rencana Awal',
                'module' => UserActivityLog::MODULE_MONITORING,
                'activity_data' => [
                    'tugas_id' => 1,
                    'skpd_id' => 1,
                    'skpd_nama' => 'Dinas Kesehatan',
                    'filename' => 'Rencana_Awal_Dinas_Kesehatan_2025-01-20.pdf',
                    'tahun' => 2025,
                    'paper_size' => 'A4',
                    'orientation' => 'portrait'
                ]
            ],
            [
                'activity_type' => UserActivityLog::TYPE_FILE_UPLOAD,
                'activity_description' => 'User mengunggah file arsip_monitoring_rencana_awal.pdf',
                'module' => UserActivityLog::MODULE_ARSIP,
                'activity_data' => [
                    'arsip_id' => 1,
                    'skpd_tugas_id' => 1,
                    'periode' => 'rencana_awal',
                    'tahun' => 2025,
                    'nama_file' => 'arsip_monitoring_rencana_awal.pdf',
                    'ukuran_file' => 2048576,
                    'tipe_file' => 'pdf',
                    'path_file' => 'arsip_monitoring/2025/1/arsip_monitoring_rencana_awal.pdf',
                    'keterangan' => 'Upload arsip monitoring untuk rencana awal'
                ]
            ]
        ];

        // Create activities for today and some past dates
        $dates = [
            Carbon::today(),
            Carbon::yesterday(),
            Carbon::today()->subDays(2),
            Carbon::today()->subDays(7),
        ];

        foreach ($dates as $date) {
            foreach ($activitiesData as $activityData) {
                UserActivityLog::create([
                    'user_id' => $user->id,
                    'activity_type' => $activityData['activity_type'],
                    'activity_description' => $activityData['activity_description'],
                    'module' => $activityData['module'],
                    'activity_data' => $activityData['activity_data'],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'created_at' => $date->addMinutes(rand(0, 1440)), // Random time in the day
                    'updated_at' => $date
                ]);
            }
        }

        $this->command->info('UserActivityLog seeder completed successfully!');
    }
}
