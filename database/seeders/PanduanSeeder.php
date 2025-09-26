<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PanduanSeeder extends Seeder
{
    public function run(): void
    {
        $dataPanduan = [
            [
                'judul' => 'Panduan Penggunaan Admin',
                'deskripsi' => 'Panduan ini berisi langkah-langkah penggunaan sistem dari login hingga pelaporan Admin.',
                'file' => 'panduan_files/BUKU PANDUAN E-Monev Admin.pdf',
                'sampul' => 'panduan_sampul/BUKU PANDUAN ADMIN.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Panduan Pengisian Data SKPD',
                'deskripsi' => 'Dokumen ini menjelaskan cara pengisian data untuk masing-masing SKPD.',
                'file' => 'panduan_files/BUKU PANDUAN E-Monev Perangkat Daerah.pdf',
                'sampul' => 'panduan_sampul/BUKU PANDUAN PERANGKAT DAERAH.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Panduan Penggunaan Operator
                ',
                'deskripsi' => 'Panduan ini berisi langkah-langkah penggunaan sistem dari login hingga pelaporan Operator.',
                'file' => 'panduan_files/BUKU PANDUAN E-Monev Operator.pdf',
                'sampul' => 'panduan_sampul/BUKU PANDUAN OPERATOR.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('panduan')->insert($dataPanduan);
}
}
