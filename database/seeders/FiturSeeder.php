<?php

namespace Database\Seeders;

use App\Models\PeriodeTahap;
use App\Models\Periode;
use App\Models\PeriodeTahun;
use Illuminate\Database\Seeder;

class FiturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default stages
        $stages = [
            ['tahap' => 'Rencana'],
            ['tahap' => 'Triwulan 1'],
            ['tahap' => 'Triwulan 2'],
            ['tahap' => 'Triwulan 3'],
            ['tahap' => 'Triwulan 4'],
            ['tahap' => 'Evaluasi Akhir'],
        ];
        
        foreach ($stages as $stage) {
            PeriodeTahap::firstOrCreate($stage);
        }
        
    }
}