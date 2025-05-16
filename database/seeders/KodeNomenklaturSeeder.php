<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeNomenklaturSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Urusan 1
            [
                'jenis_nomenklatur' => 0,
                'nomor_kode' => '1',
                'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR',
            ],
            [
                'jenis_nomenklatur' => 1,
                'nomor_kode' => '1.01',
                'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN',
            ],
            [
                'jenis_nomenklatur' => 2,
                'nomor_kode' => '1.01.01',
                'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA',
            ],
            [
                'jenis_nomenklatur' => 2,
                'nomor_kode' => '1.01.02',
                'nomenklatur' => 'PROGRAM PENGELOLAAN PENDIDIKAN',
            ],
            [
                'jenis_nomenklatur' => 3,
                'nomor_kode' => '1.01.01.201',
                'nomenklatur' => 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah',
            ],
            [
                'jenis_nomenklatur' => 4,
                'nomor_kode' => '1.01.01.201.0001',
                'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah',
            ],
            [
                'jenis_nomenklatur' => 4,
                'nomor_kode' => '1.01.01.201.0002',
                'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD',
            ],

            // Urusan 2
            [
                'jenis_nomenklatur' => 0,
                'nomor_kode' => '2',
                'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG TIDAK BERKAITAN DENGAN PELAYANAN DASAR',
            ],
            [
                'jenis_nomenklatur' => 1,
                'nomor_kode' => '2.22',
                'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KEBUDAYAAN',
            ],
            [
                'jenis_nomenklatur' => 2,
                'nomor_kode' => '2.22.02',
                'nomenklatur' => 'PROGRAM PENGEMBANGAN KEBUDAYAAN',
            ],
            [
                'jenis_nomenklatur' => 2,
                'nomor_kode' => '2.22.03',
                'nomenklatur' => 'PROGRAM PENGEMBANGAN KESENIAN TRADISIONAL',
            ],
            [
                'jenis_nomenklatur' => 3,
                'nomor_kode' => '2.22.02.2.01',
                'nomenklatur' => 'Pengelolaan Kekayaan yang Masyarakat Pelakunya dalam Daerah Kabupaten/Kota',
            ],
            [
                'jenis_nomenklatur' => 4,
                'nomor_kode' => '2.22.02.2.01.0001',
                'nomenklatur' => 'Pelindungan, Pengembangan, Pemanfaatan Objek Pemajuan Kebudayaan',
            ],
        ];

        DB::table('kode_nomenklatur')->insert($data);
}
}

