<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SumberAnggaranSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $sumberAnggaran = [
            ['nama' => 'DAK'],
            ['nama' => 'DAK Peruntukan'],
            ['nama' => 'DAK Fisik'],
            ['nama' => 'DAK Non Fisik'],
            ['nama' => 'BLUD'],
        ];

        foreach ($sumberAnggaran as $sumber) {
            DB::table('sumber_anggaran')->insertOrIgnore($sumber);
        }
    }
}
