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
            ['jenis_nomenklatur' => 0, 'nomor_kode' => '1', 'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR'], //RS HASRI AINUN HABIBIE //DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 1, 'nomor_kode' => '1.01', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 1, 'nomor_kode' => '1.02', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KESEHATAN'], //RS HASRI AINUN HABIBIE
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '1.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DASAR KABUPATEN / KOTA'], //DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '1.02.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH/ KOTA'], //RS HASRI AINUN HABIBIE
            ['jenis_nomenklatur' => 3, 'nomor_kode' => '1.01.01.2.01', 'nomenklatur' => 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 3, 'nomor_kode' => '1.02.01.2.01', 'nomenklatur' => 'Kegiatan Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah'],//RS HASRI AINUN HABIBIE
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '1.01.01.2.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '1.02.01.2.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],//RS HASRI AINUN HABIBIE
            ['jenis_nomenklatur' => 0, 'nomor_kode' => '2', 'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG TIDAK BERKAITAN DENGAN PELAYANAN DASAR'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 1, 'nomor_kode' => '2.22', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KEBUDAYAAN'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '2.22.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KEBUDAYAAN'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 3, 'nomor_kode' => '2.22.02.2.01', 'nomenklatur' => 'Pengelolaan Kekayaan yang Masyarakat Pelakunya dalam Daerah Kabupaten/Kota'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '2.22.02.2.01.0001', 'nomenklatur' => 'Pelindungan, Pengembangan, Pemanfaatan Objek Pemajuan Kebudayaan'],//DINAS PENDIDIKAN DAN KEBUDAYAAN
        ];

        $now = Carbon::now();
        $inserted = []; // Simpan data yang sudah dimasukkan: nomor_kode => id

        foreach ($data as $item) {
            // Simpan ke tabel utama
            $id_nomenklatur = DB::table('kode_nomenklatur')->insertGetId([
                'jenis_nomenklatur' => $item['jenis_nomenklatur'],
                'nomor_kode' => $item['nomor_kode'],
                'nomenklatur' => $item['nomenklatur'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $inserted[$item['nomor_kode']] = [
                'id' => $id_nomenklatur,
                'jenis' => $item['jenis_nomenklatur']
            ];

            // Cari parent berdasarkan prefix dari nomor_kode
            $parents = [
                'id_urusan' => null,
                'id_bidang_urusan' => null,
                'id_program' => null,
                'id_kegiatan' => null,
                'id_sub_kegiatan' => null,
            ];

            foreach ($inserted as $kode => $info) {
                if ($item['nomor_kode'] !== $kode && str_starts_with($item['nomor_kode'], $kode)) {
                    switch ($info['jenis']) {
                        case 0: $parents['id_urusan'] = $info['id']; break;
                        case 1: $parents['id_bidang_urusan'] = $info['id']; break;
                        case 2: $parents['id_program'] = $info['id']; break;
                        case 3: $parents['id_kegiatan'] = $info['id']; break;
                        case 4: $parents['id_sub_kegiatan'] = $info['id']; break;
                    }
                }
            }

            DB::table('kode_nomenklatur_detail')->insert(array_merge([
                'id_nomenklatur' => $id_nomenklatur,
                'created_at' => $now,
                'updated_at' => $now,
            ], $parents));
}
}
}
