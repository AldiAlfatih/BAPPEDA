<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'DAU'],
            ['nama' => 'DAK Fisik'],
            ['nama' => 'DAK Non Fisik'],
            ['nama' => 'BLUD'],
            ['nama' => 'DAU Peruntukan'],
        ];

        DB::table('sumber_anggaran')->insert($data);
    }
}
