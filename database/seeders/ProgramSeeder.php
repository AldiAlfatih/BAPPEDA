<?php
// database/seeders/ProgramSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // ===== Urusan 1 =====
            // 1.01 Pendidikan
            ['nomor_kode' => '1.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '1.01.02', 'nomenklatur' => 'PROGRAM PENGELOLAAN PENDIDIKAN'],
            ['nomor_kode' => '1.01.04', 'nomenklatur' => 'PROGRAM PENDIDIK DAN TENAGA KEPENDIDIKAN'],

            // 1.02 Kesehatan
            ['nomor_kode' => '1.02.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '1.02.02', 'nomenklatur' => 'PROGRAM PEMENUHAN UPAYA KESEHATAN PERORANGAN DAN UPAYA KESEHATAN MASYARAKAT'],
            ['nomor_kode' => '1.02.03', 'nomenklatur' => 'PROGRAM PENINGKATAN KAPASITAS SUMBER DAYA MANUSIA KESEHATAN'],
            ['nomor_kode' => '1.02.04', 'nomenklatur' => 'PROGRAM SEDIAAN FARMASI, ALAT KESEHATAN DAN MAKANAN MINUMAN'],
            ['nomor_kode' => '1.02.05', 'nomenklatur' => 'PROGRAM PEMBERDAYAAN MASYARAKAT BIDANG KESEHATAN'],

            // 1.03 Pekerjaan Umum & Penataan Ruang
            ['nomor_kode' => '1.03.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '1.03.02', 'nomenklatur' => 'PROGRAM PENGELOLAAN SUMBER DAYA AIR (SDA)'],
            ['nomor_kode' => '1.03.03', 'nomenklatur' => 'PROGRAM PENGELOLAAN DAN PENGEMBANGAN SISTEM PENYEDIAAN AIR MINUM'],
            ['nomor_kode' => '1.03.05', 'nomenklatur' => 'PROGRAM PENGELOLAAN DAN PENGEMBANGAN SISTEM AIR LIMBAH'],
            ['nomor_kode' => '1.03.06', 'nomenklatur' => 'PROGRAM PENGELOLAAN DAN PENGEMBANGAN SISTEM DRAINASE'],
            ['nomor_kode' => '1.03.08', 'nomenklatur' => 'PROGRAM PENATAAN BANGUNAN GEDUNG'],
            ['nomor_kode' => '1.03.09', 'nomenklatur' => 'PROGRAM PENATAAN BANGUNAN DAN LINGKUNGANNYA'],
            ['nomor_kode' => '1.03.10', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN JALAN'],
            ['nomor_kode' => '1.03.11', 'nomenklatur' => 'PROGRAM PENGEMBANGAN JASA KONSTRUKSI'],
            ['nomor_kode' => '1.03.12', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN PENATAAN RUANG'],

            // 1.04 Perumahan & Kawasan Permukiman
            ['nomor_kode' => '1.04.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '1.04.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN PERUMAHAN'],
            ['nomor_kode' => '1.04.03', 'nomenklatur' => 'PROGRAM KAWASAN PERMUKIMAN'],
            ['nomor_kode' => '1.04.04', 'nomenklatur' => 'PROGRAM PERUMAHAN DAN KAWASAN PERMUKIMAN KUMUH'],
            ['nomor_kode' => '1.04.05', 'nomenklatur' => 'PROGRAM PENINGKATAN PRASARANA, SARANA DAN UTILITAS UMUM (PSU)'],

            // 1.05 Trantibum & Linmas
            ['nomor_kode' => '1.05.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '1.05.02', 'nomenklatur' => 'PROGRAM PENINGKATAN KETENTERAMAN DAN KETERTIBAN UMUM'],
            ['nomor_kode' => '1.05.03', 'nomenklatur' => 'PROGRAM PENANGGULANGAN BENCANA'],
            ['nomor_kode' => '1.05.04', 'nomenklatur' => 'PROGRAM PENCEGAHAN, PENANGGULANGAN, PENYELAMATAN KEBAKARAN DAN PENYELAMATAN NON KEBAKARAN'],

            // 1.06 Sosial
            ['nomor_kode' => '1.06.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '1.06.02', 'nomenklatur' => 'PROGRAM PEMBERDAYAAN SOSIAL'],
            ['nomor_kode' => '1.06.03', 'nomenklatur' => 'PROGRAM PENANGANAN WARGA NEGARA MIGRAN KORBAN TINDAK KEKERASAN'],
            ['nomor_kode' => '1.06.04', 'nomenklatur' => 'PROGRAM REHABILITASI SOSIAL'],
            ['nomor_kode' => '1.06.05', 'nomenklatur' => 'PROGRAM PERLINDUNGAN DAN JAMINAN SOSIAL'],
            ['nomor_kode' => '1.06.06', 'nomenklatur' => 'PROGRAM PENANGANAN BENCANA'],
            ['nomor_kode' => '1.06.07', 'nomenklatur' => 'PROGRAM PENGELOLAAN TAMAN MAKAM PAHLAWAN'],

            // ===== Urusan 2 =====
            // 2.07 Tenaga Kerja
            ['nomor_kode' => '2.07.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.07.03', 'nomenklatur' => 'PROGRAM PELATIHAN KERJA DAN PRODUKTIVITAS TENAGA KERJA'],
            ['nomor_kode' => '2.07.04', 'nomenklatur' => 'PROGRAM PENEMPATAN TENAGA KERJA'],
            ['nomor_kode' => '2.07.05', 'nomenklatur' => 'PROGRAM HUBUNGAN INDUSTRIAL'],

            // 2.08 PP & PA
            ['nomor_kode' => '2.08.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.08.02', 'nomenklatur' => 'PROGRAM PENGARUSUTAMAAN GENDER DAN PEMBERDAYAAN PEREMPUAN'],
            ['nomor_kode' => '2.08.03', 'nomenklatur' => 'PROGRAM PERLINDUNGAN PEREMPUAN'],
            ['nomor_kode' => '2.08.04', 'nomenklatur' => 'PROGRAM PENINGKATAN KUALITAS KELUARGA'],
            ['nomor_kode' => '2.08.06', 'nomenklatur' => 'PROGRAM PEMENUHAN HAK ANAK (PHA)'],
            ['nomor_kode' => '2.08.07', 'nomenklatur' => 'PROGRAM PERLINDUNGAN KHUSUS ANAK'],

            // 2.09 Pangan
            ['nomor_kode' => '2.09.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.09.03', 'nomenklatur' => 'PROGRAM PENINGKATAN DIVERSIFIKASI DAN KETAHANAN PANGAN MASYARAKAT'],
            ['nomor_kode' => '2.09.04', 'nomenklatur' => 'PROGRAM PENANGANAN KERAWANAN PANGAN'],
            ['nomor_kode' => '2.09.05', 'nomenklatur' => 'PROGRAM PENGAWASAN KEAMANAN PANGAN'],

            // 2.10 Pertanahan
            ['nomor_kode' => '2.10.04', 'nomenklatur' => 'PROGRAM PENYELESAIAN SENGKETA TANAH GARAPAN'],
            ['nomor_kode' => '2.10.05', 'nomenklatur' => 'PROGRAM PENYELESAIAN GANTI KERUGIAN DAN SANTUNAN TANAH UNTUK PEMBANGUNAN'],
            ['nomor_kode' => '2.10.08', 'nomenklatur' => 'PROGRAM PENGELOLAAN TANAH KOSONG'],

            // 2.11 Lingkungan Hidup
            ['nomor_kode' => '2.11.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.11.03', 'nomenklatur' => 'PROGRAM PENGENDALIAN PENCEMARAN DAN/ATAU KERUSAKAN LINGKUNGAN HIDUP'],
            ['nomor_kode' => '2.11.04', 'nomenklatur' => 'PROGRAM PENGELOLAAN KEANEKARAGAMAN HAYATI (KEHATI)'],
            ['nomor_kode' => '2.11.05', 'nomenklatur' => 'PROGRAM PENGENDALIAN BAHAN BERBAHAYA DAN BERACUN (B3) DAN LIMBAH BAHAN BERBAHAYA DAN BERACUN (LIMBAH B3)'],
            ['nomor_kode' => '2.11.09', 'nomenklatur' => 'PROGRAM PENGHARGAAN LINGKUNGAN HIDUP UNTUK MASYARAKAT'],
            ['nomor_kode' => '2.11.11', 'nomenklatur' => 'PROGRAM PENGELOLAAN PERSAMPAHAN'],

            // 2.12 Adminduk & Capil
            ['nomor_kode' => '2.12.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.12.02', 'nomenklatur' => 'PROGRAM PENDAFTARAN PENDUDUK'],
            ['nomor_kode' => '2.12.03', 'nomenklatur' => 'PROGRAM PENCATATAN SIPIL'],
            ['nomor_kode' => '2.12.04', 'nomenklatur' => 'PROGRAM PENGELOLAAN INFORMASI ADMINISTRASI KEPENDUDUKAN'],
            ['nomor_kode' => '2.12.05', 'nomenklatur' => 'PROGRAM PENGELOLAAN PROFIL KEPENDUDUKAN'],

            // 2.14 PENGENDALIAN PENDUDUK & KB
            ['nomor_kode' => '2.14.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.14.02', 'nomenklatur' => 'PROGRAM PENGENDALIAN PENDUDUK'],
            ['nomor_kode' => '2.14.03', 'nomenklatur' => 'PROGRAM PEMBINAAN KELUARGA BERENCANA (KB)'],
            ['nomor_kode' => '2.14.04', 'nomenklatur' => 'PROGRAM PEMBERDAYAAN DAN PENINGKATAN KELUARGA SEJAHTERA (KS)'],

            // 2.15 Perhubungan
            ['nomor_kode' => '2.15.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.15.02', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN LALU LINTAS DAN ANGKUTAN JALAN (LLAJ)'],

            // 2.16 Kominfo
            ['nomor_kode' => '2.16.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.16.02', 'nomenklatur' => 'PROGRAM PENGELOLAAN INFORMASI DAN KOMUNIKASI PUBLIK'],
            ['nomor_kode' => '2.16.03', 'nomenklatur' => 'PROGRAM PENGELOLAAN APLIKASI INFORMATIKA'],

            // 2.17 Koperasi & UKM
            ['nomor_kode' => '2.17.03', 'nomenklatur' => 'PROGRAM PENGAWASAN DAN PEMERIKSAAN KOPERASI'],
            ['nomor_kode' => '2.17.07', 'nomenklatur' => 'PROGRAM PEMBERDAYAAN USAHA MENENGAH, USAHA KECIL, DAN USAHA MIKRO (UMKM)'],

            // 2.18 Penanaman Modal
            ['nomor_kode' => '2.18.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.18.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN IKLIM PENANAMAN MODAL'],
            ['nomor_kode' => '2.18.03', 'nomenklatur' => 'PROGRAM PROMOSI PENANAMAN MODAL'],
            ['nomor_kode' => '2.18.04', 'nomenklatur' => 'PROGRAM PELAYANAN PENANAMAN MODAL'],
            ['nomor_kode' => '2.18.06', 'nomenklatur' => 'PROGRAM PENGELOLAAN DATA DAN SISTEM INFORMASI PENANAMAN MODAL'],

            // 2.19 Kepemudaan & Olahraga
            ['nomor_kode' => '2.19.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.19.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KAPASITAS DAYA SAING KEPEMUDAAN'],
            ['nomor_kode' => '2.19.03', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KAPASITAS DAYA SAING KEOLAHRAGAAN'],
            ['nomor_kode' => '2.19.04', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KAPASITAS KEPRAMUKAAN'],

            // 2.20 Statistik
            ['nomor_kode' => '2.20.02', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN STATISTIK SEKTORAL'],

            // 2.21 Persandian
            ['nomor_kode' => '2.21.02', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN PERSANDIAN UNTUK PENGAMANAN INFORMASI'],

            // 2.22 Kebudayaan
            ['nomor_kode' => '2.22.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KEBUDAYAAN'],
            ['nomor_kode' => '2.22.03', 'nomenklatur' => 'PROGRAM PENGEMBANGAN KESENIAN TRADISIONAL'],
            ['nomor_kode' => '2.22.04', 'nomenklatur' => 'PROGRAM PEMBINAAN SEJARAH'],
            ['nomor_kode' => '2.22.05', 'nomenklatur' => 'PROGRAM PELESTARIAN DAN PENGELOLAAN CAGAR BUDAYA'],
            ['nomor_kode' => '2.22.06', 'nomenklatur' => 'PROGRAM PENGELOLAAN PERMUSEUMAN'],

            // 2.23 Perpustakaan
            ['nomor_kode' => '2.23.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '2.23.02', 'nomenklatur' => 'PROGRAM PEMBINAAN PERPUSTAKAAN'],

            // 2.24 Kearsipan
            ['nomor_kode' => '2.24.02', 'nomenklatur' => 'PROGRAM PENGELOLAAN ARSIP'],

            // ===== Urusan 3 =====
            // 3.25 Kelautan & Perikanan
            ['nomor_kode' => '3.25.03', 'nomenklatur' => 'PROGRAM PENGELOLAAN PERIKANAN TANGKAP'],
            ['nomor_kode' => '3.25.04', 'nomenklatur' => 'PROGRAM PENGELOLAAN PERIKANAN BUDIDAYA'],
            ['nomor_kode' => '3.25.06', 'nomenklatur' => 'PROGRAM PENGOLAHAN DAN PEMASARAN HASIL PERIKANAN'],

            // 3.26 Pariwisata
            ['nomor_kode' => '3.26.02', 'nomenklatur' => 'PROGRAM PENINGKATAN DAYA TARIK DESTINASI PARIWISATA'],
            ['nomor_kode' => '3.26.03', 'nomenklatur' => 'PROGRAM PEMASARAN PARIWISATA'],
            ['nomor_kode' => '3.26.05', 'nomenklatur' => 'PROGRAM PENGEMBANGAN SUMBER DAYA PARIWISATA DAN EKONOMI KREATIF'],

            // 3.27 Pertanian
            ['nomor_kode' => '3.27.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '3.27.02', 'nomenklatur' => 'PROGRAM PENYEDIAAN DAN PENGEMBANGAN SARANA PERTANIAN'],
            ['nomor_kode' => '3.27.03', 'nomenklatur' => 'PROGRAM PENYEDIAAN DAN PENGEMBANGAN PRASARANA PERTANIAN'],
            ['nomor_kode' => '3.27.04', 'nomenklatur' => 'PROGRAM PENGENDALIAN KESEHATAN HEWAN DAN KESEHATAN MASYARAKAT VETERINER'],
            ['nomor_kode' => '3.27.05', 'nomenklatur' => 'PROGRAM PENGENDALIAN DAN PENANGGULANGAN BENCANA PERTANIAN'],
            ['nomor_kode' => '3.27.07', 'nomenklatur' => 'PROGRAM PENYULUHAN PERTANIAN'],

            // 3.30 Perdagangan
            ['nomor_kode' => '3.30.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '3.30.03', 'nomenklatur' => 'PROGRAM PENINGKATAN SARANA DISTRIBUSI PERDAGANGAN'],
            ['nomor_kode' => '3.30.04', 'nomenklatur' => 'PROGRAM STABILISASI HARGA BARANG KEBUTUHAN POKOK DAN BARANG PENTING'],
            ['nomor_kode' => '3.30.06', 'nomenklatur' => 'PROGRAM STANDARDISASI DAN PERLINDUNGAN KONSUMEN'],

            // 3.31 Perindustrian
            ['nomor_kode' => '3.31.02', 'nomenklatur' => 'PROGRAM PERENCANAAN DAN PEMBANGUNAN INDUSTRI'],

            // ===== Urusan 4 =====
            // 4.01 Sekretariat Daerah
            ['nomor_kode' => '4.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '4.01.02', 'nomenklatur' => 'PROGRAM PEMERINTAHAN DAN KESEJAHTERAAN RAKYAT'],
            ['nomor_kode' => '4.01.03', 'nomenklatur' => 'PROGRAM PEREKONOMIAN DAN PEMBANGUNAN'],

            // 4.02 Sekretariat DPRD
            ['nomor_kode' => '4.02.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '4.02.02', 'nomenklatur' => 'PROGRAM DUKUNGAN PELAKSANAAN TUGAS DAN FUNGSI DPRD'],

            // ===== Urusan 5 =====
            // 5.01 Perencanaan
            ['nomor_kode' => '5.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '5.01.02', 'nomenklatur' => 'PROGRAM PERENCANAAN, PENGENDALIAN DAN EVALUASI PEMBANGUNAN DAERAH'],
            ['nomor_kode' => '5.01.03', 'nomenklatur' => 'PROGRAM KOORDINASI DAN SINKRONISASI PERENCANAAN PEMBANGUNAN DAERAH'],

            // 5.02 Keuangan
            ['nomor_kode' => '5.02.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '5.02.02', 'nomenklatur' => 'PROGRAM PENGELOLAAN KEUANGAN DAERAH'],
            ['nomor_kode' => '5.02.03', 'nomenklatur' => 'PROGRAM PENGELOLAAN BARANG MILIK DAERAH'],
            ['nomor_kode' => '5.02.04', 'nomenklatur' => 'PROGRAM PENGELOLAAN PENDAPATAN DAERAH'],

            // 5.03 Kepegawaian
            ['nomor_kode' => '5.03.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '5.03.02', 'nomenklatur' => 'PROGRAM KEPEGAWAIAN DAERAH'],

            // 5.04 Diklat
            ['nomor_kode' => '5.04.02', 'nomenklatur' => 'PROGRAM PENGEMBANGAN SUMBER DAYA MANUSIA'],

            // 5.05 Litbang
            ['nomor_kode' => '5.05.02', 'nomenklatur' => 'PROGRAM PENELITIAN DAN PENGEMBANGAN DAERAH'],

            // ===== Urusan 6 =====
            // 6.01 Inspektorat
            ['nomor_kode' => '6.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '6.01.02', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN PENGAWASAN'],
            ['nomor_kode' => '6.01.03', 'nomenklatur' => 'PROGRAM PERUMUSAN KEBIJAKAN, PENDAMPINGAN DAN ASISTENSI'],

            // ===== Urusan 7 =====
            // 7.01 Kecamatan
            ['nomor_kode' => '7.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '7.01.02', 'nomenklatur' => 'PROGRAM PENYELENGGARAAN PEMERINTAHAN DAN PELAYANAN PUBLIK'],
            ['nomor_kode' => '7.01.03', 'nomenklatur' => 'PROGRAM PEMBERDAYAAN MASYARAKAT DESA DAN KELURAHAN'],
            ['nomor_kode' => '7.01.04', 'nomenklatur' => 'PROGRAM KOORDINASI KETENTRAMAN DAN KETERTIBAN UMUM'],

            // ===== Urusan 8 =====
            // 8.01 Kesbangpol
            ['nomor_kode' => '8.01.01', 'nomenklatur' => 'PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA'],
            ['nomor_kode' => '8.01.02', 'nomenklatur' => 'PROGRAM PENGUATAN IDEOLOGI PANCASILA DAN KARAKTER KEBANGSAAN'],
            ['nomor_kode' => '8.01.03', 'nomenklatur' => 'PROGRAM PENINGKATAN PERAN PARTAI POLITIK DAN LEMBAGA PENDIDIKAN MELALUI PENDIDIKAN POLITIK DAN PENGEMBANGAN ETIKA SERTA BUDAYA POLITIK'],
            ['nomor_kode' => '8.01.04', 'nomenklatur' => 'PROGRAM PEMBERDAYAAN DAN PENGAWASAN ORGANISASI KEMASYARAKATAN'],
            ['nomor_kode' => '8.01.05', 'nomenklatur' => 'PROGRAM PEMBINAAN DAN PENGEMBANGAN KETAHANAN EKONOMI, SOSIAL, DAN BUDAYA'],
            ['nomor_kode' => '8.01.06', 'nomenklatur' => 'PROGRAM PENINGKATAN KEWASPADAAN NASIONAL DAN PENINGKATAN KUALITAS DAN FASILITASI PENANGANAN KONFLIK SOSIAL'],
        ];

        foreach ($rows as $r) {
            // resolve parent bidang (e.g. '1.01' from '1.01.02')
            $parts = explode('.', $r['nomor_kode']);
            $bidangKode = $parts[0] . '.' . $parts[1];
            $urusanKode = $parts[0];

            $idUrusan = DB::table('kode_nomenklatur')
                ->where('jenis_nomenklatur', 0)
                ->where('nomor_kode', $urusanKode)
                ->value('id');

            $idBidang = DB::table('kode_nomenklatur')
                ->where('jenis_nomenklatur', 1)
                ->where('nomor_kode', $bidangKode)
                ->value('id');

            DB::table('kode_nomenklatur')->updateOrInsert(
                ['nomor_kode' => $r['nomor_kode']],
                [
                    'jenis_nomenklatur' => 2,
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
                    'id_bidang_urusan'  => $idBidang,
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
