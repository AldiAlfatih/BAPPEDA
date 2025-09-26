<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(FiturSeeder::class);
        $this->call(RuleSeeder::class);
        
        // Seeder hierarki kode nomenklatur (urutan penting!)
        $this->call(UrusanSeeder::class);           // Level 0
        $this->call(BidangUrusanSeeder::class);     // Level 1 
        $this->call(ProgramSeeder::class);          // Level 2
        $this->call(KegiatanSeeder::class);         // Level 3
        $this->call(SubKegiatanSeeder::class);      // Level 4
        
        $this->call(AnggaranSeeder::class);
        $this->call(UserSeeder::class);
        // $this->call(MonitoringTargetSeeder::class);
        $this->call(PanduanSeeder::class);
    }
}
