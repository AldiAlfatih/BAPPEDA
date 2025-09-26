<?php
// database/seeders/UrusanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UrusanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // 1
            ['nomor_kode' => '1', 'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG BERKAITAN DENGAN PELAYANAN DASAR'],
            // 2
            ['nomor_kode' => '2', 'nomenklatur' => 'URUSAN PEMERINTAHAN WAJIB YANG TIDAK BERKAITAN DENGAN PELAYANAN DASAR'],
            // 3
            ['nomor_kode' => '3', 'nomenklatur' => 'URUSAN PEMERINTAHAN PILIHAN'],
            // 4
            ['nomor_kode' => '4', 'nomenklatur' => 'UNSUR PENDUKUNG URUSAN PEMERINTAHAN'],
            // 5
            ['nomor_kode' => '5', 'nomenklatur' => 'UNSUR PENUNJANG URUSAN PEMERINTAHAN'],
            // 6
            ['nomor_kode' => '6', 'nomenklatur' => 'UNSUR PENGAWASAN URUSAN PEMERINTAHAN'],
            // 7
            ['nomor_kode' => '7', 'nomenklatur' => 'UNSUR KEWILAYAHAN'],
            // 8
            ['nomor_kode' => '8', 'nomenklatur' => 'UNSUR PEMERINTAHAN UMUM'],
        ];

        foreach ($rows as $r) {
            DB::table('kode_nomenklatur')->updateOrInsert(
                ['nomor_kode' => $r['nomor_kode']],
                [
                    'jenis_nomenklatur' => 0,
                    'nomenklatur'       => $r['nomenklatur'],
                    'updated_at'        => $now,
                    'created_at'        => $now,
                ]
            );

            $id = DB::table('kode_nomenklatur')->where('nomor_kode', $r['nomor_kode'])->value('id');

            // detail (parent semua null di level urusan)
            DB::table('kode_nomenklatur_detail')->updateOrInsert(
                ['id_nomenklatur' => $id],
                [
                    'id_urusan'         => null,
                    'id_bidang_urusan'  => null,
                    'id_program'        => null,
                    'id_kegiatan'       => null,
                    'id_sub_kegiatan'   => null,
                    'updated_at'        => $now,
                    'created_at'        => $now,
                ]
            );
        }
    }
}
