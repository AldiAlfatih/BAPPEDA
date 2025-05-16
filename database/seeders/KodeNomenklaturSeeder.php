<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KodeNomenklaturSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['jenis_nomenklatur' => 0, 'nomor_kode' => '1', 'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR'],
            ['jenis_nomenklatur' => 1, 'nomor_kode' => '1.01', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN'],
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '1.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA'],
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '1.01.02', 'nomenklatur' => 'PROGRAM PENGELOLAAN PENDIDIKAN'],
            ['jenis_nomenklatur' => 3, 'nomor_kode' => '1.01.01.201', 'nomenklatur' => 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah'],
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '1.01.01.201.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '1.01.01.201.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['jenis_nomenklatur' => 0, 'nomor_kode' => '2', 'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG TIDAK BERKAITAN DENGAN PELAYANAN DASAR'],
            ['jenis_nomenklatur' => 1, 'nomor_kode' => '2.22', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KEBUDAYAAN'],
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '2.22.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KEBUDAYAAN'],
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '2.22.03', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KESENIAN TRADISIONAL'],
            ['jenis_nomenklatur' => 3, 'nomor_kode' => '2.22.02.2.01', 'nomenklatur' => 'Pengelolaan Kekayaan yang Masyarakat Pelakunya dalam Daerah Kabupaten/Kota'],
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '2.22.02.2.01.0001', 'nomenklatur' => 'Pelindungan, Pengembangan, Pemanfaatan Objek Pemajuan Kebudayaan'],
        ];

        $idMap = []; // untuk menyimpan id dari setiap jenis_nomenklatur
        $now = Carbon::now();

        foreach ($data as $index => $item) {
            $id_nomenklatur = DB::table('kode_nomenklatur')->insertGetId([
                'jenis_nomenklatur' => $item['jenis_nomenklatur'],
                'nomor_kode' => $item['nomor_kode'],
                'nomenklatur' => $item['nomenklatur'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Simpan ID berdasarkan jenis_nomenklatur
            $idMap[$item['jenis_nomenklatur']] = $id_nomenklatur;

            // Buat data untuk kode_nomenklatur_detail
            $detail = [
                'id_nomenklatur' => $id_nomenklatur,
                'id_urusan' => $idMap[0] ?? null,
                'id_bidang_urusan' => $idMap[1] ?? null,
                'id_program' => $idMap[2] ?? null,
                'id_kegiatan' => $idMap[3] ?? null,
                'id_sub_kegiatan' => $idMap[4] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('kode_nomenklatur_detail')->insert($detail);
        }
    }
}
