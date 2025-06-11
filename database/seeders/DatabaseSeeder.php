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
        $this->call(KodeNomenklaturSeeder::class);
        $this->call(AnggaranSeeder::class);
        $this->call(UserSeeder::class);
        // $this->call(MonitoringTargetSeeder::class);
        $this->call(PanduanSeeder::class);
}
}
