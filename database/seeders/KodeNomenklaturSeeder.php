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
            // Level 0: Urusan
            ['jenis_nomenklatur' => 0, 'nomor_kode' => '5', 'nomenklatur' => 'URUSAN PEMERINTAHAN PILIHAN BIDANG PERENCANAAN'],
            // Level 1: Bidang Urusan
            ['jenis_nomenklatur' => 1, 'nomor_kode' => '5.01', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERENCANAAN'],
            // Level 2: Program
            ['jenis_nomenklatur' => 2, 'nomor_kode' => '5.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            // Level 3: Kegiatan
            ['jenis_nomenklatur' => 3, 'nomor_kode' => '5.01.01.2.01', 'nomenklatur' => 'Perencanaan, Penganggaran dan Evaluasi Kinerja Perangkat Daerah'],
            // Level 4: Sub Kegiatan 
            ['jenis_nomenklatur' => 4, 'nomor_kode' => '5.01.01.2.01.01', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
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