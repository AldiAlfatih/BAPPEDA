<?php
use Illuminate\Database\Seeder;
use App\Models\PeriodeTahap;
use App\Models\Periode;
use App\Models\PeriodeTahun;
use Database\Seeders\FiturSeeder;
use Database\Seeders\RuleSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Memanggil FiturSeeder untuk menjalankan seeding
        $this->call(FiturSeeder::class);
        $this->call(RuleSeeder::class);
    }
}
