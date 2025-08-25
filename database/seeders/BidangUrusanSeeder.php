<?php
// database/seeders/BidangUrusanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BidangUrusanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // Urusan 1 — Wajib (Pelayanan Dasar)
            ['nomor_kode' => '1.01', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PENDIDIKAN'],
            ['nomor_kode' => '1.02', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KESEHATAN'],
            ['nomor_kode' => '1.03', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PEKERJAAN UMUM DAN PENATAAN RUANG'],
            ['nomor_kode' => '1.04', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERUMAHAN DAN KAWASAN PERMUKIMAN'],
            ['nomor_kode' => '1.05', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KETENTERAMAN DAN KETERTIBAN UMUM SERTA PERLINDUNGAN MASYARAKAT'],
            ['nomor_kode' => '1.06', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG SOSIAL'],

            // Urusan 2 — Wajib (Non Pelayanan Dasar)
            ['nomor_kode' => '2.07', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG TENAGA KERJA'],
            ['nomor_kode' => '2.08', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK'],
            ['nomor_kode' => '2.09', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PANGAN'],
            ['nomor_kode' => '2.10', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERTANAHAN'],
            ['nomor_kode' => '2.11', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG LINGKUNGAN HIDUP'],
            ['nomor_kode' => '2.12', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG ADMINISTRASI KEPENDUDUKAN DAN PENCATATAN SIPIL'],
            ['nomor_kode' => '2.14', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA'],
            ['nomor_kode' => '2.15', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERHUBUNGAN'],
            ['nomor_kode' => '2.16', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KOMUNIKASI DAN INFORMATIKA'],
            ['nomor_kode' => '2.17', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KOPERASI, USAHA KECIL, DAN MENENGAH'],
            ['nomor_kode' => '2.18', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PENANAMAN MODAL'],
            ['nomor_kode' => '2.19', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KEPEMUDAAN DAN OLAHRAGA'],
            ['nomor_kode' => '2.20', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG STATISTIK'],
            ['nomor_kode' => '2.21', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERSANDIAN'],
            ['nomor_kode' => '2.22', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KEBUDAYAAN'],
            ['nomor_kode' => '2.23', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERPUSTAKAAN'],
            ['nomor_kode' => '2.24', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KEARSIPAN'],

            // Urusan 3 — Pilihan
            ['nomor_kode' => '3.25', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG KELAUTAN DAN PERIKANAN'],
            ['nomor_kode' => '3.26', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PARIWISATA'],
            ['nomor_kode' => '3.27', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERTANIAN'],
            ['nomor_kode' => '3.30', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERDAGANGAN'],
            ['nomor_kode' => '3.31', 'nomenklatur' => 'URUSAN PEMERINTAHAN BIDANG PERINDUSTRIAN'],

            // Urusan 4 — Unsur Pendukung
            ['nomor_kode' => '4.01', 'nomenklatur' => 'SEKRETARIAT DAERAH'],
            ['nomor_kode' => '4.02', 'nomenklatur' => 'SEKRETARIAT DPRD'],

            // Urusan 5 — Unsur Penunjang
            ['nomor_kode' => '5.01', 'nomenklatur' => 'PERENCANAAN'],
            ['nomor_kode' => '5.02', 'nomenklatur' => 'KEUANGAN'],
            ['nomor_kode' => '5.03', 'nomenklatur' => 'KEPEGAWAIAN'],
            ['nomor_kode' => '5.04', 'nomenklatur' => 'PENDIDIKAN DAN PELATIHAN'],
            ['nomor_kode' => '5.05', 'nomenklatur' => 'PENELITIAN DAN PENGEMBANGAN'],

            // Urusan 6 — Unsur Pengawasan
            ['nomor_kode' => '6.01', 'nomenklatur' => 'INSPEKTORAT DAERAH'],

            // Urusan 7 — Unsur Kewilayahan
            ['nomor_kode' => '7.01', 'nomenklatur' => 'KECAMATAN'],

            // Urusan 8 — Unsur Pemerintahan Umum
            ['nomor_kode' => '8.01', 'nomenklatur' => 'KESATUAN BANGSA DAN POLITIK'],
        ];

        foreach ($rows as $r) {
            $urusanKode = explode('.', $r['nomor_kode'])[0];

            $idUrusan = DB::table('kode_nomenklatur')
                ->where('jenis_nomenklatur', 0)
                ->where('nomor_kode', $urusanKode)
                ->value('id');

            DB::table('kode_nomenklatur')->updateOrInsert(
                ['nomor_kode' => $r['nomor_kode']],
                [
                    'jenis_nomenklatur' => 1,
                    'nomenklatur'       => $r['nomenklatur'],
                    'updated_at'        => $now,
                    'created_at'        => $now,
                ]
            );

            $id = DB::table('kode_nomenklatur')->where('nomor_kode', $r['nomor_kode'])->value('id');

            DB::table('kode_nomenklatur_detail')->updateOrInsert(
                ['id_nomenklatur' => $id],
                [
                    'id_urusan'         => $idUrusan,
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
