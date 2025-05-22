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
                'judul' => 'Panduan Penggunaan Sistem',
                'deskripsi' => 'Panduan ini berisi langkah-langkah penggunaan sistem dari login hingga pelaporan.',
                'file' => 'panduan_files/pp.pdf',
                'sampul' => 'panduan_sampul/bpd.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Panduan Pengisian Data SKPD',
                'deskripsi' => 'Dokumen ini menjelaskan cara pengisian data untuk masing-masing SKPD.',
                'file' => 'panduan_files/pp.pdf',
                'sampul' => 'panduan_sampul/bpd.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Panduan Upload Dokumen',
                'deskripsi' => 'Berisi petunjuk tentang bagaimana cara upload file dan dokumen penting ke sistem.',
                'file' => 'panduan_files/pp.pdf',
                'sampul' => 'panduan_sampul/bpd.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('panduan')->insert($dataPanduan);
}
}
