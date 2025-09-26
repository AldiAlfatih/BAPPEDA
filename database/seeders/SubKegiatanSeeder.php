<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // URUSAN PENDIDIKAN (1.01)
            // PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA
            ['nomor_kode' => '1.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.01.01.2.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.01.01.2.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.01.01.2.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.01.01.2.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.01.01.2.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.01.01.2.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.01.01.2.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '1.01.01.2.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.01.01.2.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.01.01.2.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.01.01.2.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.01.01.2.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.01.01.2.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.01.01.2.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '1.01.01.2.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.01.01.2.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.01.01.2.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.01.01.2.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.01.01.2.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.01.01.2.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '1.01.01.2.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.01.01.2.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.01.01.2.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.01.01.2.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.01.01.2.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // PROGRAM PENGELOLAAN PENDIDIKAN
            ['nomor_kode' => '1.01.02.2.01.0001', 'nomenklatur' => 'Pembangunan Unit Sekolah Baru (USB)'],
            ['nomor_kode' => '1.01.02.2.01.0006', 'nomenklatur' => 'Pembangunan Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.02.2.01.0014', 'nomenklatur' => 'Pengadaan Mebel Sekolah'],
            ['nomor_kode' => '1.01.02.2.01.0016', 'nomenklatur' => 'Pengadaan Perlengkapan Sekolah'],
            ['nomor_kode' => '1.01.02.2.01.0025', 'nomenklatur' => 'Pembinaan Minat, Bakat dan Kreativitas Siswa'],
            ['nomor_kode' => '1.01.02.2.01.0026', 'nomenklatur' => 'Penyediaan Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Sekolah Dasar'],
            ['nomor_kode' => '1.01.02.2.01.0027', 'nomenklatur' => 'Pengembangan Karir Pendidik dan Tenaga Kependidikan pada Satuan Pendidikan Sekolah Dasar'],
            ['nomor_kode' => '1.01.02.2.01.0028', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen Sekolah'],
            ['nomor_kode' => '1.01.02.2.01.0029', 'nomenklatur' => 'Pengelolaan Dana BOS Sekolah Dasar'],
            ['nomor_kode' => '1.01.02.2.01.0035', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.02.2.01.0036', 'nomenklatur' => 'Pengembangan konten digital untuk pendidikan'],
            ['nomor_kode' => '1.01.02.2.01.0037', 'nomenklatur' => 'Pelatihan Penggunaan Aplikasi Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.01.0041', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.02.2.01.0043', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.02.2.01.0048', 'nomenklatur' => 'Rehabilitasi Sedang/Berat Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.02.2.01.0049', 'nomenklatur' => 'Bimbingan Teknis, Pelatihan, dan/atau Magang/PKL untuk Peningkatan Kapasitas Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.01.0050', 'nomenklatur' => 'Penyelenggaraan Proses Belajar Bagi Peserta Didik'],
            ['nomor_kode' => '1.01.02.2.01.0054', 'nomenklatur' => 'Penyediaan Biaya Personil Peserta Didik Sekolah Dasar'],
            ['nomor_kode' => '1.01.02.2.01.0055', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik'],
            ['nomor_kode' => '1.01.02.2.02.0012', 'nomenklatur' => 'Pembangunan Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.02.2.02.0024', 'nomenklatur' => 'Rehabilitasi Sedang/Berat Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.02.2.02.0027', 'nomenklatur' => 'Pengadaan Perlengkapan Sekolah'],
            ['nomor_kode' => '1.01.02.2.02.0032', 'nomenklatur' => 'Penyediaan Biaya Personil Peserta Didik Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.02.2.02.0038', 'nomenklatur' => 'Pembinaan Minat, Bakat dan Kreativitas Siswa'],
            ['nomor_kode' => '1.01.02.2.02.0039', 'nomenklatur' => 'Penyediaan Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.02.2.02.0040', 'nomenklatur' => 'Pengembangan Karir Pendidik dan Tenaga Kependidikan pada Satuan Pendidikan Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.02.2.02.0041', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen Sekolah'],
            ['nomor_kode' => '1.01.02.2.02.0042', 'nomenklatur' => 'Pengelolaan Dana BOS Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.02.2.02.0048', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.02.2.02.0049', 'nomenklatur' => 'Pengembangan konten digital untuk pendidikan'],
            ['nomor_kode' => '1.01.02.2.02.0050', 'nomenklatur' => 'Pelatihan Penggunaan Aplikasi Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.02.0054', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.02.2.02.0055', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.02.2.02.0058', 'nomenklatur' => 'Penyelenggaraan Proses Belajar bagi Peserta Didik'],
            ['nomor_kode' => '1.01.02.2.02.0060', 'nomenklatur' => 'Bimbingan Teknis, Pelatihan, dan/atau Magang/PKL untuk Peningkatan Kapasitas Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.02.0067', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik'],
            ['nomor_kode' => '1.01.02.2.03.0015', 'nomenklatur' => 'Penyediaan Pendidik dan Tenaga Kependidikan bagi Satuan PAUD'],
            ['nomor_kode' => '1.01.02.2.03.0016', 'nomenklatur' => 'Pengembangan Karir Pendidik dan Tenaga Kependidikan pada Satuan Pendidikan PAUD'],
            ['nomor_kode' => '1.01.02.2.03.0017', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen PAUD'],
            ['nomor_kode' => '1.01.02.2.03.0022', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.02.2.03.0026', 'nomenklatur' => 'Sosialisasi dan Advokasi Kebijakan Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.03.0029', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.02.2.03.0037', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.02.2.03.0039', 'nomenklatur' => 'Bimbingan Teknis, Pelatihan, dan/atau Magang/PKL untuk Peningkatan Kapasitas Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.03.0040', 'nomenklatur' => 'Pembangunan Unit Sekolah Baru (USB)'],
            ['nomor_kode' => '1.01.02.2.03.0045', 'nomenklatur' => 'Rehabilitasi Sedang/Berat Sarana, Prasarana dan Utilitas PAUD'],
            ['nomor_kode' => '1.01.02.2.03.0046', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik PAUD'],
            ['nomor_kode' => '1.01.02.2.03.0047', 'nomenklatur' => 'Penyelenggaraan Proses Belajar PAUD'],
            ['nomor_kode' => '1.01.02.2.04.0016', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen Sekolah Nonformal/Kesetaraan'],
            ['nomor_kode' => '1.01.02.2.04.0024', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.02.2.04.0027', 'nomenklatur' => 'Koordinasi, Perencanaan, Supervisi dan Evaluasi Layanan di Bidang Pendidikan'],
            ['nomor_kode' => '1.01.02.2.04.0030', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.02.2.04.0031', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.02.2.04.0046', 'nomenklatur' => 'Penyelenggaraan Proses Belajar bagi Peserta Didik'],
            ['nomor_kode' => '1.01.02.2.04.0049', 'nomenklatur' => 'Pemeliharaan Rutin Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.02.2.04.0055', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik Nonformal / Kesetaraan'],

            // PROGRAM PENDIDIK DAN TENAGA KEPENDIDIKAN
            ['nomor_kode' => '1.01.04.2.01.0001', 'nomenklatur' => 'Perhitungan dan Pemetaan Pendidik dan Tenaga Kependidikan Satuan Pendidikan Dasar, PAUD, dan Pendidikan Nonformal/Kesetaraan'],
            ['nomor_kode' => '1.01.04.2.01.0002', 'nomenklatur' => 'Penataan Pendistribusian Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Dasar, PAUD, dan Pendidikan Nonformal/Kesetaraan'],

            // URUSAN KESEHATAN (1.02)
            // PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA (DINAS KESEHATAN)
            ['nomor_kode' => '1.02.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.02.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.02.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.02.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.02.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.02.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.02.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.02.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.02.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.02.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.02.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.02.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.02.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.02.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.02.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.02.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.02.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '1.02.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.02.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.02.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.02.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.02.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.02.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.02.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '1.02.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.02.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.02.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.02.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '1.02.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.02.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.02.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.02.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '1.02.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            
            // PROGRAM PEMENUHAN UPAYA KESEHATAN
            ['nomor_kode' => '1.02.02.02.01.0001', 'nomenklatur' => 'Pembangunan Rumah Sakit beserta Sarana dan Prasarana Pendukungnya'],
            ['nomor_kode' => '1.02.02.02.01.0002', 'nomenklatur' => 'Pembangunan Puskesmas'],
            ['nomor_kode' => '1.02.02.02.01.0003', 'nomenklatur' => 'Pembangunan Fasilitas Kesehatan Lainnya'],
            ['nomor_kode' => '1.02.02.02.01.0006', 'nomenklatur' => 'Pengembangan Puskesmas'],
            ['nomor_kode' => '1.02.02.02.01.0007', 'nomenklatur' => 'Pengembangan Fasilitas Kesehatan Lainnya'],
            ['nomor_kode' => '1.02.02.02.01.0008', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Rumah Sakit'],
            ['nomor_kode' => '1.02.02.02.01.0009', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Puskesmas'],
            ['nomor_kode' => '1.02.02.02.01.0010', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Fasilitas Kesehatan Lainnya'],
            ['nomor_kode' => '1.02.02.02.01.0011', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Rumah Dinas Tenaga Kesehatan'],
            ['nomor_kode' => '1.02.02.02.01.0014', 'nomenklatur' => 'Pengadaan Alat Kesehatan/Alat Penunjang Medik Fasilitas Pelayanan Kesehatan'],
            ['nomor_kode' => '1.02.02.02.01.0015', 'nomenklatur' => 'Pengadaan dan Pemeliharaan Alat Kalibrasi'],
            ['nomor_kode' => '1.02.02.02.01.0020', 'nomenklatur' => 'Pemeliharaan Rutin dan Berkala Alat Kesehatan/Alat Penunjang Medik Fasilitas Pelayanan Kesehatan'],
            ['nomor_kode' => '1.02.02.02.01.0022', 'nomenklatur' => 'Pengembangan Rumah Sakit'],
            ['nomor_kode' => '1.02.02.02.01.0023', 'nomenklatur' => 'Pengadaan Obat, Bahan Habis Pakai, Bahan Medis Habis Pakai,, Vaksin, Makanan dan Minuman di Fasilitas Kesehatan'],
            ['nomor_kode' => '1.02.02.02.01.0024', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Dasar Melalui Pendekatan Keluarga'],
            ['nomor_kode' => '1.02.02.02.01.0026', 'nomenklatur' => 'Distribusi Alat Kesehatan, Obat, Bahan Habis Pakai, Bahan Medis Habis Pakai, Vaksin, Makanan dan Minuman ke Fasilitas Kesehatan'],
            ['nomor_kode' => '1.02.02.02.02.0001', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Ibu Hamil'],
            ['nomor_kode' => '1.02.02.02.02.0002', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Ibu Bersalin'],
            ['nomor_kode' => '1.02.02.02.02.0003', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Bayi Baru Lahir'],
            ['nomor_kode' => '1.02.02.02.02.0004', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Balita'],
            ['nomor_kode' => '1.02.02.02.02.0005', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan pada Usia Pendidikan Dasar'],
            ['nomor_kode' => '1.02.02.02.02.0006', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan pada Usia Produktif'],
            ['nomor_kode' => '1.02.02.02.02.0007', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan pada Usia Lanjut'],
            ['nomor_kode' => '1.02.02.02.02.0008', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Penderita Hipertensi'],
            ['nomor_kode' => '1.02.02.02.02.0009', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Penderita Diabetes Melitus'],
            ['nomor_kode' => '1.02.02.02.02.0010', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Orang dengan Gangguan Jiwa Berat'],
            ['nomor_kode' => '1.02.02.02.02.0011', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Orang Terduga Tuberkulosis'],
            ['nomor_kode' => '1.02.02.02.02.0012', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Orang dengan Risiko Terinfeksi HIV'],
            ['nomor_kode' => '1.02.02.02.02.0013', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan bagi Penduduk pada Kondisi Kejadian Luar Biasa (KLB)'],
            ['nomor_kode' => '1.02.02.02.02.0014', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan bagi Penduduk Terdampak Krisis Kesehatan Akibat Bencana dan/atau Berpotensi Bencana'],
            ['nomor_kode' => '1.02.02.02.02.0015', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Gizi Masyarakat'],
            ['nomor_kode' => '1.02.02.02.02.0016', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Kerja dan Olahraga'],
            ['nomor_kode' => '1.02.02.02.02.0017', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Lingkungan'],
            ['nomor_kode' => '1.02.02.02.02.0018', 'nomenklatur' => 'Pengelolaan Pelayanan Promosi Kesehatan'],
            ['nomor_kode' => '1.02.02.02.02.0019', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Tradisional, Akupuntur, Asuhan Mandiri, dan Tradisional Lainnya'],
            ['nomor_kode' => '1.02.02.02.02.0020', 'nomenklatur' => 'Pengelolaan Surveilans Kesehatan'],
            ['nomor_kode' => '1.02.02.02.02.0021', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Orang dengan Masalah Kesehatan Jiwa (ODMK)'],
            ['nomor_kode' => '1.02.02.02.02.0022', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Jiwa dan NAPZA'],
            ['nomor_kode' => '1.02.02.02.02.0024', 'nomenklatur' => 'Pengelolaan Upaya Pengurangan Risiko Krisis Kesehatan dan Pasca Krisis Kesehatan'],
            ['nomor_kode' => '1.02.02.02.02.0025', 'nomenklatur' => 'Pelayanan Kesehatan Penyakit Menular dan Tidak Menular'],
            ['nomor_kode' => '1.02.02.02.02.0026', 'nomenklatur' => 'Pengelolaan Jaminan Kesehatan Masyarakat'],
            ['nomor_kode' => '1.02.02.02.02.0027', 'nomenklatur' => 'Deteksi Dini Penyalahgunaan NAPZA di Fasyankes dan Sekolah'],
            ['nomor_kode' => '1.02.02.02.02.0028', 'nomenklatur' => 'Pengambilan dan Pengiriman Spesimen Penyakit Potensial KLB ke Laboratorium Rujukan/Nasional'],
            ['nomor_kode' => '1.02.02.02.02.0029', 'nomenklatur' => 'Penyelenggaraan Kabupaten/Kota Sehat'],
            ['nomor_kode' => '1.02.02.02.02.0030', 'nomenklatur' => 'Penyediaan Telemedicine di Fasilitas Pelayanan Kesehatan'],
            ['nomor_kode' => '1.02.02.02.02.0032', 'nomenklatur' => 'Operasional Pelayanan Rumah Sakit'],
            ['nomor_kode' => '1.02.02.02.02.0033', 'nomenklatur' => 'Operasional Pelayanan Puskesmas'],
            ['nomor_kode' => '1.02.02.02.02.0034', 'nomenklatur' => 'Operasional Pelayanan Fasilitas Kesehatan Lainnya'],
            ['nomor_kode' => '1.02.02.02.02.0035', 'nomenklatur' => 'Pelaksanaan Akreditasi Fasilitas Kesehatan di Kabupaten/Kota'],
            ['nomor_kode' => '1.02.02.02.02.0036', 'nomenklatur' => 'Investigasi Awal Kejadian Tidak Diharapkan (Kejadian Ikutan Pasca Imunisasi dan Pemberian Obat Massal)'],
            ['nomor_kode' => '1.02.02.02.02.0037', 'nomenklatur' => 'Pelaksanaan Kewaspadaan Dini dan Respon Wabah'],
            ['nomor_kode' => '1.02.02.02.02.0038', 'nomenklatur' => 'Penyediaan dan Pengelolaan Sistem Penanganan Gawat Darurat Terpadu (SPGDT)'],
            ['nomor_kode' => '1.02.02.02.02.0040', 'nomenklatur' => 'Pengelolaan pelayanan kesehatan orang dengan Tuberkulosis'],
            ['nomor_kode' => '1.02.02.02.02.0041', 'nomenklatur' => 'Pengelolaan pelayanan kesehatan orang dengan HIV (ODHIV)'],
            ['nomor_kode' => '1.02.02.02.02.0042', 'nomenklatur' => 'Pengelolaan pelayanan kesehatan Malaria'],
            ['nomor_kode' => '1.02.02.02.02.0043', 'nomenklatur' => 'Pengelolaan Kawasan tanpa rokok'],
            ['nomor_kode' => '1.02.02.02.02.0044', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Reproduksi'],
            ['nomor_kode' => '1.02.02.02.02.0045', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penerapan SPM Bidang Kesehatan Kabupaten/Kota'],
            ['nomor_kode' => '1.02.02.02.02.0046', 'nomenklatur' => 'Pengelolaan upaya kesehatan Ibu dan Anak'],
            ['nomor_kode' => '1.02.02.02.02.0047', 'nomenklatur' => 'Pengelolaan Pelayanan Kelanjutusiaan'],
            ['nomor_kode' => '1.02.02.02.02.0048', 'nomenklatur' => 'Pengelolaan Layanan Imunisasi'],
            ['nomor_kode' => '1.02.02.02.02.0050', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Haji'],
            ['nomor_kode' => '1.02.02.02.03.0002', 'nomenklatur' => 'Pengelolaan Sistem Informasi Kesehatan'],
            ['nomor_kode' => '1.02.02.02.04.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Pengawasan Perizinan Rumah Sakit Kelas C, D dan Fasilitas Pelayanan Kesehatan Lainnya'],
            ['nomor_kode' => '1.02.02.02.04.0002', 'nomenklatur' => 'Peningkatan Tata Kelola Rumah Sakit dan Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.02.02.02.04.0003', 'nomenklatur' => 'Peningkatan Mutu Pelayanan Fasilitas Kesehatan'],
            ['nomor_kode' => '1.02.02.02.04.0004', 'nomenklatur' => 'Penyiapan Perumusan dan Pelaksanaan Pelayanan Kesehatan Rujukan'],
            
             // PROGRAM PENINGKATAN KAPASITAS SUMBER DAYA MANUSIA KESEHATAN (lanjutan)
             ['nomor_kode' => '1.02.03.02.01.0001', 'nomenklatur' => 'Pengendalian Perizinan Praktik Tenaga Kesehatan'],
             ['nomor_kode' => '1.02.03.02.01.0002', 'nomenklatur' => 'Pembinaan dan Pengawasan Tenaga Kesehatan serta Tindak Lanjut Perizinan Praktik Tenaga Kesehatan'],
             ['nomor_kode' => '1.02.03.02.02.0001', 'nomenklatur' => 'Perencanaan dan Distribusi serta Pemerataan Sumber Daya Manusia Kesehatan'],
             ['nomor_kode' => '1.02.03.02.02.0002', 'nomenklatur' => 'Pemenuhan Kebutuhan Sumber Daya Manusia Kesehatan Sesuai Standar'],
             ['nomor_kode' => '1.02.03.02.02.0003', 'nomenklatur' => 'Pembinaan dan Pengawasan Sumber Daya Manusia Kesehatan'],
             ['nomor_kode' => '1.02.03.02.03.0001', 'nomenklatur' => 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota'],
 
             // PROGRAM SEDIAAN FARMASI, ALAT KESEHATAN DAN MAKANAN MINUMAN
             ['nomor_kode' => '1.02.04.02.01.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Pengawasan Perizinan Apotek, Toko Obat, Toko Alat Kesehatan, dan Optikal, Usaha Mikro Obat Tradisional (UMOT)'],
             ['nomor_kode' => '1.02.04.02.03.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Pengawasan Sertifikat Produksi Pangan Industri Rumah Tangga dan Nomor P-IRT sebagai Izin Produksi, untuk Produk Makanan Minuman Tertentu yang Dapat Diproduksi oleh Industri Rumah Tangga'],
             ['nomor_kode' => '1.02.04.02.04.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Pengawasan Penerbitan Sertifikat Laik Higiene Sanitasi Tempat Pengelolaan Makanan (TPM) antara lain Jasa Boga, Rumah Makan/Restoran dan Depot Air Minum (DAM)'],
             ['nomor_kode' => '1.02.04.02.05.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Penerbitan Stiker Pembinaan pada Makanan Jajanan dan Sentra Makanan Jajanan'],
             ['nomor_kode' => '1.02.04.02.06.0001', 'nomenklatur' => 'Pemeriksaan Post Market pada Produk Makanan-Minuman Industri Rumah Tangga yang Beredar dan Pengawasan serta Tindak Lanjut Pengawasan'],
 
             // PROGRAM PEMBERDAYAAN MASYARAKAT BIDANG KESEHATAN
             ['nomor_kode' => '1.02.05.02.01.0001', 'nomenklatur' => 'Peningkatan Upaya Promosi Kesehatan, Advokasi, Kemitraan dan Pemberdayaan Masyarakat'],
             ['nomor_kode' => '1.02.05.02.02.0001', 'nomenklatur' => 'Penyelenggaraan Promosi Kesehatan dan Gerakan Hidup Bersih dan Sehat'],
             ['nomor_kode' => '1.02.05.02.02.0002', 'nomenklatur' => 'Penumbuhan Kesadaran Keluarga dalam Peningkatan Derajat Kesehatan Keluarga dan Lingkungan dengan Menerapkan Perilaku Hidup Bersih dan Sehat'],
             ['nomor_kode' => '1.02.05.02.03.0001', 'nomenklatur' => 'Bimbingan Teknis dan Supervisi Pengembangan dan Pelaksanaan Upaya Kesehatan Bersumber Daya Masyarakat (UKBM)'],
 
             // URUSAN PEKERJAAN UMUM DAN PENATAAN RUANG (1.03)
             // PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA (DINAS PUPR)
             ['nomor_kode' => '1.03.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
             ['nomor_kode' => '1.03.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
             ['nomor_kode' => '1.03.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
             ['nomor_kode' => '1.03.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
             ['nomor_kode' => '1.03.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
             ['nomor_kode' => '1.03.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
             ['nomor_kode' => '1.03.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
             ['nomor_kode' => '1.03.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
             ['nomor_kode' => '1.03.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
             ['nomor_kode' => '1.03.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
             ['nomor_kode' => '1.03.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
             ['nomor_kode' => '1.03.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
             ['nomor_kode' => '1.03.01.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
             ['nomor_kode' => '1.03.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
             ['nomor_kode' => '1.03.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
             ['nomor_kode' => '1.03.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
             ['nomor_kode' => '1.03.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
             ['nomor_kode' => '1.03.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
             ['nomor_kode' => '1.03.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
             ['nomor_kode' => '1.03.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
             ['nomor_kode' => '1.03.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
             ['nomor_kode' => '1.03.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
             ['nomor_kode' => '1.03.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
             ['nomor_kode' => '1.03.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
             ['nomor_kode' => '1.03.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
             ['nomor_kode' => '1.03.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
             ['nomor_kode' => '1.03.01.02.07.0003', 'nomenklatur' => 'Pengadaan Alat Besar'],
             ['nomor_kode' => '1.03.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
             ['nomor_kode' => '1.03.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
             ['nomor_kode' => '1.03.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
             ['nomor_kode' => '1.03.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
             ['nomor_kode' => '1.03.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
             ['nomor_kode' => '1.03.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
             ['nomor_kode' => '1.03.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
             ['nomor_kode' => '1.03.01.02.09.0003', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan dan Perizinan Alat Besar'],
             ['nomor_kode' => '1.03.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
             ['nomor_kode' => '1.03.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
             ['nomor_kode' => '1.03.01.02.09.0007', 'nomenklatur' => 'Pemeliharaan Aset Tetap Lainnya'],
             ['nomor_kode' => '1.03.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
 
             // PROGRAM PENGELOLAAN SUMBER DAYA AIR (SDA)
             ['nomor_kode' => '1.03.02.02.01.0026', 'nomenklatur' => 'Rehabilitasi Pintu Air/Bendung Pengendali Banjir'],
             ['nomor_kode' => '1.03.02.02.01.0072', 'nomenklatur' => 'Evaluasi dan Rekomendasi Teknis (Rekomtek) Pemanfaatan Sumber Daya Air Wilayah Sungai Kewenangan Kabupaten/Kota'],
             ['nomor_kode' => '1.03.02.02.01.0075', 'nomenklatur' => 'Pembinaan dan Pemberdayaan Kelembagaan Pengelolaan SDA Kewenangan Kabupaten/Kota'],
             ['nomor_kode' => '1.03.02.02.01.0080', 'nomenklatur' => 'Operasi dan Pemeliharaan Tanggul dan Tebing Sungai'],
             ['nomor_kode' => '1.03.02.02.01.0091', 'nomenklatur' => 'Operasi dan Pemeliharaan Bendungan'],
             ['nomor_kode' => '1.03.02.02.01.0093', 'nomenklatur' => 'Normalisasi/Restorasi Sungai'],
             ['nomor_kode' => '1.03.02.02.01.0105', 'nomenklatur' => 'Rehabilitasi Seawall dan Bangunan Pengaman Pantai Lainnya'],
             ['nomor_kode' => '1.03.02.02.01.0106', 'nomenklatur' => 'Rehabilitasi Breakwater'],
             ['nomor_kode' => '1.03.02.02.01.0109', 'nomenklatur' => 'Pembangunan Bangunan Perkuatan Tebing'],
             ['nomor_kode' => '1.03.02.02.01.0112', 'nomenklatur' => 'Rehabilitasi Bendungan'],
             ['nomor_kode' => '1.03.02.02.01.0115', 'nomenklatur' => 'Pembangunan Tanggul Sungai'],
             ['nomor_kode' => '1.03.02.02.01.0117', 'nomenklatur' => 'Pembangunan Seawall dan Bangunan Pengaman Pantai Lainnya'],
             ['nomor_kode' => '1.03.02.02.01.0118', 'nomenklatur' => 'Penyusunan Pola dan Rencana Pengelolaan SDA WS Kewenangan Kabupaten/Kota'],
             ['nomor_kode' => '1.03.02.02.01.0124', 'nomenklatur' => 'Pembangunan Polder/Kolam Retensi'],
             ['nomor_kode' => '1.03.02.02.02.0014', 'nomenklatur' => 'Rehabilitasi Jaringan Irigasi Permukaan'],
             ['nomor_kode' => '1.03.02.02.02.0021', 'nomenklatur' => 'Operasi dan Pemeliharaan Jaringan Irigasi Permukaan'],
 
             // PROGRAM PENGELOLAAN DAN PENGEMBANGAN SISTEM PENYEDIAAN AIR MINUM
             ['nomor_kode' => '1.03.03.02.01.0022', 'nomenklatur' => 'Pembangunan Sistem Penyediaan Air Minum (SPAM) Bukan Jaringan Perpipaan'],
             ['nomor_kode' => '1.03.03.02.01.0025', 'nomenklatur' => 'Penyusunan Rencana, Kebijakan, Strategi dan Teknis Sistem Penyediaan Air Minum (SPAM)'],
             ['nomor_kode' => '1.03.03.02.01.0026', 'nomenklatur' => 'Peningkatan Sistem Penyediaan Air Minum (SPAM) Jaringan Perpipaan'],
 
             // PROGRAM PENGELOLAAN DAN PENGEMBANGAN SISTEM AIR LIMBAH
             ['nomor_kode' => '1.03.05.02.01.0025', 'nomenklatur' => 'Pembangunan Sistem Pengelolaan Air Limbah Domestik (SPALD) Terpusat Skala Perkotaan'],
             ['nomor_kode' => '1.03.05.02.01.0032', 'nomenklatur' => 'Peningkatan Sistem Pengelolaan Air Limbah Domestik (SPALD) Terpusat Skala Permukiman'],
             ['nomor_kode' => '1.03.05.02.01.0033', 'nomenklatur' => 'Penyediaan Jasa Penyedotan Lumpur Tinja'],
             ['nomor_kode' => '1.03.05.02.01.0039', 'nomenklatur' => 'Penyediaan Sub Sistem Pengolahan Air Limbah Domestik (SPALD) Setempat'],
             ['nomor_kode' => '1.03.05.02.01.0041', 'nomenklatur' => 'Penyusunan Rencana, Kebijakan, Strategi dan Teknis Sistem Pengelolaan Air Limbah Domestik (SPALD)'],
 
             // PROGRAM PENGELOLAAN DAN PENGEMBANGAN SISTEM DRAINASE
             ['nomor_kode' => '1.03.06.02.01.0024', 'nomenklatur' => 'Peningkatan Sistem Drainase Perkotaan'],
             ['nomor_kode' => '1.03.06.02.01.0028', 'nomenklatur' => 'Rehabilitasi Sistem Drainase Perkotaan'],
             ['nomor_kode' => '1.03.06.02.01.0029', 'nomenklatur' => 'Pembangunan Sistem Drainase Perkotaan'],
             ['nomor_kode' => '1.03.06.02.01.0030', 'nomenklatur' => 'Penyusunan Rencana, Kebijakan, Strategi dan Teknis Sistem Drainase Perkotaan'],
             ['nomor_kode' => '1.03.06.02.01.0031', 'nomenklatur' => 'Operasi dan Pemeliharaan Sistem Drainase Perkotaan'],
             
             // PROGRAM PENGEMBANGAN PERMUKIMAN
             ['nomor_kode' => '1.03.07.02.01.0021', 'nomenklatur' => 'Pembangunan Sistem Penyediaan Air Minum (SPAM) Jaringan Perpipaan di Kawasan Strategis Kabupaten/Kota'],
 
             // PROGRAM PENATAAN BANGUNAN GEDUNG
             ['nomor_kode' => '1.03.08.02.01.0021', 'nomenklatur' => 'Pembangunan, Pemanfaatan, Pelestariaan dan Pembongkaran Bangunan Gedung untuk Kepentingan Strategis Daerah Kabupaten/Kota'],
             ['nomor_kode' => '1.03.08.02.01.0023', 'nomenklatur' => 'Penyelenggaraan Penerbitan Persetujuan Bangunan Gedung (PBG), Sertifikat Laik Fungsi (SLF), Surat Bukti Kepemilikan Bangunan Gedung (SBKBG), Rencana Teknis Pembongkaran Bangunan Gedung (RTB), Tim Profesi Ahli (TPA), Tim Penilai Teknis (TPT), Penilik, dan Pendataan Bangunan Gedung melalui SIMBG'],
 
             // PROGRAM PENATAAN BANGUNAN DAN LINGKUNGANNYA
             ['nomor_kode' => '1.03.09.02.01.0008', 'nomenklatur' => 'Penataan Bangunan dan Lingkungan Kawasan Cagar Budaya, Kawasan Pariwisata, Kawasan Sistem Perkotaan Nasional dan Kawasan Strategis Lainnya'],
 
             // PROGRAM PENYELENGGARAAN JALAN
             ['nomor_kode' => '1.03.10.02.01.0032', 'nomenklatur' => 'Pembangunan Jalan'],
             ['nomor_kode' => '1.03.10.02.01.0033', 'nomenklatur' => 'Rekonstruksi Jalan'],
             ['nomor_kode' => '1.03.10.02.01.0034', 'nomenklatur' => 'Pemeliharaan Berkala Jalan'],
             ['nomor_kode' => '1.03.10.02.01.0038', 'nomenklatur' => 'Pemeliharaan Rutin Jembatan'],
             ['nomor_kode' => '1.03.10.02.01.0040', 'nomenklatur' => 'Pembangunan Jembatan'],
             ['nomor_kode' => '1.03.10.02.01.0044', 'nomenklatur' => 'Rehabilitasi Jalan'],
             ['nomor_kode' => '1.03.10.02.01.0046', 'nomenklatur' => 'Pemeliharaan Rutin Jalan'],
 
             // PROGRAM PENGEMBANGAN JASA KONSTRUKSI
             ['nomor_kode' => '1.03.11.02.01.0010', 'nomenklatur' => 'Fasilitasi Sertifikasi Tenaga Kerja Konstruksi Kualifikasi Jabatan Operator dan Teknisi atau Analis'],
             ['nomor_kode' => '1.03.11.02.02.0012', 'nomenklatur' => 'Penyediaan Perangkat Pendukung Layanan Informasi Jasa Konstruksi'],
 
             // PROGRAM PENYELENGGARAAN PENATAAN RUANG
             ['nomor_kode' => '1.03.12.02.01.0013', 'nomenklatur' => 'Sosialisasi Kebijakan dan Peraturan Perundang-Undangan Bidang Penataan Ruang'],
             ['nomor_kode' => '1.03.12.02.02.0005', 'nomenklatur' => 'Penyusunan RDTR Kabupaten/Kota'],
             ['nomor_kode' => '1.03.12.02.03.0004', 'nomenklatur' => 'Pelaksanaan Persetujuan Kesesuaian Kegiatan Pemanfaatan Ruang'],
             ['nomor_kode' => '1.03.12.02.03.0007', 'nomenklatur' => 'Sistem informasi dan komunikasi penataan ruang'],
             ['nomor_kode' => '1.03.12.02.04.0004', 'nomenklatur' => 'Koordinasi Pelaksanaan Penataan Ruang'],
             ['nomor_kode' => '1.03.12.02.04.0008', 'nomenklatur' => 'Penilaian Pelaksanaan Kesesuaian Kegiatan Pemanfaatan Ruang dan/atau pernyataan mandiri pelaku UMK'],

             // URUSAN PERUMAHAN DAN KAWASAN PERMUKIMAN (1.04)
            // PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA (DINAS PERKIM)
            ['nomor_kode' => '1.04.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.04.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.04.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.04.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.04.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.04.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.04.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.04.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.04.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.04.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.04.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.04.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.04.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.04.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.04.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.04.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.04.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.04.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.04.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.04.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.04.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.04.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.04.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '1.04.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.04.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.04.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '1.04.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.04.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.04.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '1.04.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.04.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.04.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.04.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.04.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // PROGRAM PENGEMBANGAN PERUMAHAN
            ['nomor_kode' => '1.04.02.02.01.0002', 'nomenklatur' => 'Identifikasi Lahan-Lahan Potensial sebagai Lokasi Relokasi Perumahan'],
            ['nomor_kode' => '1.04.02.02.01.0003', 'nomenklatur' => 'Pengumpulan Data Rumah Korban Bencana Kejadian Sebelumnya yang Belum Tertangani'],
            ['nomor_kode' => '1.04.02.02.01.0004', 'nomenklatur' => 'Pendataan Tingkat Kerusakan Rumah Akibat Bencana'],
            ['nomor_kode' => '1.04.02.02.01.0009', 'nomenklatur' => 'Identifikasi Perumahan di Lokasi Rawan Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.02.02.01.0010', 'nomenklatur' => 'Pendataan dan Verifikasi Calon Penerima Rumah bagi Korban Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.02.02.02.0001', 'nomenklatur' => 'Sosialisasi Standar Teknis Penyediaan dan Rehabilitasi Rumah kepada Masyarakat/Sukarelawan Tanggap Bencana'],
            ['nomor_kode' => '1.04.02.02.02.0008', 'nomenklatur' => 'Rembug Warga untuk Menentukan Calon Penerima Rumah bagi Korban Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.02.02.02.0009', 'nomenklatur' => 'Sosialisasi Pengembangan Perumahan Baru dan Mekanisme Akses Pembiayaan Perumahan'],
            ['nomor_kode' => '1.04.02.02.02.0010', 'nomenklatur' => 'Sosialisasi tentang Mekanisme Penggantian Hak atas Tanah dan/atau Bangunan'],
            ['nomor_kode' => '1.04.02.02.03.0001', 'nomenklatur' => 'Rehabilitasi Rumah bagi Korban Bencana'],
            ['nomor_kode' => '1.04.02.02.03.0004', 'nomenklatur' => 'Pembangunan Rumah bagi Korban Bencana'],
            ['nomor_kode' => '1.04.02.02.04.0005', 'nomenklatur' => 'Penatausahaan Serah Terima Rumah bagi Korban Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.02.02.04.0006', 'nomenklatur' => 'Pelaksanaan Pembagian Rumah bagi Korban Bencana Kabupaten/Kota'],

            // PROGRAM KAWASAN PERMUKIMAN
            ['nomor_kode' => '1.04.03.02.01.0004', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penyelenggaraan Kawasan Permukiman'],
            ['nomor_kode' => '1.04.03.02.03.0002', 'nomenklatur' => 'Perbaikan Rumah Tidak Layak Huni'],
            ['nomor_kode' => '1.04.03.02.03.0004', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pengendalian Penyelenggaraan Pemugaran/Peremajaan Permukiman Kumuh'],
            ['nomor_kode' => '1.04.03.02.03.0007', 'nomenklatur' => 'Pendataan dan Verifikasi Penyelenggaraan Kawasan Permukiman Kumuh'],
            ['nomor_kode' => '1.04.03.02.03.0009', 'nomenklatur' => 'Pelaksanaan Peremajaan Kawasan Permukiman Kumuh'],
            ['nomor_kode' => '1.04.03.02.03.0012', 'nomenklatur' => 'Pembangunan Rumah Baru Layak Huni untuk Peningkatan Kualitas Permukiman Kumuh dengan Luas di Bawah 10 (Sepuluh) Ha'],
            
            // PROGRAM PENINGKATAN PRASARANA, SARANA DAN UTILITAS UMUM (PSU)
            ['nomor_kode' => '1.04.05.02.01.0001', 'nomenklatur' => 'Perencanaan Penyediaan PSU Perumahan'],
            ['nomor_kode' => '1.04.05.02.01.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi dalam rangka Penyediaan Prasarana, Sarana, dan Utilitas Umum Perumahan'],
            ['nomor_kode' => '1.04.05.02.01.0007', 'nomenklatur' => 'Perbaikan Prasarana, Sarana, dan Utilitas Umum di Perumahan untuk Menunjang Fungsi Hunian'],
            ['nomor_kode' => '1.04.05.02.01.0010', 'nomenklatur' => 'Verifikasi dan Penyerahan PSU Perumahan dari Pengembang'],

            // URUSAN KETENTERAMAN DAN KETERTIBAN UMUM SERTA PERLINDUNGAN MASYARAKAT (1.05)
            // PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA (SATPOL PP & DAMKAR)
            ['nomor_kode' => '1.05.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.05.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.05.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.05.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.05.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.05.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.05.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.05.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.05.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.05.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.05.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.05.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.05.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.05.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.05.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '1.05.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '1.05.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.05.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.05.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.05.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.05.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '1.05.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.05.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.05.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.05.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.05.01.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '1.05.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.05.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.05.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '1.05.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.05.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.05.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.05.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.05.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // PROGRAM PENINGKATAN KETENTERAMAN DAN KETERTIBAN UMUM
            ['nomor_kode' => '1.05.02.02.01.0003', 'nomenklatur' => 'Koordinasi Penyelenggaraan Ketentraman dan Ketertiban Umum serta Perlindungan Masyarakat Tingkat Kabupaten/Kota'],
            ['nomor_kode' => '1.05.02.02.01.0004', 'nomenklatur' => 'Pemberdayaan Perlindungan Masyarakat dalam rangka Ketentraman dan Ketertiban Umum'],
            ['nomor_kode' => '1.05.02.02.01.0005', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Polisi Pamongpraja dan Satuan Perlindungan Masyarakat Termasuk dalam Pelaksanaan Tugas yang Bernuansa Hak Asasi Manusia'],
            ['nomor_kode' => '1.05.02.02.01.0006', 'nomenklatur' => 'Kerja Sama antar Lembaga dan Kemitraan dalam Teknik Pencegahan dan Penanganan Gangguan Ketentraman dan Ketertiban Umum'],
            ['nomor_kode' => '1.05.02.02.01.0008', 'nomenklatur' => 'Penyusunan SOP Ketertiban Umum dan Ketenteraman Masyarakat'],
            ['nomor_kode' => '1.05.02.02.01.0010', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Polisi Pamong Praja melalui Pendidikan dan Pelatihan Dasar Pol PPngsional Pol PP dan Uji Kompetensi bagi Pejabat Fungsional'],
            ['nomor_kode' => '1.05.02.02.01.0011', 'nomenklatur' => 'Pembentukan Tim Penilai angka kredit dan Sekretariat Pengelolaan Jabatan Fungsional Pol PP'],
            ['nomor_kode' => '1.05.02.02.01.0012', 'nomenklatur' => 'Peningkatan Kapasitas SDM Pol PP melalui Uji Kompetensi untuk usulan perpindahan jabatan ke jabatan fungsional Pol PP, Promosi dan kenaikan jenjang jabatan'],
            ['nomor_kode' => '1.05.02.02.01.0013', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Pelindungan Masyarakat'],
            ['nomor_kode' => '1.05.02.02.01.0014', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Polisi Pamong Praja dan Satlinmas melalui Pelatihan Teknis Satpol PP dan Satlinmas'],
            ['nomor_kode' => '1.05.02.02.01.0015', 'nomenklatur' => 'Pencegahan Gangguan Ketenteraman dan Ketertiban Umum Melalui Deteksi Dini dan Cegah Dini, Pembinaan dan Penyuluhan, Pelaksanaan Patroli, Pengamanan, dan Pengawalan'],
            ['nomor_kode' => '1.05.02.02.01.0016', 'nomenklatur' => 'Penindakan Atas Gangguan Ketenteraman dan Ketertiban Umum berdasarkan Perda dan Perkada Melalui Penertiban dan Penanganan Unjuk Rasa dan Kerusuhan Massa'],
            ['nomor_kode' => '1.05.02.02.01.0017', 'nomenklatur' => 'Penyediaan Layanan dasar dalam rangka Dampak Penegakan Peraturan Daerah dan Perturan kepala daerah'],
            ['nomor_kode' => '1.05.02.02.01.0018', 'nomenklatur' => 'Pengadaan dan Pemeliharaan Sarana dan Prasarana Ketentraman dan Ketertiban Umum'],
            ['nomor_kode' => '1.05.02.02.02.0004', 'nomenklatur' => 'Pembinaan dan Penyuluhan terhadap Pelanggar Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.02.02.02.0005', 'nomenklatur' => 'Penyusunan SOP Penegakan Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.02.02.02.0006', 'nomenklatur' => 'Pengadaan dan Pemeliharaan Sarana dan Prasarana Penegakan Peraturan Daerah (Ruang Pemeriksanaan, Gelar Perkara, dan Ruang Penyimpanan Barang Bukti)'],
            ['nomor_kode' => '1.05.02.02.02.0007', 'nomenklatur' => 'Penyelidikan terhadap dugaan Pelanggaran Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.02.02.02.0008', 'nomenklatur' => 'Dukungan Pelaksanaan Sidang atas Pelanggaran Peraturan Daerah'],
            ['nomor_kode' => '1.05.02.02.02.0009', 'nomenklatur' => 'Pemberkasan Administrasi Penyidikan oleh PPNS Penegak Peraturan Daerah'],
            ['nomor_kode' => '1.05.02.02.02.0010', 'nomenklatur' => 'Sosialisasi Penegakan Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.02.02.02.0011', 'nomenklatur' => 'Penanganan Atas Pelanggaran Peraturan Daerah dan Peraturan Kepala daerah'],
            ['nomor_kode' => '1.05.02.02.02.0012', 'nomenklatur' => 'Pengawasan Atas Kepatuhan Terhadap Pelaksanaan Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.02.02.03.0002', 'nomenklatur' => 'Pembentukan Sekretariat PPNS'],
            ['nomor_kode' => '1.05.02.02.03.0003', 'nomenklatur' => 'Kerja Sama Antar Lembaga dan Kemitraan dalam Pelaksanaan Penegakan Peraturan Daerah'],
            ['nomor_kode' => '1.05.02.02.03.0004', 'nomenklatur' => 'Pembentukan PPNS Penegak Peraturan Daerah'],
            ['nomor_kode' => '1.05.02.02.03.0005', 'nomenklatur' => 'Dukungan Operasional Sekretariat PPNS'],
            ['nomor_kode' => '1.05.02.02.03.0006', 'nomenklatur' => 'Pengembangan Kapasitas dan Karier PPNS'],

            // PROGRAM PENANGGULANGAN BENCANA
            ['nomor_kode' => '1.05.03.02.01.0007', 'nomenklatur' => 'Sosialisasi, Komunikasi, Informasi dan Edukasi (KIE) Rawan Bencana Kabupaten/Kota (Per Jenis Ancaman Bencana)'],
            ['nomor_kode' => '1.05.03.02.02.0015', 'nomenklatur' => 'Penyediaan Peralatan Perlindungan dan Kesiapsiagaan Terhadap Bencana kabupaten/kota'],
            ['nomor_kode' => '1.05.03.02.02.0018', 'nomenklatur' => 'Gladi Kesiapsiagaan Terhadap Bencana kabupaten/kota'],
            ['nomor_kode' => '1.05.03.02.02.0021', 'nomenklatur' => 'Pengembangan Kapasitas Tim Reaksi Cepat (TRC) Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.03.02.02.0028', 'nomenklatur' => 'Pelatihan Pencegahan dan Mitigasi Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.03.02.03.0001', 'nomenklatur' => 'Penyusunan Regulasi Penanggulangan Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.03.02.03.0010', 'nomenklatur' => 'Koordinasi penanganan Pascabencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.03.02.03.0014', 'nomenklatur' => 'Penguatan Kelembagaan Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.03.02.03.0015', 'nomenklatur' => 'Penyusunan Kajian Kebutuhan Pascabencana (JITUPASNA) dan Rencana Rehabilitasi dan Rekontruksi Pascabencana (R3P) Kab/Kota'],
            ['nomor_kode' => '1.05.03.02.03.0016', 'nomenklatur' => 'Penyusunan Rencana Aksi Penerapan Standar Pelayanan Minimal (SPM) Sub Urusan Bencana Kabupaten/Kota'],

            // PROGRAM PENCEGAHAN, PENANGGULANGAN, PENYELAMATAN KEBAKARAN
            ['nomor_kode' => '1.05.04.02.01.0001', 'nomenklatur' => 'Pencegahan Kebakaran dalam Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.05.04.02.01.0002', 'nomenklatur' => 'Pemadaman dan Pengendalian Kebakaran dalam Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.05.04.02.01.0017', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pencegahan, Penanggulangan Kebakaran dan Alat Pelindung Diri'],
            ['nomor_kode' => '1.05.04.02.02.0001', 'nomenklatur' => 'Pendataan Sarana Prasarana Proteksi Kebakaran'],
            ['nomor_kode' => '1.05.04.02.04.0001', 'nomenklatur' => 'Pemberdayaan Masyarakat dalam Pencegahan dan Penanggulangan Kebakaran Melalui Sosialisasi dan Edukasi Masyarakat'],
            ['nomor_kode' => '1.05.04.02.04.0003', 'nomenklatur' => 'Dukungan Pemberdayaan Masyarakat/Relawan Pemadam Kebakaran Melalui Penyediaan Sarana dan PraSarana'],
            ['nomor_kode' => '1.05.04.02.05.0001', 'nomenklatur' => 'Penyelenggaraan Operasi Pencarian dan Pertolongan pada Peristiwa yang Menimpa, Membahayakan, dan/atau Mengancam Keselamatan Manusia'],
            ['nomor_kode' => '1.05.04.02.05.0005', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pencarian dan Pertolongan Terhadap Kondisi Membahayakan Manusia/Penyelamatan dan Evakuasi'],

            // URUSAN SOSIAL (1.06)
            // PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA (DINAS SOSIAL)
            ['nomor_kode' => '1.06.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.06.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.06.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.06.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.06.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.06.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.06.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.06.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.06.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.06.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.06.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.06.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.06.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '1.06.01.02.05.0003', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.06.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.06.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.06.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '1.06.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.06.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '1.06.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.06.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.06.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.06.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.06.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.06.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '1.06.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.06.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.06.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '1.06.01.02.07.0010', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '1.06.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.06.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.06.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '1.06.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.06.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.06.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.06.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '1.06.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // PROGRAM PEMBERDAYAAN SOSIAL
            ['nomor_kode' => '1.06.02.02.02.0001', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penerbitan Izin Undian Gratis Berhadiah dan Pengumpulan Uang atau Barang'],
            ['nomor_kode' => '1.06.02.02.03.0001', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Pekerja Sosial Masyarakat Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.02.02.03.0002', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Tenaga Kesejahteraan Sosial Kecamatan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.02.02.03.0003', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Sumber Kesejahteraan Sosial Keluarga Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.02.02.03.0004', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Sumber Kesejahteraan Sosial Kelembagaan Masyarakat Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.02.02.03.0005', 'nomenklatur' => 'Peningkatan Kemampuan Sumber Daya Manusia dan Penguatan Lembaga Konsultasi Kesejahteraan Keluarga (LK3)'],

            // PROGRAM PENANGANAN WARGA NEGARA MIGRAN KORBAN TINDAK KEKERASAN
            ['nomor_kode' => '1.06.03.02.01.0001', 'nomenklatur' => 'Fasilitasi Pemulangan Warga Negara Migran Korban Tindak Kekerasan dari Titik Debarkasi di Daerah Kabupaten/Kota untuk dipulangkan ke Desa/Kelurahan Asal'],

            // PROGRAM REHABILITASI SOSIAL
            ['nomor_kode' => '1.06.04.02.01.0001', 'nomenklatur' => 'Penyediaan Permakanan'],
            ['nomor_kode' => '1.06.04.02.01.0003', 'nomenklatur' => 'Penyediaan Alat Bantu'],
            ['nomor_kode' => '1.06.04.02.01.0005', 'nomenklatur' => 'Pemberian Bimbingan Fisik, Mental, Spiritual, dan Sosial'],
            ['nomor_kode' => '1.06.04.02.01.0006', 'nomenklatur' => 'Pemberian Bimbingan Sosial kepada Keluarga Penyandang Disabilitas Terlantar, Anak Terlantar, Lanjut Usia Terlantar, serta Gelandangan Pengemis dan Masyarakat'],
            ['nomor_kode' => '1.06.04.02.01.0009', 'nomenklatur' => 'Pemberian Layanan Data dan Pengaduan'],
            ['nomor_kode' => '1.06.04.02.02.0001', 'nomenklatur' => 'Pemberian Layanan Data dan Pengaduan'],
            ['nomor_kode' => '1.06.04.02.02.0002', 'nomenklatur' => 'Pemberian Layanan Kedaruratan'],
            ['nomor_kode' => '1.06.04.02.02.0007', 'nomenklatur' => 'Pemberian Bimbingan Fisik, Mental, Spiritual, dan Sosial'],
            ['nomor_kode' => '1.06.04.02.02.0008', 'nomenklatur' => 'Pemberian Bimbingan Sosial kepada Keluarga Penyandang Masalah Kesejahteraan Sosial (PMKS) Lainnya Bukan Korban HIV/AIDS dan NAPZA'],

            // PROGRAM PERLINDUNGAN DAN JAMINAN SOSIAL
            ['nomor_kode' => '1.06.05.02.01.0001', 'nomenklatur' => 'Penjangkauan Anak-Anak Terlantar'],
            ['nomor_kode' => '1.06.05.02.01.0002', 'nomenklatur' => 'Rujukan Anak-Anak Terlantar'],
            ['nomor_kode' => '1.06.05.02.01.0003', 'nomenklatur' => 'Pemantauan Terhadap Pelaksanaan Pemeliharaan Anak Terlantar'],
            ['nomor_kode' => '1.06.05.02.02.0001', 'nomenklatur' => 'Pendataan Fakir Miskin Cakupan Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.06.05.02.02.0002', 'nomenklatur' => 'Pengelolaan Data Fakir Miskin Cakupan Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.06.05.02.02.0003', 'nomenklatur' => 'Fasilitasi Bantuan Sosial Kesejahteraan Keluarga'],
            ['nomor_kode' => '1.06.05.02.02.0004', 'nomenklatur' => 'Fasilitasi Bantuan Pengembangan Ekonomi Masyarakat'],

            // PROGRAM PENANGANAN BENCANA
            ['nomor_kode' => '1.06.06.02.01.0001', 'nomenklatur' => 'Penyediaan Makanan'],
            ['nomor_kode' => '1.06.06.02.01.0002', 'nomenklatur' => 'Penyediaan Sandang'],
            ['nomor_kode' => '1.06.06.02.01.0003', 'nomenklatur' => 'Penyediaan Tempat Penampungan Pengungsi'],
            ['nomor_kode' => '1.06.06.02.01.0005', 'nomenklatur' => 'Pelayanan Dukungan Psikososial'],
            ['nomor_kode' => '1.06.06.02.02.0001', 'nomenklatur' => 'Koordinasi, Sosialisasi dan Pelaksanaan Kampung Siaga Bencana'],
            ['nomor_kode' => '1.06.06.02.02.0002', 'nomenklatur' => 'Koordinasi, Sosialisasi dan Pelaksanaan Taruna Siaga Bencana'],

            // PROGRAM PENGELOLAAN TAMAN MAKAM PAHLAWAN
            ['nomor_kode' => '1.06.07.02.01.0001', 'nomenklatur' => 'Rehabilitasi Sarana dan Prasarana Taman Makam Pahlawan Nasional Kabupaten/Kota'],
            ['nomor_kode' => '1.06.07.02.01.0002', 'nomenklatur' => 'Pemeliharaan Taman Makam Pahlawan Nasional Kabupaten/Kota'],

            ['nomor_kode' => '2.07.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.07.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.07.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.07.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.07.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.07.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.07.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.07.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.07.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.07.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.07.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.07.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.07.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '2.07.01.02.05.0003', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '2.07.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.07.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.07.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.07.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.07.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.07.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.07.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.07.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.07.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.07.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.07.01.02.07.0001', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.07.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.07.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.07.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.07.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.07.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.07.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.07.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.07.03.02.01.0001', 'nomenklatur' => 'Proses Pelaksanaan Pendidikan dan Pelatihan Keterampilan bagi Pencari Kerja berdasarkan Klaster Kompetensi'],
            ['nomor_kode' => '2.07.03.02.01.0002', 'nomenklatur' => 'Koordinasi Lintas Lembaga dan Kerja Sama dengan Sektor Swasta untuk Penyediaan Instruktur serta Sarana dan Prasarana Lembaga Pelatihan Kerja'],
            ['nomor_kode' => '2.07.03.02.02.0001', 'nomenklatur' => 'Pembinaan Lembaga Pelatihan Kerja Swasta'],
            ['nomor_kode' => '2.07.03.02.05.0001', 'nomenklatur' => 'Pengukuran Kompetensi dan Produktivitas Tenaga Kerja'],
            ['nomor_kode' => '2.07.04.02.01.0002', 'nomenklatur' => 'Pelayanan antar Kerja'],
            ['nomor_kode' => '2.07.04.02.01.0003', 'nomenklatur' => 'Penyuluhan dan Bimbingan Jabatan bagi Pencari Kerja'],
            ['nomor_kode' => '2.07.04.02.01.0005', 'nomenklatur' => 'Perluasan Kesempatan Kerja'],
            ['nomor_kode' => '2.07.04.02.03.0002', 'nomenklatur' => 'Pelayanan dan Penyediaan Informasi Pasar Kerja Online'],
            ['nomor_kode' => '2.07.04.02.03.0003', 'nomenklatur' => 'Job Fair/Bursa Kerja'],
            ['nomor_kode' => '2.07.04.02.04.0001', 'nomenklatur' => 'Peningkatan Pelindungan dan Kompetensi Calon Pekerja Migran Indonesia (PMI)/Pekerja Migran Indonesia (PMI)'],
            ['nomor_kode' => '2.07.04.02.04.0003', 'nomenklatur' => 'Pemberdayaan Pekerja Migran Indonesia Purna Penempatan'],
            ['nomor_kode' => '2.07.05.02.01.0001', 'nomenklatur' => 'Pengesahan Peraturan Perusahaan bagi Perusahaan'],
            ['nomor_kode' => '2.07.05.02.01.0002', 'nomenklatur' => 'Pendaftaran Perjanjian Kerja Sama bagi Perusahaan'],
            ['nomor_kode' => '2.07.05.02.01.0003', 'nomenklatur' => 'Penyelenggaraan Pendataan dan Informasi Sarana Hubungan Industrial dan Jaminan Sosial Tenaga Kerja serta Pengupahan'],
            ['nomor_kode' => '2.07.05.02.02.0001', 'nomenklatur' => 'Pencegahan Perselisihan Hubungan Industrial, Mogok Kerja, dan Penutupan Perusahaan yang Berakibat/Berdampak pada Kepentingan di 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.07.05.02.02.0002', 'nomenklatur' => 'Penyelesaian Perselisihan Hubungan Industrial, Mogok Kerja, dan Penutupan Perusahaan yang Berakibat/Berdampak pada Kepentingan di 1 (satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.07.05.02.02.0003', 'nomenklatur' => 'Penyelenggaraan Verifikasi dan Rekapitulasi Keanggotaan pada Organisasi Pengusaha, Federasi dan Konfederasi Serikat Pekerja/Serikat Buruh serta Non Afiliasi'],
            ['nomor_kode' => '2.07.05.02.02.0004', 'nomenklatur' => 'Pelaksanaan Operasional Lembaga Kerja Sama Tripartit Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.08.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.08.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.08.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.08.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.08.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.08.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.08.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.08.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.08.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.08.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.08.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.08.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.08.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.08.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.08.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.08.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.08.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.08.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.08.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.08.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.08.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.08.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.08.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.08.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.08.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.08.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.08.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.08.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.08.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.08.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.08.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.08.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.08.02.02.01.0001', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Perumusan Kebijakan Pelaksanaan PUG'],
            ['nomor_kode' => '2.08.02.02.01.0005', 'nomenklatur' => 'Penyusunan Kebijakan Penyelenggaraan PUG kewenangan kab/ kota'],
            ['nomor_kode' => '2.08.02.02.01.0006', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Penyelenggaraan PUG kewenangan Kab/Kota'],
            ['nomor_kode' => '2.08.02.02.01.0008', 'nomenklatur' => 'Sosialisasi kebijakan penyelenggaraan PUG kewenangan Kab/Kota'],
            ['nomor_kode' => '2.08.02.02.02.0001', 'nomenklatur' => 'Sosialisasi Peningkatan Partisipasi Perempuan di Bidang Politik, Hukum, Sosial dan Ekonomi'],
            ['nomor_kode' => '2.08.02.02.02.0002', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Peningkatan Partisipasi Perempuan dan Politik, Hukum, Sosial dan Ekonomi'],
            ['nomor_kode' => '2.08.02.02.03.0001', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan kepada Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.02.02.03.0002', 'nomenklatur' => 'Peningkatan Kapasitas Sumber Daya Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.02.02.03.0003', 'nomenklatur' => 'Pengembangan Komunikasi, Informasi dan Edukasi (KIE) Pemberdayaan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.01.0001', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pelaksanaan Kebijakan, Program dan Kegiatan Pencegahan Kekerasan Terhadap Perempuan Lingkup Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.01.0002', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Layanan Perlindungan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.02.0001', 'nomenklatur' => 'Penyediaan Layanan Pengaduan Masyarakat bagi Perempuan Korban Kekerasan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.02.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pelaksanaan Penyediaan Layanan Rujukan Lanjutan bagi Perempuan Korban Kekerasan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.03.0001', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Penyediaan Sarana Prasarana Layanan bagi Perempuan Korban Kekerasan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.03.0002', 'nomenklatur' => 'Peningkatan Kapasitas Sumber Daya Lembaga Penyedia Layanan Penanganan bagi Perempuan Korban Kekerasan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.03.0003', 'nomenklatur' => 'Penyediaan Kebutuhan Spesifik bagi Perempuan dalam Situasi Darurat dan Kondisi Khusus Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.03.02.03.0004', 'nomenklatur' => 'Penguatan Jejaring antar Lembaga Penyedia Layanan Perlindungan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.01.0001', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan untuk Mewujudkan KG dan Perlindungan Anak Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.01.0002', 'nomenklatur' => 'Pelaksanaan Komunikasi, Informasi dan Edukasi KG dan Perlindungan Anak bagi Keluarga Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.01.0003', 'nomenklatur' => 'Pengembangan Kegiatan Masyarakat untuk Peningkatan Kualitas Keluarga Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.02.0001', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Pengembangan Lembaga Penyedia Layanan Peningkatan Kualitas Keluarga Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.02.0002', 'nomenklatur' => 'Peningkatan Kapasitas Sumber Daya Lembaga Penyedia Layanan Peningkatan Kualitas Keluarga Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.02.0003', 'nomenklatur' => 'Penguatan Jejaring antar Lembaga Penyedia Layanan Peningkatan Kualitas Keluarga Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.04.02.03.0001', 'nomenklatur' => 'Pelaksanaan Penyediaan Layanan Komprehensif bagi Keluarga dalam Mewujudkan KG dan Perlindungan Anak yang Wilayah Kerjanya dalam Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.05.02.01.0001', 'nomenklatur' => 'Penyediaan Data Gender dan Anak di Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.05.02.01.0002', 'nomenklatur' => 'Penyajian dan Pemanfaatan Data Gender dan Anak dalam Kelembagaan Data di Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.06.02.01.0001', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Pemenuhan Hak Anak pada Lembaga Pemerintah, Non Pemerintah, Media dan Dunia Usaha Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.06.02.01.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pelembagaan Pemenuhan Hak Anak Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.06.02.02.0001', 'nomenklatur' => 'Penyediaan Layanan Peningkatan Kualitas Hidup Anak Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.06.02.02.0003', 'nomenklatur' => 'Pengembangan Komunikasi, Informasi dan Edukasi Pemenuhan Hak Anak bagi Lembaga Penyedia Layanan Peningkatan Kualitas Hidup Anak Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.08.07.02.01.0003', 'nomenklatur' => 'Penguatan kerja sama lintas perangkat daerah untuk mewujudkan kabupaten/kota layak Anak, kecamatan layak Anak, desa/kelurahan layak Anak, dan DRPPA'],
            ['nomor_kode' => '2.08.07.02.01.0004', 'nomenklatur' => 'Advokasi dan pendampingan Perangkat Daerah dalam pelaksanaan kebijakan /program/ kegiatan pencegahan KTA'],
            ['nomor_kode' => '2.08.07.02.01.0005', 'nomenklatur' => 'Penyusunan kebijakan perlindungan khusus anak kewenangan kab/ kota'],
            ['nomor_kode' => '2.08.07.02.02.0007', 'nomenklatur' => 'Koordinasi Pelaksanaan Layanan AMPK'],
            ['nomor_kode' => '2.08.07.02.03.0004', 'nomenklatur' => 'Pengembangan Lembaga Penyedia Layanan AMPK tingkat Kabupaten/kota'],
            ['nomor_kode' => '2.08.07.02.03.0005', 'nomenklatur' => 'Penguatan jejaring antar lembaga penyedia layanan perlindungan bagi AMPK tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.08.07.02.03.0006', 'nomenklatur' => 'Peningkatan kapasitas SDM lembaga penyedia layanan perlindungan dan penanganan bagi AMPK tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.08.07.02.03.0007', 'nomenklatur' => 'Pengembangan KIE (komunikasi, informasi, dan edukasi) perlindungan khusus anak tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.08.07.02.03.0008', 'nomenklatur' => 'Penyediaan Bantuan kebutuhan khusus bagi AMPK tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.09.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.09.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.09.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.09.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.09.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.09.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.09.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.09.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.09.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.09.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.09.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.09.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.09.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.09.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.09.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.09.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.09.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.09.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.09.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.09.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.09.01.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],
            ['nomor_kode' => '2.09.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.09.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.09.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.09.01.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.09.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.09.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.09.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.09.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.09.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.09.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.09.02.02.01.0003', 'nomenklatur' => 'Penyediaan Infrastruktur Pendukung Kemandirian Pangan Lainnya'],
            ['nomor_kode' => '2.09.02.02.01.0004', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penyediaan Infrastruktur Logistik'],
            ['nomor_kode' => '2.09.02.02.01.0006', 'nomenklatur' => 'Penyediaan Infrastruktur Cadangan Pangan Pemerintah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.03.02.01.0002', 'nomenklatur' => 'Penyediaan Pangan Berbasis Sumber Daya Lokal'],
            ['nomor_kode' => '2.09.03.02.01.0003', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Pelaksanaan Distribusi Pangan Pokok dan Pangan Lainnya'],
            ['nomor_kode' => '2.09.03.02.01.0006', 'nomenklatur' => 'Pengembangan Kelembagaan Usaha Pangan Masyarakat dan Toko Tani Indonesia'],
            ['nomor_kode' => '2.09.03.02.01.0007', 'nomenklatur' => 'Peningkatan Ketahanan Pangan Keluarga'],
            ['nomor_kode' => '2.09.03.02.01.0008', 'nomenklatur' => 'Stabilisasi Pasokan dan Harga Pangan Tingkat Produsen dan Konsumen di Kabupaten/Kota'],
            ['nomor_kode' => '2.09.03.02.01.0012', 'nomenklatur' => 'Penyediaan Informasi Harga Pangan Tingkat Produsen dan Konsumen Wilayah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.03.02.01.0013', 'nomenklatur' => 'Penyusunan Prognosa Neraca Pangan Wilayah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.03.02.01.0014', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pemantauan Stok, Pasokan dan Harga Pangan Pokok Strategis'],
            ['nomor_kode' => '2.09.03.02.01.0015', 'nomenklatur' => 'Pemantauan Harga dan Pasokan Pangan'],
            ['nomor_kode' => '2.09.03.02.01.0016', 'nomenklatur' => 'Penyusunan Neraca Bahan Makanan (NBM)'],
            ['nomor_kode' => '2.09.03.02.02.0003', 'nomenklatur' => 'Pengadaan Cadangan Pangan Pemerintah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.03.02.02.0006', 'nomenklatur' => 'Pengelolaan Cadangan Pangan Pemerintah Kab/Kota'],
            ['nomor_kode' => '2.09.03.02.04.0001', 'nomenklatur' => 'Penyusunan dan Penetapan Target Konsumsi Pangan Per Kapita Per Tahun'],
            ['nomor_kode' => '2.09.03.02.04.0002', 'nomenklatur' => 'Pemberdayaan Masyarakat dalam Penganekaragaman Konsumsi Pangan Berbasis Sumber Daya Lokal'],
            ['nomor_kode' => '2.09.03.02.04.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pemantauan dan Evaluasi Konsumsi per Kapita per Tahun'],
            ['nomor_kode' => '2.09.04.02.01.0001', 'nomenklatur' => 'Penyusunan, Pemutakhiran dan Analisis Peta Ketahanan dan Kerentanan Pangan'],
            ['nomor_kode' => '2.09.04.02.02.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penanganan Kerawanan Pangan dan Gizi Kabupaten/Kota'],
            ['nomor_kode' => '2.09.04.02.02.0004', 'nomenklatur' => 'Pelaksanaan Intervensi Kewaspadaan Pangan dan Gizi'],
            ['nomor_kode' => '2.09.04.02.02.0005', 'nomenklatur' => 'Penyusunan Peta Situasi Kewaspadaan Pangan dan Gizi Kabupaten/Kota'],
            ['nomor_kode' => '2.09.05.02.01.0004', 'nomenklatur' => 'Rekomendasi Keamanan Pangan Segar Asal Tumbuhan Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.05.02.01.0006', 'nomenklatur' => 'Rekomendasi Perizinan keamanan pangan segar asal tumbuhan'],
            ['nomor_kode' => '2.09.05.02.01.0007', 'nomenklatur' => 'Penyediaan Sarana Pengujian keamanan dan mutu pangan segar asal tumbuhan Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.05.02.01.0008', 'nomenklatur' => 'Koordinasi dan sinkronisasi keamanan dan mutu pangan segar asal tumbuhan'],
            ['nomor_kode' => '2.09.05.02.01.0009', 'nomenklatur' => 'Penguatan kelembagaan pengawas keamanan dan mutu pangan segar asal tumbuhan'],
            ['nomor_kode' => '2.10.04.02.01.0005', 'nomenklatur' => 'Inventarisasi Kasus Pertanahan dalam 1 (satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.10.05.02.01.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penyelesaian Masalah Ganti Kerugian dan Santunan Tanah untuk Pembangunan oleh Pemerintah Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.10.08.02.02.0001', 'nomenklatur' => 'Pelaksanaan Inventarisasi Tanah Kosong'],
            ['nomor_kode' => '2.11.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.11.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.11.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.11.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.11.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.11.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.11.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.11.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.11.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.11.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.11.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.11.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.11.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.11.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.11.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.11.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.11.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.11.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.11.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.11.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.11.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.11.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.11.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.11.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.11.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.11.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.11.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.11.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.11.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.11.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.11.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.11.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.11.02.02.02.0002', 'nomenklatur' => 'Pembuatan dan Pelaksanaan KLHS RPJPD/RPJMD'],
            ['nomor_kode' => '2.11.03.02.01.0002', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Pelaksanaan Pengendalian Emisi Gas Rumah Kaca, Mitigasi dan Adaptasi Perubahan Iklim'],
            ['nomor_kode' => '2.11.03.02.01.0004', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pencegahan  Pencemaran Lingkungan Hidup terhadap Media Tanah, Air, Udara, dan Laut'],
            ['nomor_kode' => '2.11.03.02.01.0007', 'nomenklatur' => 'Pelaksanaan pemantauan kualitas Lingkungan Hidup terhadap Media Tanah, Air, Udara, dan Laut'],
            ['nomor_kode' => '2.11.03.02.01.0011', 'nomenklatur' => 'Penyusunan dokumen status lingkungan hidup daerah'],
            ['nomor_kode' => '2.11.03.02.01.0015', 'nomenklatur' => 'Pengelolaan Laboratorium Lingkungan Hidup kabupaten/kota'],
            ['nomor_kode' => '2.11.04.02.01.0003', 'nomenklatur' => 'Pengelolaan Kebun Raya'],
            ['nomor_kode' => '2.11.04.02.01.0004', 'nomenklatur' => 'Pengelolaan Ruang Terbuka Hijau (RTH)'],
            ['nomor_kode' => '2.11.04.02.01.0006', 'nomenklatur' => 'Pengembangan Kapasitas Kelembagaan dan SDM dalam Pengelolaan Keanekaragaman Hayati'],
            ['nomor_kode' => '2.11.05.02.01.0002', 'nomenklatur' => 'Verifikasi Lapangan untuk Memastikan Pemenuhan Persyaratan Administrasi dan Teknis Penyimpanan sementara Limbah B3'],
            ['nomor_kode' => '2.11.06.02.01.0001', 'nomenklatur' => 'Fasilitasi Pemenuhan Ketentuan dan Kewajiban Izin Lingkungan dan/atau Izin PPLH'],
            ['nomor_kode' => '2.11.06.02.01.0007', 'nomenklatur' => 'Pengembangan Kapasitas Pejabat Pengawas Lingkungan Hidup'],
            ['nomor_kode' => '2.11.09.02.01.0001', 'nomenklatur' => 'Penilaian Kinerja Masyarakat/Lembaga Masyarakat/Dunia Usaha/Dunia Pendidikan/Filantropi dalam Perlindungan dan Pengelolaan Lingkungan Hidup'],
            ['nomor_kode' => '2.11.10.02.01.0004', 'nomenklatur' => 'Pengelolaan Pengaduan permasalahan Pencemaran dan Perusakan Lingkungan Hidup tingkat Kabupaten/Kota'],
            ['nomor_kode' => '2.11.10.02.01.0005', 'nomenklatur' => 'Penyelesaian sengketa lingkungan hidup yang ditangani yang menjadi kewenangan kabupaten/kota'],
            ['nomor_kode' => '2.11.11.02.01.0004', 'nomenklatur' => 'Peningkatan Peran Serta Masyarakat dalam Pengelolaan Persampahan'],
            ['nomor_kode' => '2.11.11.02.01.0007', 'nomenklatur' => 'Penyediaan Sarana dan Prasarana Pengelolaan Persampahan di TPA/TPST/SPA Kabupaten/Kota'],
            ['nomor_kode' => '2.11.11.02.01.0012', 'nomenklatur' => 'Penanganan sampah melalui pengangkutan'],
            ['nomor_kode' => '2.11.11.02.01.0019', 'nomenklatur' => 'Pengurangan sampah melalui pendauran ulang sampah'],
            ['nomor_kode' => '2.12.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.12.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.12.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.12.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.12.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.12.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.12.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.12.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.12.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.12.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.12.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.12.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.12.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.12.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.12.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.12.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.12.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.12.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '2.12.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.12.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.12.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.12.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.12.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.12.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.12.01.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],
            ['nomor_kode' => '2.12.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.12.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.12.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.12.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.12.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.12.01.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.12.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.12.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.12.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.12.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.12.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.12.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.12.02.02.01.0001', 'nomenklatur' => 'Pendataan Penduduk Non Permanen dan Rentan Administrasi Kependudukan'],
            ['nomor_kode' => '2.12.02.02.01.0002', 'nomenklatur' => 'Pencatatan, Penatausahaan dan Penerbitan Dokumen Atas Pendaftaran Penduduk'],
            ['nomor_kode' => '2.12.02.02.01.0004', 'nomenklatur' => 'Peningkatan Pelayanan Pendaftaran Penduduk'],
            ['nomor_kode' => '2.12.02.02.01.0005', 'nomenklatur' => 'Pencatatan, Penatausahaan dan Penerbitan Dokumen Atas Pelaporan Peristiwa Kependudukan'],
            ['nomor_kode' => '2.12.02.02.01.0007', 'nomenklatur' => 'Penerbitan Dokumen Atas Hasil Pelaporan Peristiwa Kependudukan'],
            ['nomor_kode' => '2.12.02.02.03.0002', 'nomenklatur' => 'Pelayanan Secara Aktif Pendaftaran Peristiwa Kependudukan dan Pencatatan Peristiwa Penting Terkait Pendaftaran Penduduk'],
            ['nomor_kode' => '2.12.02.02.03.0005', 'nomenklatur' => 'Sosialisasi Pendaftaran Penduduk'],
            ['nomor_kode' => '2.12.03.02.01.0001', 'nomenklatur' => 'Pencatatan, Penatausahaan dan Penerbitan Dokumen Atas Pelaporan Peristiwa Penting'],
            ['nomor_kode' => '2.12.03.02.01.0002', 'nomenklatur' => 'Peningkatan dalam Pelayanan Pencatatan Sipil'],
            ['nomor_kode' => '2.12.03.02.02.0001', 'nomenklatur' => 'Koordinasi dengan Kantor Kementerian yang Menyelenggarakan Urusan Pemerintahan di Bidang Agama Kabupaten/Kota dan Pengadilan Agama yang Berkaitan dengan Pencatatan Nikah, Talak, Cerai, dan Rujuk bagi Penduduk yang Beragama Islam'],
            ['nomor_kode' => '2.12.03.02.02.0004', 'nomenklatur' => 'Pelayanan Secara Aktif Pendaftaran Peristiwa Kependudukan dan Pencatatan Peristiwa Penting Terkait Pencatatan Sipil'],
            ['nomor_kode' => '2.12.03.02.02.0008', 'nomenklatur' => 'Sosialisasi Terkait Pencatatan Sipil'],
            ['nomor_kode' => '2.12.04.02.01.0001', 'nomenklatur' => 'Pengolahan dan Penyajian Data Kependudukan'],
            ['nomor_kode' => '2.12.04.02.01.0002', 'nomenklatur' => 'Kerja Sama Pemanfaatan Data Kependudukan'],
            ['nomor_kode' => '2.12.04.02.03.0001', 'nomenklatur' => 'Koordinasi Antar Lembaga Pemerintah dan Lembaga Non-Pemerintah di Kabupaten/Kota dalam Penertiban Pengelolaan Informasi Administrasi Kependudukan'],
            ['nomor_kode' => '2.12.04.02.03.0003', 'nomenklatur' => 'Fasilitasi Terkait Pengelolaan Informasi Administrasi Kependudukan'],
            ['nomor_kode' => '2.12.04.02.03.0004', 'nomenklatur' => 'Penyelenggaraan Pemanfaatan Data Kependudukan'],
            ['nomor_kode' => '2.12.04.02.03.0005', 'nomenklatur' => 'Sosialisasi Terkait Pengelolaan Informasi Administrasi Kependudukan'],
            ['nomor_kode' => '2.12.04.02.03.0007', 'nomenklatur' => 'Komunikasi, Informasi, dan Edukasi kepada Pemangku Kepentingan dan Masyarakat'],
            ['nomor_kode' => '2.12.04.02.03.0008', 'nomenklatur' => 'Penyajian Data Kependudukan yang Akurat dan dapat Dipertanggungjawabkan'],
            ['nomor_kode' => '2.12.05.02.01.0001', 'nomenklatur' => 'Penyediaan Data Kependudukan Kabupaten/Kota'],
            ['nomor_kode' => '2.14.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.14.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.14.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.14.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.14.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.14.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.14.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.14.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.14.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.14.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.14.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.14.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.14.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.14.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.14.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.14.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.14.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.14.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.14.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.14.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.14.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.14.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.14.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.14.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.14.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.14.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.14.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.14.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.14.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.14.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.14.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.14.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.14.01.02.09.0007', 'nomenklatur' => 'Pemeliharaan Aset Tetap Lainnya'],
            ['nomor_kode' => '2.14.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.14.02.02.01.0002', 'nomenklatur' => 'Penyusunan dan Pemanfaatan Grand Design Pembangunan Kependudukan (GDPK) Tingkat Kabupaten/Kota'],
            ['nomor_kode' => '2.14.02.02.01.0004', 'nomenklatur' => 'Pelaksanaan Survei/Pendataan Indeks Pengetahuan Masyarakat tentang Kependudukan'],
            ['nomor_kode' => '2.14.02.02.01.0007', 'nomenklatur' => 'Penyediaan dan Pengembangan Materi Pendidikan Kependudukan Jalur Pendidikan Formal Sesuai Isu Lokal Kabupaten/Kota'],
            ['nomor_kode' => '2.14.02.02.01.0008', 'nomenklatur' => 'Penyediaan dan Pengembangan Materi Pendidikan Kependudukan Jalur Pendidikan Nonformal Sesuai Isu Lokal Kabupaten/Kota'],
            ['nomor_kode' => '2.14.02.02.01.0009', 'nomenklatur' => 'Advokasi, Sosialisasi dan Fasilitasi Pelaksanaan Pendidikan Kependudukan Jalur Formal di Satuan Pendidikan Jenjang SD/MI dan SLTP/MTS, Jalur Nonformal dan Informal'],
            ['nomor_kode' => '2.14.02.02.01.0012', 'nomenklatur' => 'Advokasi tentang Pemanfaatan Kajian Dampak Kependudukan Beserta Model Solusi Strategis Sebagai Peringatan Dini Dampak Kependudukan kepada Pemangku Kepentingan'],
            ['nomor_kode' => '2.14.02.02.01.0013', 'nomenklatur' => 'Sosialisasi tentang Pemanfaatan Kajian Dampak Kependudukan Beserta Model Solusi Strategis Sebagai Peringatan Dini Dampak Kependudukan kepada Pemangku Kepentingan'],
            ['nomor_kode' => '2.14.02.02.01.0016', 'nomenklatur' => 'Implementasi Pendidikan Kependudukan Jalur Informal di Kelompok Kegiatan Masyarakat Binaan'],
            ['nomor_kode' => '2.14.02.02.01.0017', 'nomenklatur' => 'Pelaksanaan Sarasehan Hasil Pemutakhiran Data Keluarga'],
            ['nomor_kode' => '2.14.02.02.01.0018', 'nomenklatur' => 'Penguatan Kerja Sama Pelaksanaan Pendidikan Kependudukan Jalur Pendidikan Nonformal'],
            ['nomor_kode' => '2.14.02.02.01.0019', 'nomenklatur' => 'Implementasi Pendidikan Kependudukan Jalur Pendidikan Formal dan Nonformal'],
            ['nomor_kode' => '2.14.02.02.01.0020', 'nomenklatur' => 'Penyerasian Kebijakan Pembangunan Daerah Kabupaten/Kota terhadap  Pembangunan Keluarga, Kependudukan, dan Keluarga Berencana (Bangga Kencana)'],
            ['nomor_kode' => '2.14.02.02.01.0021', 'nomenklatur' => 'Kerjasama Pelaksanaan Pendidikan Kependudukan Jalur Pendidikan Formal'],
            ['nomor_kode' => '2.14.02.02.01.0022', 'nomenklatur' => 'Pelaksanaan penyediaan data dan sosialisasi Indeks Pembangunan Berwawasan Kependudukan (IPBK)'],
            ['nomor_kode' => '2.14.02.02.01.0023', 'nomenklatur' => 'Pelaksanaan Rapat Pengendalian Program Bangga Kencana'],
            ['nomor_kode' => '2.14.02.02.02.0002', 'nomenklatur' => 'Penyediaan dan Pengolahan Data Kependudukan'],
            ['nomor_kode' => '2.14.02.02.02.0005', 'nomenklatur' => 'Penyusunan Kajian Dampak Kependudukan'],
            ['nomor_kode' => '2.14.02.02.02.0006', 'nomenklatur' => 'Pengembangan Model Solusi Strategis Pengendalian Dampak Kependudukan'],
            ['nomor_kode' => '2.14.02.02.02.0009', 'nomenklatur' => 'Pembinaan dan Pengawasan Penyelenggaraan Sistem Informasi Keluarga'],
            ['nomor_kode' => '2.14.02.02.02.0010', 'nomenklatur' => 'Pemanfaatan Data Hasil Pemutakhiran Data Keluarga'],
            ['nomor_kode' => '2.14.02.02.02.0011', 'nomenklatur' => 'Penyediaan Data dan Informasi Keluarga'],
            ['nomor_kode' => '2.14.02.02.02.0012', 'nomenklatur' => 'Pencatatan dan Pengumpulan Data Keluarga'],
            ['nomor_kode' => '2.14.02.02.02.0013', 'nomenklatur' => 'Pengolahan dan Pelaporan Data Pengendalian Lapangan dan Pelayanan KB'],
            ['nomor_kode' => '2.14.02.02.02.0015', 'nomenklatur' => 'Pembentukan dan operasionalisasi  Rumah Data Kependudukan di Kampung KB  Untuk Memperkuat Integrasi Program Bangga Kencana di Sektor Lain'],
            ['nomor_kode' => '2.14.02.02.02.0016', 'nomenklatur' => 'Pelaksanaan Sistem Peringatan Dini Pengendalian Penduduk di tingkat kabupaten/kota'],
            ['nomor_kode' => '2.14.02.02.02.0017', 'nomenklatur' => 'Perumusan Parameter pengendalian penduduk dan KB'],
            ['nomor_kode' => '2.14.02.02.02.0018', 'nomenklatur' => 'Pembinaan dan Pengawasan Pencatatan dan Pelaporan Program Bangga Kencana'],
            ['nomor_kode' => '2.14.02.02.02.0019', 'nomenklatur' => 'Pemetaan Program Pembangunan Keluarga, Kependudukan, dan Keluarga Berencana (Bangga Kencana)'],
            ['nomor_kode' => '2.14.02.02.02.0020', 'nomenklatur' => 'Penyusunan Profil program Pembangunan Keluarga, Kependudukan, dan Keluarga Berencana (Bangga Kencana)'],
            ['nomor_kode' => '2.14.03.02.01.0008', 'nomenklatur' => 'Pengendalian Program KKBPK'],
            ['nomor_kode' => '2.14.03.02.01.0009', 'nomenklatur' => 'Penyediaan dan Distribusi Sarana KIE Program Bangga Kencana'],
            ['nomor_kode' => '2.14.03.02.01.0010', 'nomenklatur' => 'Pengelolaan Operasional dan Sarana di Balai Penyuluhan Bangga Kencana'],
            ['nomor_kode' => '2.14.03.02.01.0011', 'nomenklatur' => 'Pelaksanaan Mekanisme Operasional Program Bangga Kencana melalui Rapat Koordinasi Kecamatan (Rakorcam), Rapat Koordinasi Desa (Rakordes), dan Mini Lokakarya (Minilok)'],
            ['nomor_kode' => '2.14.03.02.01.0012', 'nomenklatur' => 'Promosi dan KIE Program Bangga Kencana Melalui Media Massa Cetak dan Elektronik serta Media Luar Ruang'],
            ['nomor_kode' => '2.14.03.02.01.0013', 'nomenklatur' => 'Komunikasi, Informasi dan Edukasi (KIE) ProgramBangga Kencana sesuai Kearifan Budaya Lokal'],
            ['nomor_kode' => '2.14.03.02.01.0014', 'nomenklatur' => 'Advokasi Program Bangga kencana oleh pokja advokasi kepada Stakeholders dan Mitra Kerja'],
            ['nomor_kode' => '2.14.03.02.02.0002', 'nomenklatur' => 'Penyediaan Sarana Pendukung Operasional PKB/PLKB'],
            ['nomor_kode' => '2.14.03.02.02.0004', 'nomenklatur' => 'Penggerakan Kader Institusi Masyarakat Pedesaan (IMP)'],
            ['nomor_kode' => '2.14.03.02.02.0005', 'nomenklatur' => 'Pembinaan IMP dan Program Bangga Kencana  di Lini Lapangan oleh PKB/PLKB'],
            ['nomor_kode' => '2.14.03.02.02.0006', 'nomenklatur' => 'Fasilitasi Pelaksanaan Penyuluhan, Penggerakan, Pelayanan dan Pengembangan Program Bangga Kencana untuk Petugas Keluarga Berencana/Penyuluh Lapangan Keluarga Berencana (PKB/PLKB)'],
            ['nomor_kode' => '2.14.03.02.03.0001', 'nomenklatur' => 'Pengendalian Pendistribusian Alat dan Obat Kontrasepsi dan Sarana Penunjang Pelayanan KB ke Fasilitas Kesehatan Termasuk Jaringan dan Jejaringnya'],
            ['nomor_kode' => '2.14.03.02.03.0003', 'nomenklatur' => 'Peningkatan Kesertaan Penggunaan Metode Kontrasepsi Jangka Panjang (MKJP)'],
            ['nomor_kode' => '2.14.03.02.03.0004', 'nomenklatur' => 'Penyediaan Dukungan Ayoman Komplikasi Berat dan Kegagalan Penggunaan MKJP'],
            ['nomor_kode' => '2.14.03.02.03.0005', 'nomenklatur' => 'Penyusunan Rencana Kebutuhan Alat dan Obat Kontrasepsi (Alokon) dan Sarana Penunjang Pelayanan KB'],
            ['nomor_kode' => '2.14.03.02.03.0006', 'nomenklatur' => 'Penyediaan Sarana Penunjang Pelayanan KB'],
            ['nomor_kode' => '2.14.03.02.03.0007', 'nomenklatur' => 'Pembinaan Pasca Pelayanan bagi Peserta KB'],
            ['nomor_kode' => '2.14.03.02.03.0008', 'nomenklatur' => 'Pembinaan Pelayanan Keluarga Berencana dan Kesehatan Reproduksi di Fasilitas Kesehatan Termasuk Jaringan dan Jejaringnya'],
            ['nomor_kode' => '2.14.03.02.03.0010', 'nomenklatur' => 'Peningkatan Kompetensi Tenaga Pelayanan Keluarga Berencana dan Kesehatan Reproduksi'],
            ['nomor_kode' => '2.14.03.02.03.0011', 'nomenklatur' => 'Dukungan Operasional Pelayanan KB Bergerak'],
            ['nomor_kode' => '2.14.03.02.03.0013', 'nomenklatur' => 'Peningkatan Kesertaan KB Pria'],
            ['nomor_kode' => '2.14.03.02.03.0014', 'nomenklatur' => 'Pemerintah Daerah yang Mendapatkan Fasilitasi dan Pembinaan Pendampingan Ibu Hamil dan Ibu Pasca Persalinan'],
            ['nomor_kode' => '2.14.03.02.03.0015', 'nomenklatur' => 'Peningkatan Kompetensi Pengelola dan Petugas Logistik Alat dan Obat Kontrasepsi serta Sarana Penunjang Pelayanan KB'],
            ['nomor_kode' => '2.14.03.02.03.0016', 'nomenklatur' => 'Promosi dan Konseling KB Pasca Persalinan'],
            ['nomor_kode' => '2.14.03.02.04.0001', 'nomenklatur' => 'Penguatan Peran Serta Organisasi Kemasyarakatan dan Mitra Kerja Lainnya dalam Pelaksanaan Pelayanan dan Pembinaan Kesertaan Ber-KB'],
            ['nomor_kode' => '2.14.03.02.04.0002', 'nomenklatur' => 'Integrasi Pembangunan Lintas Sektor di Kampung KB'],
            ['nomor_kode' => '2.14.03.02.04.0004', 'nomenklatur' => 'Pembinaan Terpadu Kampung KB'],
            ['nomor_kode' => '2.14.03.02.04.0005', 'nomenklatur' => 'Fasilitasi Pengelolaan Dapur Sehat Atasi Stunting (DASHAT) di Kampung Keluarga Berkualitas'],
            ['nomor_kode' => '2.14.03.02.04.0006', 'nomenklatur' => 'Pelaksanaan dan Pengelolaan Program Bangga Kencana di Kampung Keluarga Berkualitas'],
            ['nomor_kode' => '2.14.04.02.01.0008', 'nomenklatur' => 'Promosi dan Sosialisasi Kelompok Kegiatan Ketahanan dan Kesejahteraan Keluarga (Menjadi Orang Tua Hebat, Generasi Berencana, Kelanjutusiaan serta Pengelolaan Keuangan Keluarga)'],
            ['nomor_kode' => '2.14.04.02.01.0014', 'nomenklatur' => 'Penumbuhan dan Peningkatan Kesadaran Keluarga dalam Keterlibatan Perencanaan Kehidupan Menuju Keluarga Berkualitas'],
            ['nomor_kode' => '2.14.04.02.01.0015', 'nomenklatur' => 'Pembentukan dan operasional Sekolah Lansia di Kelompok BKL'],
            ['nomor_kode' => '2.14.04.02.01.0016', 'nomenklatur' => 'Pengelolaan Ketahanan Keluarga Melalui Pusat Pelayanan Keluarga Sejahtera (PPKS)'],
            ['nomor_kode' => '2.14.04.02.01.0017', 'nomenklatur' => 'Promosi dan Sosialisasi Kelompok Kegiatan Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA))'],
            ['nomor_kode' => '2.14.04.02.01.0018', 'nomenklatur' => 'Pengadaan Sarana Kelompok Kegiatan Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.04.02.01.0019', 'nomenklatur' => 'Orientasi/Pelatihan Teknis Pelaksana/Kader Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.04.02.01.0020', 'nomenklatur' => 'Advokasi dan Promosi iBangga (Indeks Pembangunan Keluarga)'],
            ['nomor_kode' => '2.14.04.02.01.0021', 'nomenklatur' => 'Orientasi dan Pelatihan Teknis Pengelola Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.04.02.01.0022', 'nomenklatur' => 'Sosialisasi iBangga (Indeks Pembangunan Keluarga)'],
            ['nomor_kode' => '2.14.04.02.01.0023', 'nomenklatur' => 'Penyerasian Kebijakan dalam Pelaksanaan Program yang Mendukung Tercapainya Bangga'],
            ['nomor_kode' => '2.14.04.02.01.0024', 'nomenklatur' => 'Penyediaan Biaya Operasional bagi Pengelola dan Pelaksana (Kader) Ketahanan dan Kesejaheraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.04.02.01.0025', 'nomenklatur' => 'Pelaksanaan Koordinasi Evaluasi Pencapaian iBangga (Indeks Pembangunan Keluarga)'],
            ['nomor_kode' => '2.14.04.02.01.0026', 'nomenklatur' => 'Penyediaan Biaya Operasional bagi Kelompok Kegiatan Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.04.02.01.0027', 'nomenklatur' => 'Penyediaan dan Pengembangan Materi iBangga (Indeks Pembangunan Keluarga)'],
            ['nomor_kode' => '2.14.04.02.01.0028', 'nomenklatur' => 'Pembentukan Kelompok Ketahanan dan Kesejahteraan Keluarga (Bina Keluarga Balita (BKB), Bina Keluarga Remaja (BKR), Pusat Informasi dan Konseling Remaja (PIK-R) Bina Keluarga Lansia (BKL), Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA) dan Pemberdayaan Ekonomi Keluarga)'],
            ['nomor_kode' => '2.14.04.02.02.0001', 'nomenklatur' => 'Penguatan Kebijakan Daerah dalam rangka Pemberdayaan dan Peningkatan Peran Serta Organisasi Kemasyarakatan dan Mitra Kerja Lainnya dalam Pembinaan Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPPKS, PIK-R dan Pemberdayaan Ekonomi Keluarga/UPPKS)'],
            ['nomor_kode' => '2.14.04.02.02.0002', 'nomenklatur' => 'Pendayagunaan Mitra Kerja dan Organisasi Kemasyarakatan dalam Penggerakan Operasional Pembinaan Program Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPPKS, PIK-R dan Pemberdayaan Ekonomi Keluarga/UPPKS)'],
            ['nomor_kode' => '2.14.04.02.02.0003', 'nomenklatur' => 'Pelaksanaan Peningkatan Kapasitas Mitra dan Organisasi Kemasyarakatan dalam Pengelolaan Program Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPPKS, PIK-R dan Pemberdayaan Ekonomi Keluarga/UPPKS)'],
            ['nomor_kode' => '2.14.04.02.02.0004', 'nomenklatur' => 'Promosi dan Sosialisasi Program Ketahanan dan Kesejahteraan Keluarga bagi Mitra Kerja'],
            ['nomor_kode' => '2.14.04.02.02.0005', 'nomenklatur' => 'Pemantauan  Data dan Informasi Keluarga Berisiko Stunting (Termasuk remaja Calon Pengantin/Calon PUS, Ibu Hamil, Pasca salin/kelahiran, Baduta/Balita)'],
            ['nomor_kode' => '2.14.04.02.02.0006', 'nomenklatur' => 'Pendampingan Keluarga Berisiko Stunting (Termasuk remaja Calon Pengantin/Calon PUS, Ibu Hamil, Pasca salin/kelahiran, Baduta/Balita)'],
            ['nomor_kode' => '2.15.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.15.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.15.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.15.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.15.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.15.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.15.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.15.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.15.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.15.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.15.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.15.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.15.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '2.15.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.15.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.15.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.15.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.15.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.15.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.15.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.15.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.15.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.15.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.15.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.15.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.15.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.15.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.15.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.15.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.15.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.15.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.15.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.15.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.15.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.15.01.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.15.02.02.01.0001', 'nomenklatur' => 'Pelaksanaan Penyusunan Rencana Induk Jaringan LLAJ Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.02.0001', 'nomenklatur' => 'Pembangunan Prasarana Jalan di Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.02.0002', 'nomenklatur' => 'Penyediaan Perlengkapan Jalan di Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.02.0003', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Prasarana Jalan'],
            ['nomor_kode' => '2.15.02.02.02.0004', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Perlengkapan Jalan'],
            ['nomor_kode' => '2.15.02.02.03.0007', 'nomenklatur' => 'Revitalisasi Terminal Tipe C (Fasilitas Utama dan Penunjang)'],
            ['nomor_kode' => '2.15.02.02.03.0010', 'nomenklatur' => 'Peningkatan Kapasitas Kompetensi SDM Pengelola Terminal Penumpang Tipe C'],
            ['nomor_kode' => '2.15.02.02.04.0001', 'nomenklatur' => 'Fasilitasi Pemenuhan Persyaratan Perolehan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir Kewenangan Kabupaten/Kota dalam Sistem Pelayanan Perizinan Berusaha Terintegrasi Secara Elektronik'],
            ['nomor_kode' => '2.15.02.02.04.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pengawasan Pelaksanaan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.05.0001', 'nomenklatur' => 'Penyediaan Sarana dan Prasarana Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.02.02.05.0002', 'nomenklatur' => 'Peningkatan Kapasitas Sumber Daya Manusia Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.02.02.05.0004', 'nomenklatur' => 'Penyediaan Bukti Lulus Uji Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.02.02.05.0007', 'nomenklatur' => 'Pemeliharaan Sarana dan Prasarana Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.02.02.06.0004', 'nomenklatur' => 'Pengawasan dan Pengendalian Efektivitas Pelaksanaan Kebijakan untuk Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.06.0015', 'nomenklatur' => 'Forum Lalu Lintas dan Angkutan Jalan untuk Jaringan Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.06.0017', 'nomenklatur' => 'Penataan Manajemen dan Rekayasa Lalu Lintas untuk Jaringan Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.07.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penilaian Hasil Andalalin'],
            ['nomor_kode' => '2.15.02.02.07.0005', 'nomenklatur' => 'Peningkatan Kompetensi Penilai Andalalin'],
            ['nomor_kode' => '2.15.02.02.07.0006', 'nomenklatur' => 'Pengawasan Pelaksanaan Rekomendasi Persetujuan Teknis Andalalin'],
            ['nomor_kode' => '2.15.02.02.09.0002', 'nomenklatur' => 'Pengendalian dan Pengawasan Ketersediaan Angkutan Umum untuk Jasa Angkutan Orang dan/atau Barang Antar Kota dalam 1 (Satu) Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.11.0003', 'nomenklatur' => 'Pengendalian Pelaksanaan Rencana Umum Jaringan Trayek Perkotaan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.16.0002', 'nomenklatur' => 'Penyediaan Data dan Informasi Tarif Kelas Ekonomi Angkutan Orang dan Angkutan Perkotaan dan Perdesaan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.15.02.02.16.0003', 'nomenklatur' => 'Pengendalian dan Pengawasan Tarif Kelas Ekonomi Angkutan Orang dan Angkutan Perkotaan dan Perdesaan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.16.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.16.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.16.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.16.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.16.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.16.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.16.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.16.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.16.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.16.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.16.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.16.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.16.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '2.16.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '2.16.01.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '2.16.01.02.04.0007', 'nomenklatur' => 'Pelaporan Pengelolaan Retribusi Daerah'],
            ['nomor_kode' => '2.16.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.16.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.16.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.16.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.16.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.16.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '2.16.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.16.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.16.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.16.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.16.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.16.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.16.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.16.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.16.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.16.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.16.01.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.16.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.16.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.16.01.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.16.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.16.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.16.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.16.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.16.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.16.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.16.02.02.01.0014', 'nomenklatur' => 'Relasi Media'],
            ['nomor_kode' => '2.16.02.02.01.0015', 'nomenklatur' => 'Kemitraan Komunikasi dengan Komunitas Informasi Masyarakat'],
            ['nomor_kode' => '2.16.02.02.01.0017', 'nomenklatur' => 'Pelayanan Informasi Publik'],
            ['nomor_kode' => '2.16.02.02.01.0018', 'nomenklatur' => 'Sosialisasi Peraturan Bidang Informasi dan Komunikasi Publik'],
            ['nomor_kode' => '2.16.02.02.01.0019', 'nomenklatur' => 'Monitoring Informasi Kebijakan, Opini, dan Aspirasi Publik'],
            ['nomor_kode' => '2.16.02.02.01.0020', 'nomenklatur' => 'Diseminasi Informasi'],
            ['nomor_kode' => '2.16.02.02.01.0021', 'nomenklatur' => 'Pengelolaan Media Komunikasi Publik'],
            ['nomor_kode' => '2.16.02.02.01.0022', 'nomenklatur' => 'Penyusunan Strategi Komunikasi Publik'],
            ['nomor_kode' => '2.16.02.02.01.0023', 'nomenklatur' => 'Penyusunan Konten'],
            ['nomor_kode' => '2.16.02.02.01.0024', 'nomenklatur' => 'Penguatan Kapasitas Sumber Daya Manusia Komunikasi Publik'],
            ['nomor_kode' => '2.16.03.02.01.0004', 'nomenklatur' => 'Pengelolaan Nama Domain dan Sub Domain Penyelenggaraan Pemerintah Daerah dan Pengelolaan Nama Domain Pemerintah Desa'],
            ['nomor_kode' => '2.16.03.02.02.0013', 'nomenklatur' => 'Koordinasi Pemanfaatan Pusat Data Nasional'],
            ['nomor_kode' => '2.16.03.02.02.0015', 'nomenklatur' => 'Fasilitasi penyelenggaraan SPBE di lingkungan Pemda'],
            ['nomor_kode' => '2.16.03.02.02.0016', 'nomenklatur' => 'Penyelenggaraan  pusat kendali Pemerintah Daerah'],
            ['nomor_kode' => '2.16.03.02.02.0017', 'nomenklatur' => 'Koordinasi Pengelolaan Data dan Informasi'],
            ['nomor_kode' => '2.16.03.02.02.0018', 'nomenklatur' => 'Koordinasi penyusunan dan/atau reviu arsitektur dan peta rencana SPBE Pemerintah Daerah'],
            ['nomor_kode' => '2.16.03.02.02.0019', 'nomenklatur' => 'Koordinasi pelaksanaan Manajemen SPBE'],
            ['nomor_kode' => '2.16.03.02.02.0020', 'nomenklatur' => 'Pembangunan dan/atau Pengembangan Aplikasi Khusus yang sesuai dengan arsitektur dan peta rencana SPBE pemerintah daerah'],
            ['nomor_kode' => '2.16.03.02.02.0021', 'nomenklatur' => 'Penyelenggaraan Sistem Penghubung Layanan Pemerintah Daerah'],
            ['nomor_kode' => '2.16.03.02.02.0024', 'nomenklatur' => 'Penyelenggaraan Jaringan Intra Pemerintah Daerah Kab/Kota'],
            ['nomor_kode' => '2.16.03.02.02.0029', 'nomenklatur' => 'Koordinasi pemanfaatan Aplikasi Umum SPBE'],
            ['nomor_kode' => '2.16.03.02.02.0030', 'nomenklatur' => 'Penyediaan Akses Internet untuk Perangkat Daerah dalam rangka penyelenggaraan SPBE'],
            ['nomor_kode' => '2.17.03.02.01.0003', 'nomenklatur' => 'Penguatan Tata Kelola Kelembagaan Koperasi'],
            ['nomor_kode' => '2.17.03.02.01.0004', 'nomenklatur' => 'Pelaksanaan Proses Pemeriksaan dan Pengawasan Koperasi yang Wilayah Keanggotaannya Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.17.04.02.01.0001', 'nomenklatur' => 'Pelaksanaan Penilaian Kesehatan KSP/USP Koperasi Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.17.04.02.01.0003', 'nomenklatur' => 'Penilaian Kesehatan Koperasi Meliputi Tata Kelola, Profil Risiko, Kinerja Keuangan, dan Permodalan'],
            ['nomor_kode' => '2.17.05.02.01.0001', 'nomenklatur' => 'Peningkatan Pemahaman dan Pengetahuan Perkoperasian serta Kapasitas dan Kompetensi SDM Koperasi'],
            ['nomor_kode' => '2.17.06.02.01.0002', 'nomenklatur' => 'Penumbuhan Kesadaran Keluarga dalam Peningkatan Taraf Hidup Keluarga Melalui Kehidupan Berkoperasi dan Pengembangan Ekonomi Lainnya'],
            ['nomor_kode' => '2.17.07.02.01.0002', 'nomenklatur' => 'Pemberdayaan Melalui Kemitraan Usaha Mikro'],
            ['nomor_kode' => '2.17.07.02.01.0004', 'nomenklatur' => 'Pemberdayaan Kelembagaan Potensi dan Pengembangan Usaha Mikro'],
            ['nomor_kode' => '2.17.07.02.01.0005', 'nomenklatur' => 'Koordinasi dan Sinkronisasi dengan Para Pemangku Kepentingan dalam Pemberdayaan Usaha Mikro'],
            ['nomor_kode' => '2.18.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.18.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.18.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.18.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.18.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.18.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.18.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.18.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.18.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.18.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.18.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.18.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '2.18.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '2.18.01.02.05.0001', 'nomenklatur' => 'Peningkatan Sarana dan Prasarana Disiplin Pegawai'],
            ['nomor_kode' => '2.18.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '2.18.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.18.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.18.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.18.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.18.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.18.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.18.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.18.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.18.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.18.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.18.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.18.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.18.01.02.06.0010', 'nomenklatur' => 'Penatausahaan Arsip Dinamis pada SKPD'],
            ['nomor_kode' => '2.18.01.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],
            ['nomor_kode' => '2.18.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.18.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.18.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.18.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.18.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.18.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.18.01.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.18.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.18.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.18.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.18.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.18.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.18.01.02.09.0008', 'nomenklatur' => 'Pemeliharaan Aset Tak Berwujud'],
            ['nomor_kode' => '2.18.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.18.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.18.02.02.01.0003', 'nomenklatur' => 'Fasilitasi Kemitraan yang dilakukan oleh Pemerintah Kabupaten/Kota'],
            ['nomor_kode' => '2.18.02.02.02.0001', 'nomenklatur' => 'Penyusunan Rencana Umum Penanaman Modal Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.18.02.02.02.0004', 'nomenklatur' => 'Penyusunan Peta Potensi Investasi Kabupaten/Kota'],
            ['nomor_kode' => '2.18.03.02.01.0002', 'nomenklatur' => 'Pelaksanaan Kegiatan Promosi Penanaman Modal Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.18.03.02.01.0003', 'nomenklatur' => 'Penyusunan Strategi Promosi Penanaman Modal Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.18.04.02.01.0005', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penetapan Pemberian Fasilitas/Insentif Daerah'],
            ['nomor_kode' => '2.18.04.02.01.0006', 'nomenklatur' => 'Penyediaan Pelayanan Perizinan Berusaha melalui Sistem Perizinan Berusaha Berbasis Risiko Terintegrasi secara Elektronik'],
            ['nomor_kode' => '2.18.04.02.01.0007', 'nomenklatur' => 'Penyediaan dan pengelolaan Layanan konsultasi perizinan berusaha berbasis risiko'],
            ['nomor_kode' => '2.18.04.02.01.0008', 'nomenklatur' => 'Pemantauan, analisis, evaluasi, dan pelaporan di bidang perizinan berusaha berbasis risiko'],
            ['nomor_kode' => '2.18.05.02.01.0004', 'nomenklatur' => 'Penyelesaian Permasalahan dan Hambatan yang dihadapi Pelaku Usaha dalam merealisasikan Kegiatan Usahanya'],
            ['nomor_kode' => '2.18.05.02.01.0005', 'nomenklatur' => 'Bimbingan Teknis kepada Pelaku Usaha'],
            ['nomor_kode' => '2.18.05.02.01.0006', 'nomenklatur' => 'Pengawasan Penanaman Modal'],
            ['nomor_kode' => '2.18.06.02.01.0002', 'nomenklatur' => 'Pengolahan, Penyajian dan Pemanfaatan Data dan Informasi Perizinan Berbasis Sistem Pelayanan Perizinan Berusaha Terintegrasi secara Elektronik'],
            ['nomor_kode' => '2.19.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.19.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.19.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.19.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.19.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.19.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.19.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.19.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.19.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.19.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.19.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.19.01.02.03.0007', 'nomenklatur' => 'Pemanfaatan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '2.19.01.02.04.0007', 'nomenklatur' => 'Pelaporan Pengelolaan Retribusi Daerah'],
            ['nomor_kode' => '2.19.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.19.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.19.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.19.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.19.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.19.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.19.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.19.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.19.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.19.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.19.01.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.19.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.19.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.19.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.19.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.19.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.19.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.19.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.19.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.19.01.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.19.02.02.01.0010', 'nomenklatur' => 'Pelaksanaan Koordinasi Strategis Lintas Sektor Penyelenggaraan Pelayanan Kepemudaan melalui pembentukan tim koordinasi kabupaten/kota Penyelenggaraan Pelayanan Kepemudaan serta penyusunan dan implementasi Rencana Aksi Daerah/RAD Tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.02.02.01.0011', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan Penyelenggaraan Pengembangan Kewirausahaan Pemuda Bagi Wirausaha pemula Tingkat Kabupaten/kota'],
            ['nomor_kode' => '2.19.02.02.01.0012', 'nomenklatur' => 'Pemberian Penghargaan Kepemudaan bagi yang berprestasi dan/atau berjasa dalam memajukan potensi pemuda'],
            ['nomor_kode' => '2.19.02.02.01.0013', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Penyelenggaraan Pengembangan kepemimpinan pemuda tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.02.02.01.0014', 'nomenklatur' => 'Pelaksanaan koordinasi dan sinkronisasi Pemenuhan Hak Pemuda di tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.02.02.01.0015', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Penyelenggaraan Pengembangan Kepeloporan Pemuda bagi Pemuda Pelopor Tingkat Kabupaten/kota'],
            ['nomor_kode' => '2.19.02.02.01.0016', 'nomenklatur' => 'Penyediaan dan Pengelolaan Prasarana dan Sarana Kepemudaan tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.02.02.02.0004', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan penyelenggaraan Pemberdayaan organisasi kepemudaan melalui kemitraan berbasis peneguhan kemandirian ekonomi pemuda tingkat Kabupaten/Kota'],
            ['nomor_kode' => '2.19.03.02.01.0005', 'nomenklatur' => 'Koordinasi dan sinkronisasi penyediaan prasarana olahraga melalui perencanaan, pengadaan, pemanfaatan, pemeliharaan, dan pengawasan Prasarana Olahraga di tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.03.02.02.0004', 'nomenklatur' => 'Penyelenggaraan Kejuaraan Olahraga Multi Event dan Single Event Tingkat Kabupaten/Kota'],
            ['nomor_kode' => '2.19.03.02.02.0005', 'nomenklatur' => 'Penyelenggaraan Pekan Paralimpik Pelajar Tingkat Nasional dan kabupaten/kota serta Kejuaraan Paralimpik Pelajar Tingkat kabupaten/kota dan kabupaten/kota'],
            ['nomor_kode' => '2.19.03.02.02.0006', 'nomenklatur' => 'Keikutsertaan anggota kontingen kabupaten/kota dalam Penyelenggaraan pekan dan kejuaraan olahraga'],
            ['nomor_kode' => '2.19.03.02.03.0006', 'nomenklatur' => 'Seleksi Atlet Daerah'],
            ['nomor_kode' => '2.19.03.02.03.0007', 'nomenklatur' => 'Pemberian Penghargaan olahraga bagi yang berprestasi dan/atau berjasa dalam memajukan Olahraga'],
            ['nomor_kode' => '2.19.03.02.03.0009', 'nomenklatur' => 'Pembinaan dan Pengembangan Olahragawan Berprestasi kabupaten/kota'],
            ['nomor_kode' => '2.19.03.02.03.0010', 'nomenklatur' => 'pembentukan dan Penyediaan sistem data Keolahragaan terpadu di kabupaten/kota'],
            ['nomor_kode' => '2.19.03.02.04.0006', 'nomenklatur' => 'Peningkatan Kerja Sama Organisasi Keolahragaan Kabupaten/Kota dengan Lembaga Terkait'],
            ['nomor_kode' => '2.19.03.02.05.0007', 'nomenklatur' => 'Pengembangan Olahraga Wisata, Tantangan dan Petualangan'],
            ['nomor_kode' => '2.19.03.02.05.0009', 'nomenklatur' => 'Penyediaan prasarana dan sarana olahraga rekreasi melalui perencanaan, pengadaan, pemanfaatan, pemeliharaan, pengembangan, dan pengawasan'],
            ['nomor_kode' => '2.19.03.02.05.0010', 'nomenklatur' => 'Pemassalan olahraga dan penyelenggaraan festival Olahraga Rekreasi yang berjenjang dan berkelanjutan pada tingkat daerah, nasional, dan internasional'],
            ['nomor_kode' => '2.19.04.02.01.0003', 'nomenklatur' => 'Pengembangan Kapasitas SDM Kepramukaan Tingkat Daerah'],
            ['nomor_kode' => '2.19.04.02.01.0005', 'nomenklatur' => 'Penyelenggaraan Kegiatan Kepramukaan Tingkat Daerah'],
            ['nomor_kode' => '2.19.04.02.01.0008', 'nomenklatur' => 'Partisipasi dan Keikutsertaan dalam Kegiatan Kepramukaan'],
            ['nomor_kode' => '2.20.02.02.01.0007', 'nomenklatur' => 'Pengingkatan Kapasitas Kelembagaan Statistik Sektoral'],
            ['nomor_kode' => '2.20.02.02.01.0008', 'nomenklatur' => 'Peningkatan Peran Statistik Sektoral terhadap Sistem Statistik Nasional'],
            ['nomor_kode' => '2.20.02.02.01.0009', 'nomenklatur' => 'Peningkatan Kualitas Data Statistik Sektoral'],
            ['nomor_kode' => '2.20.02.02.01.0010', 'nomenklatur' => 'Penyelenggaraan Statistik Sektoral yang sesuai dengan Prinsip Satu Data Indonesia'],
            ['nomor_kode' => '2.20.02.02.01.0011', 'nomenklatur' => 'Pelaksanaan Proses Bisnis Statistik Sektoral Sesuai Standar'],
            ['nomor_kode' => '2.21.02.02.01.0002', 'nomenklatur' => 'Pelaksanaan Analisis Kebutuhan dan Pengelolaan Sumber Daya Keamanan Informasi Pemerintah Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.21.02.02.01.0003', 'nomenklatur' => 'Pelaksanaan Keamanan Informasi Pemerintahan Daerah Kabupaten/Kota Berbasis Elektronik dan Non Elektronik'],
            ['nomor_kode' => '2.21.02.02.02.0001', 'nomenklatur' => 'Operasionalisasi Jaring Komunikasi Sandi Pemerintah Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.22.02.02.01.0001', 'nomenklatur' => 'Pelindungan, Pengembangan, Pemanfaatan Objek Pemajuan Kebudayaan'],
            ['nomor_kode' => '2.22.02.02.02.0002', 'nomenklatur' => 'Pembinaan Sumber Daya Manusia, Lembaga, dan Pranata Tradisional'],
            ['nomor_kode' => '2.22.03.02.01.0001', 'nomenklatur' => 'Peningkatan Pendidikan dan Pelatihan Sumber Daya Manusia Kesenian Tradisional'],
            ['nomor_kode' => '2.22.03.02.01.0003', 'nomenklatur' => 'Peningkatan Kapasitas Tata Kelola Lembaga Kesenian Tradisional'],
            ['nomor_kode' => '2.22.04.02.01.0003', 'nomenklatur' => 'Peningkatan Akses Masyarakat Terhadap Data dan Informasi Sejarah'],
            ['nomor_kode' => '2.22.05.02.01.0002', 'nomenklatur' => 'Penetapan Cagar Budaya'],
            ['nomor_kode' => '2.22.05.02.02.0001', 'nomenklatur' => 'Pelindungan Cagar Budaya'],
            ['nomor_kode' => '2.22.05.02.02.0002', 'nomenklatur' => 'Pengembangan Cagar Budaya'],
            ['nomor_kode' => '2.22.06.02.01.0001', 'nomenklatur' => 'Pelindungan, Pengembangan, dan Pemanfataan Koleksi Secara Terpadu'],
            ['nomor_kode' => '2.22.06.02.01.0004', 'nomenklatur' => 'Penyediaan dan Pemeliharaan Sarana dan Prasarana Museum'],
            ['nomor_kode' => '2.23.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.23.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.23.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.23.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.23.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.23.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.23.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.23.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.23.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.23.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.23.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.23.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            ['nomor_kode' => '2.23.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.23.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.23.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.23.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.23.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.23.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.23.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.23.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.23.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.23.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.23.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            ['nomor_kode' => '2.23.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.23.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.23.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.23.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.23.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.23.02.02.01.0004', 'nomenklatur' => 'Pembinaan Perpustakaan pada Satuan Pendidikan Dasar di Seluruh Wilayah Kabupaten/Kota Sesuai dengan Standar Nasional Perpustakaan'],
            ['nomor_kode' => '2.23.02.02.01.0011', 'nomenklatur' => 'Pengembangan Perpustakaan di Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.23.02.02.01.0016', 'nomenklatur' => 'Peningkatan Kapasitas Tenaga Perpustakaan dan Pustakawan Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.23.02.02.01.0017', 'nomenklatur' => 'Penyusunan Data dan Informasi Perpustakaan'],
            ['nomor_kode' => '2.23.02.02.01.0018', 'nomenklatur' => 'Pengelolaan dan Pengembangan Bahan Perpustakaan'],
            ['nomor_kode' => '2.23.02.02.01.0020', 'nomenklatur' => 'Pengembangan dan Pemeliharaan Layanan Perpustakaan Elektronik'],
            ['nomor_kode' => '2.23.02.02.02.0006', 'nomenklatur' => 'Pemilihan Duta Baca/Bunda Baca/Bunda Literasi Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.23.02.02.02.0007', 'nomenklatur' => 'Pengembangan Literasi Berbasis Inklusi Sosial'],
            ['nomor_kode' => '2.23.02.02.02.0009', 'nomenklatur' => 'Pemberian Penghargaan Gerakan Budaya Gemar Membaca'],
            ['nomor_kode' => '2.23.02.02.02.0010', 'nomenklatur' => 'Sosialisasi Budaya Baca dan Literasi pada Satuan Pendidikan Dasar dan Masyarakat'],
            ['nomor_kode' => '2.23.03.02.01.0003', 'nomenklatur' => 'Peningkatan Peran Serta Masyarakat dalam Penyimpanan, Perawatan, Pelestarian, dan Pendaftaran Naskah Kuno'],
            ['nomor_kode' => '2.23.03.02.01.0004', 'nomenklatur' => 'Pengembangan, Pengolahan dan Pengalihmediaan Naskah Kuno yang Dimiliki oleh Masyarakat untuk Dilestarikan dan Didayagunakan'],
            ['nomor_kode' => '2.24.02.02.01.0001', 'nomenklatur' => 'Penciptaan dan Penggunaan Arsip Dinamis'],
            ['nomor_kode' => '2.24.02.02.01.0002', 'nomenklatur' => 'Pemeliharaan dan Penyusutan Arsip Dinamis'],
            ['nomor_kode' => '2.24.02.02.01.0003', 'nomenklatur' => 'Pengawasan Arsip Dinamis Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.24.02.02.02.0001', 'nomenklatur' => 'Pengumpulan dan Penyampaian Salinan Otentik Naskah Asli Arsip Terjaga kepada ANRI'],
            ['nomor_kode' => '2.24.02.02.02.0004', 'nomenklatur' => 'Akuisisi, Pengolahan, Preservasi, dan Akses Arsip Statis'],
            ['nomor_kode' => '2.24.02.02.03.0001', 'nomenklatur' => 'Penyediaan Informasi, Akses dan Layanan Kearsipan Tingkat Daerah Kabupaten/Kota Melalui JIKN'],
            ['nomor_kode' => '2.24.02.02.03.0002', 'nomenklatur' => 'Pemberdayaan Kapasitas Unit Kearsipan dan Lembaga Kearsipan Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.24.03.02.01.0003', 'nomenklatur' => 'Pelaksanaan Pemusnahan Arsip yang Memiliki Retensi di Bawah 10 Tahun'],
            ['nomor_kode' => '2.24.03.02.01.0004', 'nomenklatur' => 'Penilaian, Penetapan dan Pelaksanaan Pemusnahan Arsip yang Memiliki Retensi di Bawah 10 (Sepuluh) Tahun'],
            ['nomor_kode' => '2.24.03.02.04.0001', 'nomenklatur' => 'Penilaian dan Penetapan Autentisitas Arsip Statis Sesuai Persyaratan Penjaminan Keabsahan Arsip'],
            ['nomor_kode' => '2.24.04.02.01.0004', 'nomenklatur' => 'Penyediaan Daftar dan Penetapan Izin Penggunaan Arsip yang Bersifat Tertutup'],
                    
                // =========================
            // 3.25.03 — PROGRAM PENGELOLAAN PERIKANAN TANGKAP
            // =========================

            // -- 3.25.03.02.01 Pengelolaan Penangkapan Ikan...
            ['nomor_kode' => '3.25.03.02.01.0001', 'nomenklatur' => 'Penyediaan Data dan Informasi Sumber Daya Ikan'],
            ['nomor_kode' => '3.25.03.02.01.0004', 'nomenklatur' => 'Penyediaan Sarana Usaha Perikanan Tangkap'],

            // -- 3.25.03.02.02 Pemberdayaan Nelayan Kecil...
            ['nomor_kode' => '3.25.03.02.02.0001', 'nomenklatur' => 'Pengembangan Kapasitas Nelayan Kecil'],
            ['nomor_kode' => '3.25.03.02.02.0002', 'nomenklatur' => 'Pelaksanaan Fasilitasi Pembentukan dan Pengembangan Kelembagaan Nelayan Kecil'],
            ['nomor_kode' => '3.25.03.02.02.0003', 'nomenklatur' => 'Pelaksanaan Fasilitasi Bantuan Pendanaan, Bantuan Pembiayaan, Kemitraan Usaha'],

            // -- 3.25.03.02.03 Pengelolaan dan Penyelenggaraan TPI
            ['nomor_kode' => '3.25.03.02.03.0002', 'nomenklatur' => 'Pelayanan Penyelenggaraan Tempat Pelelangan Ikan (TPI)'],

            // =========================
            // 3.25.04 — PROGRAM PENGELOLAAN PERIKANAN BUDIDAYA
            // =========================

            // -- 3.25.04.02.02 Pemberdayaan Pembudi Daya Ikan Kecil
            ['nomor_kode' => '3.25.04.02.02.0001', 'nomenklatur' => 'Pengembangan Kapasitas Pembudi Daya Ikan Kecil'],
            ['nomor_kode' => '3.25.04.02.02.0002', 'nomenklatur' => 'Pelaksanaan Fasilitasi Pembentukan dan Pengembangan Kelembagaan Pembudi Daya Ikan Kecil'],
            ['nomor_kode' => '3.25.04.02.02.0003', 'nomenklatur' => 'Pelaksanaan Fasilitasi Bantuan Pendanaan, Bantuan Pembiayaan, Kemitraan Usaha'],
            ['nomor_kode' => '3.25.04.02.02.0004', 'nomenklatur' => 'Pemberian Pendampingan, Kemudahan Akses Ilmu Pengetahuan, Teknologi dan Informasi, serta Penyelenggaraan Pendidikan dan Pelatihan'],

            // -- 3.25.04.02.04 Pengelolaan Pembudidayaan Ikan
            ['nomor_kode' => '3.25.04.02.04.0001', 'nomenklatur' => 'Penyediaan Data dan Informasi Pembudidayaan Ikan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '3.25.04.02.04.0002', 'nomenklatur' => 'Penyediaan Prasarana Pembudidayaan Ikan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '3.25.04.02.04.0009', 'nomenklatur' => 'Penjaminan Ketersediaan Sarana Pembudidayaan Ikan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '3.25.04.02.04.0010', 'nomenklatur' => 'Pembinaan dan Pemantauan Pembudidayaan Ikan di Darat'],
            ['nomor_kode' => '3.25.04.02.04.0011', 'nomenklatur' => 'Perencanaan, dan Pengembangan Pemanfaatan Air untuk Pembudidayaan Ikan di Darat'],

            // =========================
            // 3.25.06 — PROGRAM PENGOLAHAN DAN PEMASARAN HASIL PERIKANAN
            // =========================

            // -- 3.25.06.02.01 Penerbitan Tanda Daftar Usaha Pengolahan...
            ['nomor_kode' => '3.25.06.02.01.0005', 'nomenklatur' => 'Penyediaan Data dan Informasi Usaha Pemasaran dan Pengolahan Hasil Perikanan dalam 1 (Satu) Daerah Kabupaten/Kota berdasarkan skala usaha dan risiko'],

            // -- 3.25.06.02.02 Pembinaan Mutu dan Keamanan Hasil Perikanan...
            ['nomor_kode' => '3.25.06.02.02.0002', 'nomenklatur' => 'Pembinaan terhadap Penerapan Persyaratan Perizinan Berusaha Pada Usaha Pengolahan dan Pemasaran Hasil Perikanan sesuai Skala Usaha dan Risiko'],

            // -- 3.25.06.02.03 Penyediaan dan Penyaluran Bahan Baku Industri Pengolahan Ikan...
            ['nomor_kode' => '3.25.06.02.03.0001', 'nomenklatur' => 'Peningkatan Ketersediaan Ikan untuk Konsumsi dan Usaha Pengolahan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '3.25.06.02.03.0002', 'nomenklatur' => 'Pemberian Fasilitas bagi Pelaku Usaha Perikanan Skala Mikro dan Kecil dalam 1 (Satu) Daerah Kabupaten/Kota'],

            // =========================
            // 3.26 — URUSAN PEMERINTAHAN BIDANG PARIWISATA
            // =========================

            // 3.26.02 — PROGRAM PENINGKATAN DAYA TARIK DESTINASI PARIWISATA
            // -- 3.26.02.02.02 Pengelolaan Kawasan Strategis Pariwisata
            ['nomor_kode' => '3.26.02.02.02.0004', 'nomenklatur' => 'Pengadaan/Pemeliharaan/Rehabilitasi Sarana dan Prasarana dalam Pengelolaan Kawasan Wisata Strategis Pariwisata Kabupaten/Kota'],
            ['nomor_kode' => '3.26.02.02.02.0008', 'nomenklatur' => 'Peningkatan Kapasitas SDM Pengelola Kawasan Strategis Pariwisata Kabupaten/Kota'],

            // -- 3.26.02.02.03 Pengelolaan Destinasi Pariwisata
            ['nomor_kode' => '3.26.02.02.03.0004', 'nomenklatur' => 'Pengadaan/Pemeliharaan/Rehabilitasi Sarana dan Prasarana dalam Pengelolaan Destinasi Pariwisata Kabupaten/Kota'],
            ['nomor_kode' => '3.26.02.02.03.0006', 'nomenklatur' => 'Pemberdayaan Masyarakat dalam Pengelolaan Destinasi Pariwisata Kabupaten/Kota'],

            // -- 3.26.02.02.04 Penetapan Tanda Daftar Usaha Pariwisata Daerah
            ['nomor_kode' => '3.26.02.02.04.0007', 'nomenklatur' => 'Pembinaan dan Pengawasan untuk memastikan Kepatuhan Pelaku Usaha Melaksanakan Standar Usaha Risiko Menengah Rendah di Kabupaten/Kota'],

            // 3.26.03 — PROGRAM PEMASARAN PARIWISATA
            // -- 3.26.03.02.01 Pemasaran Pariwisata Dalam dan Luar Negeri...
            ['nomor_kode' => '3.26.03.02.01.0004', 'nomenklatur' => 'Peningkatan Kerja Sama dan Kemitraan Pariwisata Dalam dan Luar Negeri'],
            ['nomor_kode' => '3.26.03.02.01.0006', 'nomenklatur' => 'Fasilitasi Kegiatan Pemasaran Pariwisata Baik Dalam dan Luar Negeri Pariwisata Kabupaten/Kota'],
            ['nomor_kode' => '3.26.03.02.01.0007', 'nomenklatur' => 'Penguatan Promosi Melalui Media Cetak, Elektronik, dan Media Lainnya Baik Dalam dan Luar Negeri'],

            // 3.26.04 — PROGRAM PENGEMBANGAN EKONOMI KREATIF...
            // -- 3.26.04.02.01 Penyediaan Prasarana (Zona/Ruang/Kota Kreatif)...
            ['nomor_kode' => '3.26.04.02.01.0001', 'nomenklatur' => 'Pengembangan dan Revitalisasi Prasarana Kota Kreatif'],

            // 3.26.05 — PROGRAM PENGEMBANGAN SDM PARIWISATA DAN EKONOMI KREATIF
            // -- 3.26.05.02.01 Pelaksanaan Peningkatan Kapasitas SDM...
            ['nomor_kode' => '3.26.05.02.01.0005', 'nomenklatur' => 'Fasilitasi Proses Kreasi, Produksi, Distribusi Konsumsi dan Konservasi Ekonomi Kreatif'],
            ['nomor_kode' => '3.26.05.02.01.0006', 'nomenklatur' => 'Fasilitasi Pengembangan Kompetensi Sumber Daya Manusia Ekonomi Kreatif'],
            ['nomor_kode' => '3.26.05.02.01.0010', 'nomenklatur' => 'Fasilitasi Sertifikasi Kompetensi bagi Tenaga Kerja Bidang Pariwisata'],

            // -- 3.26.05.02.02 Pengembangan Kapasitas Pelaku Ekonomi Kreatif
            ['nomor_kode' => '3.26.05.02.02.0001', 'nomenklatur' => 'Pelatihan, Bimbingan Teknis, dan Pendampingan Ekonomi Kreatif'],

            // =========================
            // 3.27 — URUSAN PEMERINTAHAN BIDANG PERTANIAN
            // =========================

            // 3.27.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KAB/KOTA
            // -- 3.27.01.02.01 Perencanaan, Penganggaran, dan Evaluasi...
            ['nomor_kode' => '3.27.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.27.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // -- 3.27.01.02.02 Administrasi Keuangan Perangkat Daerah
            ['nomor_kode' => '3.27.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '3.27.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '3.27.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '3.27.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],

            // -- 3.27.01.02.03 Administrasi Barang Milik Daerah pada Perangkat Daerah
            ['nomor_kode' => '3.27.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '3.27.01.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],

            // -- 3.27.01.02.05 Administrasi Kepegawaian Perangkat Daerah
            ['nomor_kode' => '3.27.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '3.27.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '3.27.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '3.27.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],

            // -- 3.27.01.02.06 Administrasi Umum Perangkat Daerah
            ['nomor_kode' => '3.27.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '3.27.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '3.27.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '3.27.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '3.27.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '3.27.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '3.27.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '3.27.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '3.27.01.02.06.0010', 'nomenklatur' => 'Penatausahaan Arsip Dinamis pada SKPD'],

            // -- 3.27.01.02.07 Pengadaan BMD Penunjang Urusan Pemda
            ['nomor_kode' => '3.27.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '3.27.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '3.27.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '3.27.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '3.27.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '3.27.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // -- 3.27.01.02.08 Penyediaan Jasa Penunjang Urusan Pemda
            ['nomor_kode' => '3.27.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '3.27.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '3.27.01.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '3.27.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // -- 3.27.01.02.09 Pemeliharaan BMD Penunjang Urusan Pemda
            ['nomor_kode' => '3.27.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '3.27.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '3.27.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '3.27.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '3.27.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '3.27.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '3.27.01.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],

            // 3.27.02 — PROGRAM PENYEDIAAN & PENGEMBANGAN SARANA PERTANIAN
            // -- 3.27.02.02.01 Pengawasan Penggunaan Sarana Pertanian
            ['nomor_kode' => '3.27.02.02.01.0001', 'nomenklatur' => 'Pengawasan Penggunaan Sarana Pendukung Pertanian Sesuai dengan Komoditas, Teknologi dan Spesifik Lokasi'],
            ['nomor_kode' => '3.27.02.02.01.0002', 'nomenklatur' => 'Pendampingan Penggunaan Sarana Pendukung Pertanian'],

            // -- 3.27.02.02.02 Pengelolaan SDG Hewan/Tumbuhan/Mikroorganisme
            ['nomor_kode' => '3.27.02.02.02.0002', 'nomenklatur' => 'Peningkatan Kualitas SDG Hewan/Tanaman'],

            // -- 3.27.02.02.03 Peningkatan Mutu & Peredaran Benih/Bibit Ternak...
            ['nomor_kode' => '3.27.02.02.03.0001', 'nomenklatur' => 'Pengawasan Mutu Benih/Bibit Ternak, Bahan Pakan/Pakan/Tanaman Skala Kecil'],

            // -- 3.27.02.02.05 Pengendalian & Pengawasan Penyediaan/Peredaran Benih/Bibit Ternak...
            ['nomor_kode' => '3.27.02.02.05.0006', 'nomenklatur' => 'Pengawasan Produksi Benih/Bibit Ternak dan HPT, Bahan Pakan/Pakan'],

            // -- 3.27.02.02.06 Penyediaan Benih/Bibit & Hijauan Pakan Ternak...
            ['nomor_kode' => '3.27.02.02.06.0002', 'nomenklatur' => 'Pengadaan Hijauan Pakan Ternak yang Sumbernya dari Daerah Kabupaten/Kota Lain'],
            ['nomor_kode' => '3.27.02.02.06.0004', 'nomenklatur' => 'Pengadaan Benih Ternak yang Sumbernya dari Daerah Kabupaten/Kota Lain'],

            // 3.27.03 — PROGRAM PENYEDIAAN & PENGEMBANGAN PRASARANA PERTANIAN
            // -- 3.27.03.02.01 Pengembangan Prasarana Pertanian
            ['nomor_kode' => '3.27.03.02.01.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Prasarana Pendukung Pertanian Lainnya'],
            ['nomor_kode' => '3.27.03.02.01.0015', 'nomenklatur' => 'Pengelolaan Lahan Pertanian Pangan Berkelanjutan/LP2B, Kawasan Pertanian Pangan Berkelanjutan/KP2B dan Lahan Cadangan Pertanian Pangan Berkelanjutan/LCP2B di Kabupaten/Kota'],

            // -- 3.27.03.02.02 Pembangunan Prasarana Pertanian
            ['nomor_kode' => '3.27.03.02.02.0002', 'nomenklatur' => 'Pembangunan, Rehabilitasi dan Pemeliharaan Embung Pertanian'],
            ['nomor_kode' => '3.27.03.02.02.0003', 'nomenklatur' => 'Pembangunan, Rehabilitasi dan Pemeliharaan Jalan Usaha Tani'],
            ['nomor_kode' => '3.27.03.02.02.0006', 'nomenklatur' => 'Pembangunan, Rehabilitasi dan Pemeliharaan Pintu Air'],
            ['nomor_kode' => '3.27.03.02.02.0008', 'nomenklatur' => 'Pembangunan, Rehabilitasi dan Pemeliharaan Balai Penyuluh di Kecamatan serta Sarana Pendukungnya'],
            ['nomor_kode' => '3.27.03.02.02.0009', 'nomenklatur' => 'Pembangunan, Rehabilitasi dan Pemeliharaan Prasarana Pertanian Lainnya'],
            ['nomor_kode' => '3.27.03.02.02.0010', 'nomenklatur' => 'Rehabilitasi dan Pemeliharaan Jaringan Irigasi Usaha Tani'],
            ['nomor_kode' => '3.27.03.02.02.0015', 'nomenklatur' => 'Pembangunan, Rehabilitasi, Pemeliharaan dan operasionalisasi Rumah Potong Hewan'],

            // -- 3.27.03.02.03 Pengelolaan Wilayah Sumber Bibit...
            ['nomor_kode' => '3.27.03.02.03.0001', 'nomenklatur' => 'Pelestarian dan Pemanfaatan Wilayah Sumber Bibit Ternak dan Rumpun/Galur Ternak'],

            // 3.27.04 — PROGRAM PENGENDALIAN KESEHATAN HEWAN & KESMAVET
            // -- 3.27.04.02.01 Penjaminan Kesehatan Hewan...
            ['nomor_kode' => '3.27.04.02.01.0008', 'nomenklatur' => 'Pemberantasan Penyakit Hewan Menular dan Zoonosis dalam 1 (satu) Daerah Kabupaten/Kota'],

            // -- 3.27.04.02.02 Pengawasan Pemasukan & Pengeluaran Hewan/Produk Hewan
            ['nomor_kode' => '3.27.04.02.02.0004', 'nomenklatur' => 'Pengawasan atas Penerapan Persyaratan Teknis untuk Pemasukan dan/atau Pengeluaran Hewan, Produk Hewan dan Media Pembawa Penyakit Hewan Lainnya (HPM)'],
            ['nomor_kode' => '3.27.04.02.02.0006', 'nomenklatur' => 'Pengawasan dan Pemeriksaan Kesehatan Hewan, Produk Hewan dan Media Pembawa Penyakit Hewan Lainnya (HPM) di Perbatasan Tempat Pemeriksaan HPM'],

            // -- 3.27.04.02.03 Pengelolaan Pelayanan Jasa Lab & Medik Veteriner
            ['nomor_kode' => '3.27.04.02.03.0001', 'nomenklatur' => 'Penyediaan Pelayanan Jasa Laboratorium'],
            ['nomor_kode' => '3.27.04.02.03.0002', 'nomenklatur' => 'Penyediaan Pelayanan Jasa Medik Veteriner'],

            // -- 3.27.04.02.04 Penerapan & Pengawasan Persyaratan Teknis Kesmavet
            ['nomor_kode' => '3.27.04.02.04.0004', 'nomenklatur' => 'Pengujian Laboratorium Kesehatan Masyarakat Veteriner'],
            ['nomor_kode' => '3.27.04.02.04.0005', 'nomenklatur' => 'Pembinaan Penerapan persyaratan higiene sanitasi pada unit usaha produk hewan'],

            // 3.27.05 — PROGRAM PENGENDALIAN & PENANGGULANGAN BENCANA PERTANIAN
            // -- 3.27.05.02.01 Pengendalian & Penanggulangan Bencana Pertanian
            ['nomor_kode' => '3.27.05.02.01.0001', 'nomenklatur' => 'Pengendalian Organisme Pengganggu Tumbuhan (OPT) Tanaman Pangan, Hortikultura, dan Perkebunan'],
            ['nomor_kode' => '3.27.05.02.01.0004', 'nomenklatur' => 'Penanggulangan Bencana Non Alam yang Bersifat Zoonosis'],
            ['nomor_kode' => '3.27.05.02.01.0006', 'nomenklatur' => 'Penanggulangan Pasca Bencana Alam Bidang Tanaman Pangan, Hortikultura dan Perkebunan'],

            // 3.27.07 — PROGRAM PENYULUHAN PERTANIAN
            // -- 3.27.07.02.01 Pelaksanaan Penyuluhan Pertanian
            ['nomor_kode' => '3.27.07.02.01.0001', 'nomenklatur' => 'Peningkatan Kapasitas Kelembagaan Penyuluhan Pertanian di Kecamatan dan Desa'],
            ['nomor_kode' => '3.27.07.02.01.0002', 'nomenklatur' => 'Pengembangan Kapasitas Kelembagaan Petani di Kecamatan dan Desa'],
            ['nomor_kode' => '3.27.07.02.01.0003', 'nomenklatur' => 'Penyediaan dan Pemanfaatan Sarana dan Prasarana Penyuluhan Pertanian'],
            ['nomor_kode' => '3.27.07.02.01.0006', 'nomenklatur' => 'Penyediaan dan Peningkatan Kapasitas Penyuluh Pertanian'],

            // =========================
            // 3.30 — URUSAN PEMERINTAHAN BIDANG PERDAGANGAN
            // =========================

            // 3.30.01 — PROGRAM PENUNJANG URUSAN PEMDA KAB/KOTA
            // -- 3.30.01.02.01 Perencanaan, Penganggaran, dan Evaluasi...
            ['nomor_kode' => '3.30.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.30.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // -- 3.30.01.02.02 Administrasi Keuangan Perangkat Daerah
            ['nomor_kode' => '3.30.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '3.30.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '3.30.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '3.30.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],

            // -- 3.30.01.02.03 Administrasi BMD pada Perangkat Daerah
            ['nomor_kode' => '3.30.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],

            // -- 3.30.01.02.04 Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah
            ['nomor_kode' => '3.30.01.02.04.0003', 'nomenklatur' => 'Penyuluhan dan Penyebarluasan Kebijakan Retribusi Daerah'],
            ['nomor_kode' => '3.30.01.02.04.0004', 'nomenklatur' => 'Pendataan dan Pendaftaran Objek Retribusi Daerah'],

            // -- 3.30.01.02.05 Administrasi Kepegawaian Perangkat Daerah
            ['nomor_kode' => '3.30.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],

            // -- 3.30.01.02.06 Administrasi Umum Perangkat Daerah
            ['nomor_kode' => '3.30.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '3.30.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '3.30.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '3.30.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '3.30.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '3.30.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '3.30.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '3.30.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // -- 3.30.01.02.07 Pengadaan BMD Penunjang Urusan Pemda
            ['nomor_kode' => '3.30.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '3.30.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '3.30.01.02.07.0007', 'nomenklatur' => 'Pengadaan Aset Tetap Lainnya'],
            ['nomor_kode' => '3.30.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '3.30.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // -- 3.30.01.02.08 Penyediaan Jasa Penunjang Urusan Pemda
            ['nomor_kode' => '3.30.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '3.30.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '3.30.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // -- 3.30.01.02.09 Pemeliharaan BMD Penunjang Urusan Pemda
            ['nomor_kode' => '3.30.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '3.30.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '3.30.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '3.30.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '3.30.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // 3.30.02 — PROGRAM PERIZINAN DAN PENDAFTARAN PERUSAHAAN
            // -- 3.30.02.02.01 Penerbitan Izin Pengelolaan Pasar Rakyat...
            ['nomor_kode' => '3.30.02.02.01.0001', 'nomenklatur' => 'Fasilitasi Pemenuhan Komitmen Perolehan Perizinan Pasar Rakyat, Pusat Perbelanjaan, dan Toko Swalayan Melalui Sistem Pelayanan Perizinan Berusaha Terintegrasi Secara Elektronik'],

            // 3.30.03 — PROGRAM PENINGKATAN SARANA DISTRIBUSI PERDAGANGAN
            // -- 3.30.03.02.01 Pembangunan dan Pengelolaan Sarana Distribusi Perdagangan
            ['nomor_kode' => '3.30.03.02.01.0001', 'nomenklatur' => 'Penyediaan Sarana Distribusi Perdagangan'],
            ['nomor_kode' => '3.30.03.02.01.0002', 'nomenklatur' => 'Fasilitasi Pengelolaan Sarana Distribusi Perdagangan'],

            // -- 3.30.03.02.02 Pembinaan terhadap Pengelola Sarana Distribusi Perdagangan...
            ['nomor_kode' => '3.30.03.02.02.0001', 'nomenklatur' => 'Pembinaan dan Pengendalian Pengelola Sarana Distribusi Perdagangan'],
            ['nomor_kode' => '3.30.03.02.02.0002', 'nomenklatur' => 'Pemberdayaan Pengelola Sarana Distribusi Perdagangan'],

            // 3.30.04 — PROGRAM STABILISASI HARGA BAPOKTING
            // -- 3.30.04.02.01 Menjamin Ketersediaan Barang Kebutuhan Pokok dan Barang Penting...
            ['nomor_kode' => '3.30.04.02.01.0001', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Ketersediaan Barang Kebutuhan Pokok dan Barang Penting di Tingkat Agen dan Pasar Rakyat'],
            ['nomor_kode' => '3.30.04.02.01.0003', 'nomenklatur' => 'Pengendalian Ketersediaan Barang Kebutuhan Pokok dan Barang Penting di Tingkat Agen dan Pasar Rakyat'],

            // -- 3.30.04.02.02 Pengendalian Harga dan Stok...
            ['nomor_kode' => '3.30.04.02.02.0002', 'nomenklatur' => 'Pemantauan Harga dan Stok Barang Kebutuhan Pokok dan Barang Penting pada Pasar Rakyat yang Terintegrasi dalam Sistem Informasi Perdagangan'],
            ['nomor_kode' => '3.30.04.02.02.0003', 'nomenklatur' => 'Pelaksanaan Operasi Pasar Reguler dan Pasar Khusus yang Berdampak dalam 1 (Satu) Kabupaten/Kota'],

            // -- 3.30.04.02.03 Pengawasan Pupuk dan Pestisida Bersubsidi
            ['nomor_kode' => '3.30.04.02.03.0003', 'nomenklatur' => 'Pengawasan Penyaluran dan Penggunaan Pupuk dan Pestisida Bersubsidi'],

            // 3.30.05 — PROGRAM PENGEMBANGAN EKSPOR
            // -- 3.30.05.02.01 Penyelenggaraan Promosi Dagang...
            ['nomor_kode' => '3.30.05.02.01.0003', 'nomenklatur' => 'Pameran Dagang Lokal'],

            // 3.30.06 — PROGRAM STANDARDISASI DAN PERLINDUNGAN KONSUMEN
            // -- 3.30.06.02.01 Pelaksanaan Metrologi Legal, Berupa Tera, Tera Ulang, dan Pengawasan
            ['nomor_kode' => '3.30.06.02.01.0001', 'nomenklatur' => 'Pelaksanaan Metrologi Legal, Berupa Tera, Tera Ulang'],
            ['nomor_kode' => '3.30.06.02.01.0002', 'nomenklatur' => 'Pengawasan/Penyuluhan Metrologi Legal'],
            ['nomor_kode' => '3.30.06.02.01.0003', 'nomenklatur' => 'Penyidikan Metrologi Legal'],

            // =========================
            // 3.31 — URUSAN PEMERINTAHAN BIDANG PERINDUSTRIAN
            // =========================

            // 3.31.02 — PROGRAM PERENCANAAN DAN PEMBANGUNAN INDUSTRI
            // -- 3.31.02.02.01 Penyusunan & Evaluasi Renbang Industri Kab/Kota
            ['nomor_kode' => '3.31.02.02.01.0001', 'nomenklatur' => 'Penyusunan Rencana Pembangunan Industri Kabupaten/Kota'],
            ['nomor_kode' => '3.31.02.02.01.0002', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan Pelaksanaan Kebijakan Percepatan Pengembangan, Penyebaran dan Perwilayahan Industri'],
            ['nomor_kode' => '3.31.02.02.01.0003', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan Pelaksanaan Pembangunan Sumber Daya Industri'],
            ['nomor_kode' => '3.31.02.02.01.0004', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan Pelaksanaan Pembangunan Sarana dan Prasarana Industri'],
            ['nomor_kode' => '3.31.02.02.01.0005', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan Pelaksanaan Pemberdayaan Industri dan Peran Serta Masyarakat'],

            // 3.31.03 — PROGRAM PENGENDALIAN IZIN USAHA INDUSTRI
            // -- 3.31.03.02.01 Penerbitan IUI, IPUI, IUKI, IPKI Kewenangan Kab/Kota
            ['nomor_kode' => '3.31.03.02.01.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pengawasan terhadap Perizinan Berusaha sektor perindustrian yang menjadi kewenangan Kabupaten/Kota'],

            // 3.31.04 — PROGRAM PENGELOLAAN SISTEM INFORMASI INDUSTRI NASIONAL
            // -- 3.31.04.02.01 Penyediaan Informasi Industri untuk IUI, IPUI, IUKI dan IPKI Kewenangan Kab/Kota
            ['nomor_kode' => '3.31.04.02.01.0001', 'nomenklatur' => 'Fasilitasi Pengumpulan, Pengolahan dan Analisis Data Industri, Data Kawasan Industri serta Data Lain Lingkup Kabupaten/Kota Melalui Sistem Informasi Industri Nasional (SIINas)'],

    // =========================
    // 4.01 — URUSAN PEMERINTAHAN DAERAH KAB/KOTA
    // =========================

    // 4.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KAB/KOTA
    // -- 4.01.01.02.01 Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah
    ['nomor_kode' => '4.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '4.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // -- 4.01.01.02.02 Administrasi Keuangan Perangkat Daerah
            ['nomor_kode' => '4.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '4.01.01.02.02.0002', 'nomenklatur' => 'Penyediaan Administrasi Pelaksanaan Tugas ASN'],
            ['nomor_kode' => '4.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '4.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '4.01.01.02.02.0008', 'nomenklatur' => 'Penyusunan Pelaporan dan Analisis Prognosis Realisasi Anggaran'],

            // -- 4.01.01.02.03 Administrasi Barang Milik Daerah pada Perangkat Daerah
            ['nomor_kode' => '4.01.01.02.03.0002', 'nomenklatur' => 'Pengamanan Barang Milik Daerah SKPD'],

            // -- 4.01.01.02.05 Administrasi Kepegawaian Perangkat Daerah
            ['nomor_kode' => '4.01.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '4.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '4.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '4.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],

            // -- 4.01.01.02.06 Administrasi Umum Perangkat Daerah
            ['nomor_kode' => '4.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '4.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '4.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '4.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '4.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '4.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '4.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // -- 4.01.01.02.07 Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah
            ['nomor_kode' => '4.01.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '4.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '4.01.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // -- 4.01.01.02.08 Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah
            ['nomor_kode' => '4.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '4.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '4.01.01.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '4.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // -- 4.01.01.02.09 Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah
            ['nomor_kode' => '4.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '4.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '4.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '4.01.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '4.01.01.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],

            // -- 4.01.01.02.11 Administrasi Keuangan dan Operasional Kepala Daerah dan Wakil Kepala Daerah
            ['nomor_kode' => '4.01.01.02.11.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan Kepala Daerah dan Wakil Kepala Daerah'],
            ['nomor_kode' => '4.01.01.02.11.0002', 'nomenklatur' => 'Penyediaan Pakaian Dinas dan Atribut Kelengkapan Kepala Daerah dan Wakil Kepala Daerah'],
            ['nomor_kode' => '4.01.01.02.11.0003', 'nomenklatur' => 'Pelaksanaan Medical Check Up Kepala Daerah dan Wakil Kepala Daerah'],
            ['nomor_kode' => '4.01.01.02.11.0004', 'nomenklatur' => 'Penyediaan Dana Penunjang Operasional Kepala Daerah dan Wakil Kepala Daerah'],

            // -- 4.01.01.02.12 Fasilitasi Kerumahtanggaan Sekretariat Daerah
            ['nomor_kode' => '4.01.01.02.12.0001', 'nomenklatur' => 'Penyediaan Kebutuhan Rumah Tangga Kepala Daerah'],
            ['nomor_kode' => '4.01.01.02.12.0002', 'nomenklatur' => 'Penyediaan Kebutuhan Rumah Tangga Wakil Kepala Daerah'],
            ['nomor_kode' => '4.01.01.02.12.0003', 'nomenklatur' => 'Penyediaan Kebutuhan Rumah Tangga Sekretariat Daerah'],

            // -- 4.01.01.02.13 Penataan Organisasi
            ['nomor_kode' => '4.01.01.02.13.0001', 'nomenklatur' => 'Pengelolaan Kelembagaan dan Analisis Jabatan'],
            ['nomor_kode' => '4.01.01.02.13.0002', 'nomenklatur' => 'Fasilitasi Pelayanan Publik dan Tata Laksana'],
            ['nomor_kode' => '4.01.01.02.13.0003', 'nomenklatur' => 'Peningkatan Kinerja dan Reformasi Birokrasi'],
            ['nomor_kode' => '4.01.01.02.13.0004', 'nomenklatur' => 'Monitoring, Evaluasi dan Pengendalian Kualitas Pelayanan Publik dan Tata Laksana'],

            // -- 4.01.01.02.14 Pelaksanaan Protokol dan Komunikasi Pimpinan
            ['nomor_kode' => '4.01.01.02.14.0001', 'nomenklatur' => 'Fasilitasi Keprotokolan'],
            ['nomor_kode' => '4.01.01.02.14.0002', 'nomenklatur' => 'Fasilitasi Komunikasi Pimpinan'],
            ['nomor_kode' => '4.01.01.02.14.0003', 'nomenklatur' => 'Pendokumentasian Tugas Pimpinan'],

            // =========================
            // 4.01.02 — PROGRAM PEMERINTAHAN DAN KESEJAHTERAAN RAKYAT
            // =========================

            // -- 4.01.02.02.01 Administrasi Tata Pemerintahan
            ['nomor_kode' => '4.01.02.02.01.0001', 'nomenklatur' => 'Penataan Administrasi Pemerintahan'],
            ['nomor_kode' => '4.01.02.02.01.0002', 'nomenklatur' => 'Pengelolaan Administrasi Kewilayahan'],
            ['nomor_kode' => '4.01.02.02.01.0003', 'nomenklatur' => 'Fasilitasi Pelaksanaan Otonomi Daerah'],

            // -- 4.01.02.02.02 Pelaksanaan Kebijakan Kesejahteraan Rakyat
            ['nomor_kode' => '4.01.02.02.02.0001', 'nomenklatur' => 'Fasilitasi Pengelolaan Bina Mental Spiritual'],
            ['nomor_kode' => '4.01.02.02.02.0003', 'nomenklatur' => 'Pelaksanaan Kebijakan, Evaluasi, dan Capaian Kinerja Terkait Kesejahteraan Masyarakat'],

            // -- 4.01.02.02.03 Fasilitasi dan Koordinasi Hukum
            ['nomor_kode' => '4.01.02.02.03.0001', 'nomenklatur' => 'Fasilitasi Penyusunan Produk Hukum Daerah'],
            ['nomor_kode' => '4.01.02.02.03.0002', 'nomenklatur' => 'Fasilitasi Bantuan Hukum'],
            ['nomor_kode' => '4.01.02.02.03.0003', 'nomenklatur' => 'Pendokumentasian Produk Hukum dan Pengelolaan Informasi Hukum'],

            // -- 4.01.02.02.04 Fasilitasi Kerja Sama Daerah
            ['nomor_kode' => '4.01.02.02.04.0001', 'nomenklatur' => 'Fasilitasi Kerja Sama Dalam Negeri'],

            // =========================
            // 4.01.03 — PROGRAM PEREKONOMIAN DAN PEMBANGUNAN
            // =========================

            // -- 4.01.03.02.01 Pelaksanaan Kebijakan Perekonomian
            ['nomor_kode' => '4.01.03.02.01.0001', 'nomenklatur' => 'Koordinasi, Sinkronisasi, Monitoring dan Evaluasi Kebijakan Pengelolaan BUMD dan BLUD'],
            ['nomor_kode' => '4.01.03.02.01.0002', 'nomenklatur' => 'Pengendalian dan Distribusi Perekonomian'],

            // -- 4.01.03.02.02 Pelaksanaan Administrasi Pembangunan
            ['nomor_kode' => '4.01.03.02.02.0001', 'nomenklatur' => 'Fasilitasi Penyusunan Program Pembangunan'],
            ['nomor_kode' => '4.01.03.02.02.0002', 'nomenklatur' => 'Pengendalian dan Evaluasi Program Pembangunan'],
            ['nomor_kode' => '4.01.03.02.02.0003', 'nomenklatur' => 'Pengelolaan Evaluasi dan Pelaporan Pelaksanaan Pembangunan'],

            // -- 4.01.03.02.03 Pengelolaan Pengadaan Barang dan Jasa
            ['nomor_kode' => '4.01.03.02.03.0001', 'nomenklatur' => 'Pengelolaan Pengadaan Barang dan Jasa'],
            ['nomor_kode' => '4.01.03.02.03.0002', 'nomenklatur' => 'Pengelolaan Layanan Pengadaan Secara Elektronik'],
            ['nomor_kode' => '4.01.03.02.03.0003', 'nomenklatur' => 'Pembinaan dan Advokasi Pengadaan Barang dan Jasa'],

            // -- 4.01.03.02.04 Pemantauan Kebijakan Sumber Daya Alam
            ['nomor_kode' => '4.01.03.02.04.0003', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Evaluasi Kebijakan Energi dan Air'],

            // =========================
            // 4.02.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KAB/KOTA (SEKRETARIAT DPRD)
            // =========================

            // -- 4.02.01.02.01 Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah
            ['nomor_kode' => '4.02.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '4.02.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // -- 4.02.01.02.02 Administrasi Keuangan Perangkat Daerah
            ['nomor_kode' => '4.02.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '4.02.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '4.02.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '4.02.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '4.02.01.02.02.0006', 'nomenklatur' => 'Pengelolaan dan Penyiapan Bahan Tanggapan Pemeriksaan'],
            ['nomor_kode' => '4.02.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '4.02.01.02.02.0008', 'nomenklatur' => 'Penyusunan Pelaporan dan Analisis Prognosis Realisasi Anggaran'],

            // -- 4.02.01.02.03 Administrasi Barang Milik Daerah pada Perangkat Daerah
            ['nomor_kode' => '4.02.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],

            // -- 4.02.01.02.05 Administrasi Kepegawaian Perangkat Daerah
            ['nomor_kode' => '4.02.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '4.02.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '4.02.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '4.02.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],

            // -- 4.02.01.02.06 Administrasi Umum Perangkat Daerah
            ['nomor_kode' => '4.02.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '4.02.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '4.02.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '4.02.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '4.02.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '4.02.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '4.02.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '4.02.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '4.02.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // -- 4.02.01.02.07 Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah
            ['nomor_kode' => '4.02.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '4.02.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '4.02.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '4.02.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '4.02.01.02.07.0007', 'nomenklatur' => 'Pengadaan Aset Tetap Lainnya'],
            ['nomor_kode' => '4.02.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '4.02.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '4.02.01.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],

            // -- 4.02.01.02.08 Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah
            ['nomor_kode' => '4.02.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '4.02.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '4.02.01.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '4.02.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // -- 4.02.01.02.09 Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah
            ['nomor_kode' => '4.02.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '4.02.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '4.02.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '4.02.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '4.02.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '4.02.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '4.02.01.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],

            // =========================
            // 4.02.02 — PROGRAM DUKUNGAN PELAKSANAAN TUGAS DAN FUNGSI DPRD
            // =========================

            // -- 4.02.02.02.01 Pembentukan Peraturan Daerah dan Peraturan DPRD
            ['nomor_kode' => '4.02.02.02.01.0001', 'nomenklatur' => 'Penyusunan dan Pembahasan Program Pembentukan Peraturan Daerah'],
            ['nomor_kode' => '4.02.02.02.01.0002', 'nomenklatur' => 'Pembahasan Rancangan Peraturan Daerah'],
            ['nomor_kode' => '4.02.02.02.01.0003', 'nomenklatur' => 'Penyelenggaraan Kajian Perundang-Undangan'],
            ['nomor_kode' => '4.02.02.02.01.0004', 'nomenklatur' => 'Fasilitasi Penyusunan Penjelasan/Keterangan Naskah Akademik'],
            ['nomor_kode' => '4.02.02.02.01.0006', 'nomenklatur' => 'Sosialisasi Peraturan Daerah yang Dilakukan Bersama oleh DPRD dan Pemerintah Daerah'],

            // -- 4.02.02.02.02 Pembahasan Kebijakan Anggaran
            ['nomor_kode' => '4.02.02.02.02.0001', 'nomenklatur' => 'Pembahasan KUA dan PPAS'],
            ['nomor_kode' => '4.02.02.02.02.0002', 'nomenklatur' => 'Pembahasan Perubahan KUA dan Perubahan PPAS'],
            ['nomor_kode' => '4.02.02.02.02.0003', 'nomenklatur' => 'Pembahasan APBD'],
            ['nomor_kode' => '4.02.02.02.02.0004', 'nomenklatur' => 'Pembahasan APBD Perubahan'],
            ['nomor_kode' => '4.02.02.02.02.0005', 'nomenklatur' => 'Pembahasan Laporan Semester'],
            ['nomor_kode' => '4.02.02.02.02.0006', 'nomenklatur' => 'Pembahasan Pertanggungjawaban APBD'],

            // -- 4.02.02.02.03 Pengawasan Penyelenggaraan Pemerintahan
            ['nomor_kode' => '4.02.02.02.03.0001', 'nomenklatur' => 'Pengawasan Urusan Pemerintahan Bidang Pemerintahan dan Hukum'],
            ['nomor_kode' => '4.02.02.02.03.0003', 'nomenklatur' => 'Pengawasan Urusan Pemerintahan Bidang Kesejahteraan Rakyat'],
            ['nomor_kode' => '4.02.02.02.03.0004', 'nomenklatur' => 'Pengawasan Urusan Pemerintahan Bidang Perekonomian'],
            ['nomor_kode' => '4.02.02.02.03.0008', 'nomenklatur' => 'Pembahasan Laporan Keterangan Pertanggungjawaban Kepala Daerah'],

            // -- 4.02.02.02.04 Peningkatan Kapasitas DPRD
            ['nomor_kode' => '4.02.02.02.04.0002', 'nomenklatur' => 'Pendalaman Tugas DPRD'],
            ['nomor_kode' => '4.02.02.02.04.0004', 'nomenklatur' => 'Penyediaan Kelompok Pakar dan Tim Ahli'],
            ['nomor_kode' => '4.02.02.02.04.0005', 'nomenklatur' => 'Penyediaan Tenaga Ahli Fraksi'],
            ['nomor_kode' => '4.02.02.02.04.0006', 'nomenklatur' => 'Penyelenggaraan Hubungan Masyarakat'],
            ['nomor_kode' => '4.02.02.02.04.0007', 'nomenklatur' => 'Penyusunan Program Kerja DPRD'],
            ['nomor_kode' => '4.02.02.02.04.0008', 'nomenklatur' => 'Publikasi dan Dokumentasi DPRD'],

            // -- 4.02.02.02.05 Penyerapan dan Penghimpunan Aspirasi Masyarakat
            ['nomor_kode' => '4.02.02.02.05.0001', 'nomenklatur' => 'Kunjungan Kerja dalam Daerah'],
            ['nomor_kode' => '4.02.02.02.05.0002', 'nomenklatur' => 'Penyusunan Pokok-Pokok Pikiran DPRD'],
            ['nomor_kode' => '4.02.02.02.05.0003', 'nomenklatur' => 'Pelaksanaan Reses'],

            // -- 4.02.02.02.06 Pelaksanaan dan Pengawasan Kode Etik DPRD
            ['nomor_kode' => '4.02.02.02.06.0002', 'nomenklatur' => 'Pengawasan Kode Etik DPRD'],

            // -- 4.02.02.02.08 Fasilitasi Tugas DPRD
            ['nomor_kode' => '4.02.02.02.08.0001', 'nomenklatur' => 'Koordinasi dan Konsultasi Pelaksanaan Tugas DPRD'],
            ['nomor_kode' => '4.02.02.02.08.0002', 'nomenklatur' => 'Penyusunan Laporan Kinerja DPRD'],
            ['nomor_kode' => '4.02.02.02.08.0003', 'nomenklatur' => 'Fasilitasi Pelaksanaan Tugas Badan Musyawarah'],
            ['nomor_kode' => '4.02.02.02.08.0004', 'nomenklatur' => 'Fasilitasi Tugas Pimpinan DPRD'],

                    // ===== Program 5.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 5.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // --- Kegiatan 5.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '5.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '5.01.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '5.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '5.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],

            // --- Kegiatan 5.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '5.01.01.02.05.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Sistem Informasi Kepegawaian'],
            ['nomor_kode' => '5.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '5.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '5.01.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],

            // --- Kegiatan 5.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '5.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '5.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '5.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '5.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '5.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '5.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // --- Kegiatan 5.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '5.01.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '5.01.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '5.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],

            // --- Kegiatan 5.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '5.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '5.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '5.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // --- Kegiatan 5.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '5.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '5.01.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '5.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '5.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '5.01.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 5.01.02 — PROGRAM PERENCANAAN, PENGENDALIAN DAN EVALUASI PEMBANGUNAN DAERAH =====
            // --- Kegiatan 5.01.02.02.01 — Penyusunan Perencanaan dan Pendanaan ---
            ['nomor_kode' => '5.01.02.02.01.0001', 'nomenklatur' => 'Analisis Kondisi Daerah, Permasalahan, dan Isu Strategis Pembangunan Daerah'],
            ['nomor_kode' => '5.01.02.02.01.0002', 'nomenklatur' => 'Koordinasi Penelaahan Dokumen Perencanaan Pembangunan Daerah dengan Dokumen Kebijakan Lainnya'],
            ['nomor_kode' => '5.01.02.02.01.0003', 'nomenklatur' => 'Pelaksanaan Konsultasi Publik'],
            ['nomor_kode' => '5.01.02.02.01.0004', 'nomenklatur' => 'Koordinasi Pelaksanaan Forum Perangkat Daerah/Lintas Perangkat Daerah'],
            ['nomor_kode' => '5.01.02.02.01.0005', 'nomenklatur' => 'Pelaksanaan Musrenbang Kabupaten/Kota'],
            ['nomor_kode' => '5.01.02.02.01.0006', 'nomenklatur' => 'Penyiapan Bahan Koordinasi Musrenbang Kecamatan'],
            ['nomor_kode' => '5.01.02.02.01.0007', 'nomenklatur' => 'Koordinasi Penyusunan dan Penetapan Dokumen Perencanaan Pembangunan Daerah Kabupaten/Kota'],

            // --- Kegiatan 5.01.02.02.02 — Analisis Data dan Informasi Pemerintahan Daerah Bidang Perencanaan Pembangunan Daerah ---
            ['nomor_kode' => '5.01.02.02.02.0001', 'nomenklatur' => 'Analisis Data dan Informasi Perencanaan Pembangunan Daerah'],

            // --- Kegiatan 5.01.02.02.03 — Pengendalian, Evaluasi dan Pelaporan Bidang Perencanaan Pembangunan Daerah ---
            ['nomor_kode' => '5.01.02.02.03.0001', 'nomenklatur' => 'Koordinasi Pengendalian Perencanaan dan Pelaksanaan Pembangunan Daerah di Kabupaten/Kota'],
            ['nomor_kode' => '5.01.02.02.03.0003', 'nomenklatur' => 'Monitoring, Evaluasi dan Penyusunan Laporan Berkala Pelaksanaan Pembangunan Daerah'],

            // --- Kegiatan 5.01.02.02.04 — Implementasi Sistem Informasi Pemerintahan Daerah di Bidang Pembangunan Daerah ---
            ['nomor_kode' => '5.01.02.02.04.0002', 'nomenklatur' => 'Penerapan Sistem Informasi Pemerintahan Daerah di Bidang Pembangunan Daerah'],

            // ===== Program 5.01.03 — PROGRAM KOORDINASI DAN SINKRONISASI PERENCANAAN PEMBANGUNAN DAERAH =====
            // --- Kegiatan 5.01.03.02.01 — Koordinasi Perencanaan Bidang Pemerintahan dan Pembangunan Manusia ---
            ['nomor_kode' => '5.01.03.02.01.0001', 'nomenklatur' => 'Koordinasi Penyusunan Dokumen Perencanaan Pembangunan Daerah Bidang Pemerintahan (RPJPD, RPJMD dan RKPD)'],
            ['nomor_kode' => '5.01.03.02.01.0002', 'nomenklatur' => 'Asistensi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Pemerintahan'],
            ['nomor_kode' => '5.01.03.02.01.0003', 'nomenklatur' => 'Pelaksanaan Monitoring dan Evaluasi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Pemerintahan'],
            ['nomor_kode' => '5.01.03.02.01.0004', 'nomenklatur' => 'Koordinasi Pelaksanaan Sinergitas dan Harmonisasi Perencanaan Pembangunan Daerah Bidang Pemerintahan'],
            ['nomor_kode' => '5.01.03.02.01.0005', 'nomenklatur' => 'Koordinasi Penyusunan Dokumen Perencanaan Pembangunan Daerah Bidang Pembangunan Manusia (RPJPD, RPJMD dan RKPD)'],
            ['nomor_kode' => '5.01.03.02.01.0006', 'nomenklatur' => 'Asistensi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Pembangunan Manusia'],
            ['nomor_kode' => '5.01.03.02.01.0007', 'nomenklatur' => 'Pelaksanaan Monitoring dan Evaluasi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Pembangunan Manusia'],
            ['nomor_kode' => '5.01.03.02.01.0008', 'nomenklatur' => 'Koordinasi Pelaksanaan Sinergitas dan Harmonisasi Perencanaan Pembangunan Daerah Bidang Pembangunan Manusia'],

            // --- Kegiatan 5.01.03.02.02 — Koordinasi Perencanaan Bidang Perekonomian dan SDA ---
            ['nomor_kode' => '5.01.03.02.02.0001', 'nomenklatur' => 'Koordinasi Penyusunan Dokumen Perencanaan Pembangunan Daerah Bidang Perekonomian (RPJPD, RPJMD dan RKPD)'],
            ['nomor_kode' => '5.01.03.02.02.0002', 'nomenklatur' => 'Asistensi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Perekonomian'],
            ['nomor_kode' => '5.01.03.02.02.0003', 'nomenklatur' => 'Pelaksanaan Monitoring dan Evaluasi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Perekonomian'],
            ['nomor_kode' => '5.01.03.02.02.0004', 'nomenklatur' => 'Koordinasi Pelaksanaan Sinergitas dan Harmonisasi Perencanaan Pembangunan Daerah Bidang Perekonomian'],
            ['nomor_kode' => '5.01.03.02.02.0005', 'nomenklatur' => 'Koordinasi Penyusunan Dokumen Perencanaan Pembangunan Daerah Bidang SDA (RPJPD, RPJMD dan RKPD)'],
            ['nomor_kode' => '5.01.03.02.02.0006', 'nomenklatur' => 'Asistensi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang SDA'],
            ['nomor_kode' => '5.01.03.02.02.0007', 'nomenklatur' => 'Pelaksanaan Monitoring dan Evaluasi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang SDA'],
            ['nomor_kode' => '5.01.03.02.02.0008', 'nomenklatur' => 'Koordinasi Pelaksanaan Sinergitas dan Harmonisasi Perencanaan Pembangunan Daerah Bidang SDA'],

            // --- Kegiatan 5.01.03.02.03 — Koordinasi Perencanaan Bidang Infrastruktur dan Kewilayahan ---
            ['nomor_kode' => '5.01.03.02.03.0001', 'nomenklatur' => 'Koordinasi Penyusunan Dokumen Perencanaan Pembangunan Daerah Bidang Infrastruktur (RPJPD, RPJMD dan RKPD)'],
            ['nomor_kode' => '5.01.03.02.03.0002', 'nomenklatur' => 'Asistensi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Infrastruktur'],
            ['nomor_kode' => '5.01.03.02.03.0003', 'nomenklatur' => 'Pelaksanaan Monitoring dan Evaluasi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Infrastruktur'],
            ['nomor_kode' => '5.01.03.02.03.0004', 'nomenklatur' => 'Koordinasi Pelaksanaan Sinergitas dan Harmonisasi Perencanaan Pembangunan Daerah Bidang Infrastruktur'],
            ['nomor_kode' => '5.01.03.02.03.0005', 'nomenklatur' => 'Koordinasi Penyusunan Dokumen Perencanaan Pembangunan Daerah Bidang Kewilayahan (RPJPD, RPJMD dan RKPD)'],
            ['nomor_kode' => '5.01.03.02.03.0006', 'nomenklatur' => 'Asistensi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Kewilayahan'],
            ['nomor_kode' => '5.01.03.02.03.0007', 'nomenklatur' => 'Pelaksanaan Monitoring dan Evaluasi Penyusunan Dokumen Perencanaan Pembangunan Perangkat Daerah Bidang Kewilayahan'],
            ['nomor_kode' => '5.01.03.02.03.0008', 'nomenklatur' => 'Koordinasi Pelaksanaan Sinergitas dan Harmonisasi Perencanaan Pembangunan Daerah Bidang Kewilayahan'],

            // ===== Program 5.02.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA (KEUANGAN) =====
            // --- Kegiatan 5.02.01.02.01 ---
            ['nomor_kode' => '5.02.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.02.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // --- Kegiatan 5.02.01.02.02 ---
            ['nomor_kode' => '5.02.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '5.02.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '5.02.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '5.02.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '5.02.01.02.02.0006', 'nomenklatur' => 'Pengelolaan dan Penyiapan Bahan Tanggapan Pemeriksaan'],
            ['nomor_kode' => '5.02.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '5.02.01.02.02.0008', 'nomenklatur' => 'Penyusunan Pelaporan dan Analisis Prognosis Realisasi Anggaran'],

            // --- Kegiatan 5.02.01.02.03 ---
            ['nomor_kode' => '5.02.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '5.02.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],

            // --- Kegiatan 5.02.01.02.05 ---
            ['nomor_kode' => '5.02.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '5.02.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],

            // --- Kegiatan 5.02.01.02.06 ---
            ['nomor_kode' => '5.02.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '5.02.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '5.02.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '5.02.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '5.02.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '5.02.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '5.02.01.02.06.0010', 'nomenklatur' => 'Penatausahaan Arsip Dinamis pada SKPD'],
            ['nomor_kode' => '5.02.01.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],

            // --- Kegiatan 5.02.01.02.07 ---
            ['nomor_kode' => '5.02.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '5.02.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '5.02.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // --- Kegiatan 5.02.01.02.08 ---
            ['nomor_kode' => '5.02.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '5.02.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '5.02.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // --- Kegiatan 5.02.01.02.09 ---
            ['nomor_kode' => '5.02.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '5.02.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '5.02.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '5.02.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 5.02.02 — PROGRAM PENGELOLAAN KEUANGAN DAERAH =====
            // --- Kegiatan 5.02.02.02.01 — Koordinasi dan Penyusunan Rencana Anggaran Daerah ---
            ['nomor_kode' => '5.02.02.02.01.0001', 'nomenklatur' => 'Koordinasi dan Penyusunan KUA dan PPAS'],
            ['nomor_kode' => '5.02.02.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan KUA dan Perubahan PPAS'],
            ['nomor_kode' => '5.02.02.02.01.0003', 'nomenklatur' => 'Koordinasi, Penyusunan dan Verifikasi RKA-SKPD'],
            ['nomor_kode' => '5.02.02.02.01.0004', 'nomenklatur' => 'Koordinasi, Penyusunan dan Verifikasi Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.02.02.02.01.0005', 'nomenklatur' => 'Koordinasi, Penyusunan dan Verifikasi DPA-SKPD'],
            ['nomor_kode' => '5.02.02.02.01.0006', 'nomenklatur' => 'Koordinasi, Penyusunan dan Verifikasi Perubahan DPA-SKPD'],
            ['nomor_kode' => '5.02.02.02.01.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Peraturan Daerah tentang APBD dan Peraturan Kepala Daerah tentang Penjabaran APBD'],
            ['nomor_kode' => '5.02.02.02.01.0008', 'nomenklatur' => 'Koordinasi dan Penyusunan Peraturan Daerah tentang Perubahan APBD dan Peraturan Kepala Daerah tentang Penjabaran Perubahan APBD'],
            ['nomor_kode' => '5.02.02.02.01.0011', 'nomenklatur' => 'Koordinasi Perencanaan Anggaran Belanja Daerah'],

            // --- Kegiatan 5.02.02.02.02 — Koordinasi dan Pengelolaan Perbendaharaan Daerah ---
            ['nomor_kode' => '5.02.02.02.02.0001', 'nomenklatur' => 'Koordinasi dan Pengelolaan Kas Daerah'],
            ['nomor_kode' => '5.02.02.02.02.0005', 'nomenklatur' => 'Koordinasi, Fasilitasi, Asistensi, Sinkronisasi, Supervisi, Monitoring dan Evaluasi Pengelolaan Dana Perimbangan dan Dana Transfer Lainnya'],
            ['nomor_kode' => '5.02.02.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Realisasi Penerimaan dan Pengeluaran Kas Daerah, Laporan Aliran Kas, dan Pelaksanaan Pemungutan/ Pemotongan dan Penyetoran Perhitungan Pihak Ketiga (PFK)'],
            ['nomor_kode' => '5.02.02.02.02.0008', 'nomenklatur' => 'Koordinasi Pelaksanaan Piutang dan Utang Daerah yang Timbul Akibat Pengelolaan Kas, Pelaksanaan Analisis Pembiayaan dan Penempatan Uang Daerah sebagai Optimalisasi Kas'],
            ['nomor_kode' => '5.02.02.02.02.0009', 'nomenklatur' => 'Rekonsiliasi Data Penerimaan dan Pengeluaran Kas serta Pemungutan dan Pemotongan atas SP2D dengan Instansi Terkait'],

            // --- Kegiatan 5.02.02.02.03 — Koordinasi dan Pelaksanaan Akuntansi dan Pelaporan Keuangan Daerah ---
            ['nomor_kode' => '5.02.02.02.03.0001', 'nomenklatur' => 'Koordinasi Pelaksanaan Akuntansi Penerimaan dan Pengeluaran Kas Daerah'],
            ['nomor_kode' => '5.02.02.02.03.0002', 'nomenklatur' => 'Rekonsiliasi dan Verifikasi Aset, Kewajiban, Ekuitas, Pendapatan, Belanja, Pembiayaan, Pendapatan-LO dan Beban'],
            ['nomor_kode' => '5.02.02.02.03.0003', 'nomenklatur' => 'Koordinasi Penyusunan Laporan Pertanggungjawaban Pelaksanaan APBD Bulanan, Triwulanan dan Semesteran'],
            ['nomor_kode' => '5.02.02.02.03.0004', 'nomenklatur' => 'Konsolidasi Laporan Keuangan SKPD, BLUD dan Laporan Keuangan Pemerintah Daerah'],
            ['nomor_kode' => '5.02.02.02.03.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Rancangan Peraturan Daerah tentang Pertanggungjawaban Pelaksanaan APBD Kabupaten/Kota dan Rancangan Peraturan Kepala Daerah tentang Penjabaran Pertanggungjawaban Pelaksanaan APBD Kabupaten/Kota'],
            ['nomor_kode' => '5.02.02.02.03.0006', 'nomenklatur' => 'Penyusunan Tanggapan/Tindak Lanjut Terhadap LHP BPK atas Laporan Pertanggungjawaban Pelaksanaan APBD'],
            ['nomor_kode' => '5.02.02.02.03.0011', 'nomenklatur' => 'Pembinaan Akuntansi, Pelaporan dan Pertanggungjawaban Pemerintah Kabupaten/Kota'],

            // --- Kegiatan 5.02.02.02.04 — Penunjang Urusan Kewenangan Pengelolaan Keuangan Daerah ---
            ['nomor_kode' => '5.02.02.02.04.0008', 'nomenklatur' => 'Analisis Perencanaan dan Penyaluran Bantuan Keuangan'],
            ['nomor_kode' => '5.02.02.02.04.0009', 'nomenklatur' => 'Pengelolaan Dana Darurat dan Mendesak'],

            // --- Kegiatan 5.02.02.02.05 — Pengelolaan Data & Implementasi SIPD Lingkup Keuangan Daerah ---
            ['nomor_kode' => '5.02.02.02.05.0001', 'nomenklatur' => 'Inventarisasi dan Analisis Data Bidang Keuangan Daerah'],
            ['nomor_kode' => '5.02.02.02.05.0002', 'nomenklatur' => 'Implementasi dan Pemeliharaan Sistem Informasi Pemerintah Daerah Bidang Keuangan Daerah'],
            ['nomor_kode' => '5.02.02.02.05.0003', 'nomenklatur' => 'Pembinaan Sistem Informasi Pemerintah Daerah Bidang Keuangan Daerah Pemerintah Kabupaten/Kota'],

            // ===== Program 5.02.03 — PROGRAM PENGELOLAAN BARANG MILIK DAERAH =====
            // --- Kegiatan 5.02.03.02.01 — Pengelolaan Barang Milik Daerah ---
            ['nomor_kode' => '5.02.03.02.01.0001', 'nomenklatur' => 'Penyusunan Standar Harga'],
            ['nomor_kode' => '5.02.03.02.01.0003', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0005', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0006', 'nomenklatur' => 'Inventarisasi Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0007', 'nomenklatur' => 'Pengamanan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0008', 'nomenklatur' => 'Penilaian Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0009', 'nomenklatur' => 'Pengawasan dan Pengendalian Pengelolaan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0010', 'nomenklatur' => 'Optimalisasi Penggunaan, Pemanfaatan, Pemindahtanganan, Pemusnahan, dan Penghapusan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0011', 'nomenklatur' => 'Rekonsiliasi dalam rangka Penyusunan Laporan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0012', 'nomenklatur' => 'Penyusunan Laporan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0013', 'nomenklatur' => 'Pembinaan Pengelolaan Barang Milik Daerah Pemerintah Kabupaten/Kota'],

            // ===== Program 5.02.04 — PROGRAM PENGELOLAAN PENDAPATAN DAERAH =====
            // --- Kegiatan 5.02.04.02.01 — Kegiatan Pengelolaan Pendapatan Daerah ---
            ['nomor_kode' => '5.02.04.02.01.0003', 'nomenklatur' => 'Penyuluhan dan Penyebarluasan Kebijakan Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0005', 'nomenklatur' => 'Pendataan dan Pendaftaran Objek Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0006', 'nomenklatur' => 'Pengolahan, Pemeliharaan, dan Pelaporan Basis Data Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0007', 'nomenklatur' => 'Penilaian Pajak Bumi dan Bangunan Perdesaan dan Perkotaan (PBBP2) serta Bea Perolehan Hak atas Tanah dan Bangunan (BPHTB)'],
            ['nomor_kode' => '5.02.04.02.01.0010', 'nomenklatur' => 'Penelitian dan Verifikasi Data Pelaporan Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0011', 'nomenklatur' => 'Penagihan Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0012', 'nomenklatur' => 'Penyelesaian Keberatan Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0013', 'nomenklatur' => 'Pengendalian, Pemeriksaan dan Pengawasan Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0014', 'nomenklatur' => 'Pembinaan dan Pengawasan Pengelolaan Pajak Daerah dan Retribusi Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0015', 'nomenklatur' => 'Elektronifikasi Transaksi Pemerintah Daerah'],

            // ===== Program 5.03.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KAB/KOTA (KEPEGAWAIAN) =====
            // --- Kegiatan 5.03.01.02.01 ---
            ['nomor_kode' => '5.03.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.03.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // --- Kegiatan 5.03.01.02.02 ---
            ['nomor_kode' => '5.03.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '5.03.01.02.02.0002', 'nomenklatur' => 'Penyediaan Administrasi Pelaksanaan Tugas ASN'],
            ['nomor_kode' => '5.03.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '5.03.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '5.03.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],

            // --- Kegiatan 5.03.01.02.03 ---
            ['nomor_kode' => '5.03.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '5.03.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '5.03.01.02.03.0007', 'nomenklatur' => 'Pemanfaatan Barang Milik Daerah SKPD'],

            // --- Kegiatan 5.03.01.02.05 ---
            ['nomor_kode' => '5.03.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '5.03.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '5.03.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '5.03.01.02.05.0006', 'nomenklatur' => 'Pemulangan Pegawai yang Pensiun'],
            ['nomor_kode' => '5.03.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],

            // --- Kegiatan 5.03.01.02.06 ---
            ['nomor_kode' => '5.03.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '5.03.01.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '5.03.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '5.03.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '5.03.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '5.03.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '5.03.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '5.03.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // --- Kegiatan 5.03.01.02.07 ---
            ['nomor_kode' => '5.03.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '5.03.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],

            // --- Kegiatan 5.03.01.02.08 ---
            ['nomor_kode' => '5.03.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '5.03.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '5.03.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // --- Kegiatan 5.03.01.02.09 ---
            ['nomor_kode' => '5.03.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '5.03.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '5.03.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '5.03.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '5.03.01.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 5.03.02 — PROGRAM KEPEGAWAIAN DAERAH =====
            // --- Kegiatan 5.03.02.02.01 — Pengadaan, Pemberhentian dan Informasi Kepegawaian ASN ---
            ['nomor_kode' => '5.03.02.02.01.0002', 'nomenklatur' => 'Penyusunan Rencana Kebutuhan, Jenis dan Jumlah Jabatan untuk Pelaksanaan Pengadaan ASN'],
            ['nomor_kode' => '5.03.02.02.01.0003', 'nomenklatur' => 'Koordinasi dan Fasilitasi Pengadaan PNS dan PPPK'],
            ['nomor_kode' => '5.03.02.02.01.0006', 'nomenklatur' => 'Koordinasi Pelaksanaan Administrasi Pemberhentian'],
            ['nomor_kode' => '5.03.02.02.01.0008', 'nomenklatur' => 'Fasilitasi Lembaga Profesi ASN'],
            ['nomor_kode' => '5.03.02.02.01.0010', 'nomenklatur' => 'Pengelolaan Sistem Informasi Kepegawaian'],
            ['nomor_kode' => '5.03.02.02.01.0012', 'nomenklatur' => 'Evaluasi Data, Informasi dan Sistem Informasi Kepegawaian'],

            // --- Kegiatan 5.03.02.02.02 — Mutasi dan Promosi ASN ---
            ['nomor_kode' => '5.03.02.02.02.0001', 'nomenklatur' => 'Pengelolaan Mutasi ASN'],
            ['nomor_kode' => '5.03.02.02.02.0002', 'nomenklatur' => 'Pengelolaan Kenaikan Pangkat ASN'],
            ['nomor_kode' => '5.03.02.02.02.0003', 'nomenklatur' => 'Pengelolaan Promosi ASN'],

            // --- Kegiatan 5.03.02.02.03 — Pengembangan Kompetensi ASN ---
            ['nomor_kode' => '5.03.02.02.03.0001', 'nomenklatur' => 'Peningkatan Kapasitas Kinerja ASN'],
            ['nomor_kode' => '5.03.02.02.03.0002', 'nomenklatur' => 'Pengelolaan Assessment Center'],
            ['nomor_kode' => '5.03.02.02.03.0004', 'nomenklatur' => 'Pengelolaan Pendidikan Lanjutan ASN'],
            ['nomor_kode' => '5.03.02.02.03.0005', 'nomenklatur' => 'Koordinasi dan Kerja Sama Pelaksanaan Diklat'],
            ['nomor_kode' => '5.03.02.02.03.0014', 'nomenklatur' => 'Fasilitasi Pengembangan Karir dalam Jabatan Fungsional'],

            // --- Kegiatan 5.03.02.02.04 — Penilaian dan Evaluasi Kinerja Aparatur ---
            ['nomor_kode' => '5.03.02.02.04.0002', 'nomenklatur' => 'Pelaksanaan Penilaian dan Evaluasi Kinerja Aparatur'],
            ['nomor_kode' => '5.03.02.02.04.0004', 'nomenklatur' => 'Pengelolaan Pemberian Penghargaan bagi Pegawai'],
            ['nomor_kode' => '5.03.02.02.04.0005', 'nomenklatur' => 'Pengelolaan Tanda Jasa bagi Pegawai'],
            ['nomor_kode' => '5.03.02.02.04.0007', 'nomenklatur' => 'Pembinaan Disiplin ASN'],
            ['nomor_kode' => '5.03.02.02.04.0008', 'nomenklatur' => 'Pengelolaan Penyelesaian Pelanggaran Disiplin ASN'],
            ['nomor_kode' => '5.03.02.02.04.0009', 'nomenklatur' => 'Pelayanan Proses Izin Perceraian Pegawai'],

            // ===== Program 5.04.02 — PROGRAM PENGEMBANGAN SUMBER DAYA MANUSIA =====
            // --- Kegiatan 5.04.02.02.01 — Pengembangan Kompetensi Teknis ---
            ['nomor_kode' => '5.04.02.02.01.0001', 'nomenklatur' => 'Penyusunan Kebijakan Teknis dan Rencana Pengembangan Kompetensi Teknis Umum, Inti, dan Pilihan bagi Jabatan Administrasi Penyelenggara Urusan Pemerintahan Konkuren, Perangkat Daerah Penunjang, dan Urusan Pemerintahan Umum'],
            ['nomor_kode' => '5.04.02.02.01.0003', 'nomenklatur' => 'Penyelenggaraan Pengembangan Kompetensi Teknis Umum, Inti, dan Pilihan bagi Jabatan Administrasi Penyelenggara Urusan Pemerintahan Konkuren, Perangkat Daerah Penunjang, dan Urusan Pemerintahan Umum'],

            // --- Kegiatan 5.04.02.02.02 — Sertifikasi, Kelembagaan, Pengembangan Kompetensi Manajerial dan Fungsional ---
            ['nomor_kode' => '5.04.02.02.02.0003', 'nomenklatur' => 'Pelaksanaan Sertifikasi Kompetensi di Lingkungan Pemerintah Kabupaten/Kota'],
            ['nomor_kode' => '5.04.02.02.02.0007', 'nomenklatur' => 'Penyelenggaraan Pengembangan Kompetensi bagi Pimpinan Daerah, Jabatan Pimpinan Tinggi, Jabatan Fungsional, Kepemimpinan, dan Prajabatan'],

            // ===== Program 5.05.02 — PROGRAM PENELITIAN DAN PENGEMBANGAN DAERAH =====
            // --- Kegiatan 5.05.02.02.01 — Penelitian & Pengembangan Bidang Penyelenggaraan Pemerintahan dan Pengkajian Peraturan ---
            ['nomor_kode' => '5.05.02.02.01.0012', 'nomenklatur' => 'Pengelolaan Data Kelitbangan dan Peraturan'],

            // --- Kegiatan 5.05.02.02.03 — Penelitian & Pengembangan Bidang Ekonomi dan Pembangunan ---
            ['nomor_kode' => '5.05.02.02.03.0007', 'nomenklatur' => 'Penelitian dan Pengembangan Lingkungan Hidup'],
            ['nomor_kode' => '5.05.02.02.03.0012', 'nomenklatur' => 'Penelitian dan Pengembangan Penataan Ruang dan Pertanahan'],

            // --- Kegiatan 5.05.02.02.04 — Pengembangan Inovasi dan Teknologi ---
            ['nomor_kode' => '5.05.02.02.04.0001', 'nomenklatur' => 'Penelitian, Pengembangan, dan Perekayasaan di Bidang Teknologi dan Inovasi'],
            ['nomor_kode' => '5.05.02.02.04.0003', 'nomenklatur' => 'Diseminasi Jenis, Prosedur dan Metode Penyelenggaraan Pemerintahan Daerah yang Bersifat Inovatif'],
            ['nomor_kode' => '5.05.02.02.04.0004', 'nomenklatur' => 'Sosialisasi dan Diseminasi Hasil-Hasil Kelitbangan'],
            ['nomor_kode' => '5.05.02.02.04.0005', 'nomenklatur' => 'Fasilitasi Hak Kekayaan Intelektual'],

                    
            // ===== Program 6.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 6.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '6.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '6.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '6.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '6.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '6.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '6.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '6.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // --- Kegiatan 6.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '6.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '6.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '6.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '6.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],

            // --- Kegiatan 6.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '6.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '6.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '6.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],

            // --- Kegiatan 6.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '6.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '6.01.01.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '6.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '6.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '6.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '6.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '6.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '6.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '6.01.01.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],

            // --- Kegiatan 6.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '6.01.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '6.01.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '6.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],

            // --- Kegiatan 6.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '6.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '6.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '6.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // --- Kegiatan 6.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '6.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '6.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '6.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '6.01.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 6.01.02 — PROGRAM PENYELENGGARAAN PENGAWASAN =====
            // --- Kegiatan 6.01.02.02.01 — Penyelenggaraan Pengawasan Internal ---
            ['nomor_kode' => '6.01.02.02.01.0001', 'nomenklatur' => 'Pengawasan Kinerja Pemerintah Daerah'],
            ['nomor_kode' => '6.01.02.02.01.0002', 'nomenklatur' => 'Pengawasan Keuangan Pemerintah Daerah'],
            ['nomor_kode' => '6.01.02.02.01.0003', 'nomenklatur' => 'Reviu Laporan Kinerja'],
            ['nomor_kode' => '6.01.02.02.01.0004', 'nomenklatur' => 'Reviu Laporan Keuangan'],
            ['nomor_kode' => '6.01.02.02.01.0006', 'nomenklatur' => 'Kerja Sama Pengawasan Internal'],
            ['nomor_kode' => '6.01.02.02.01.0007', 'nomenklatur' => 'Monitoring dan Evaluasi Tindak Lanjut Hasil Pemeriksaan BPK RI dan Tindak Lanjut Hasil Pemeriksaan APIP'],

            // --- Kegiatan 6.01.02.02.02 — Penyelenggaraan Pengawasan dengan Tujuan Tertentu ---
            ['nomor_kode' => '6.01.02.02.02.0002', 'nomenklatur' => 'Pengawasan dengan Tujuan Tertentu'],

            // ===== Program 6.01.03 — PROGRAM PERUMUSAN KEBIJAKAN, PENDAMPINGAN DAN ASISTENSI =====
            // --- Kegiatan 6.01.03.02.01 — Perumusan Kebijakan Teknis di Bidang Pengawasan dan Fasilitasi Pengawasan ---
            ['nomor_kode' => '6.01.03.02.01.0001', 'nomenklatur' => 'Perumusan Kebijakan Teknis di Bidang Pengawasan'],
            ['nomor_kode' => '6.01.03.02.01.0002', 'nomenklatur' => 'Perumusan Kebijakan Teknis di Bidang Fasilitasi Pengawasan'],

            // --- Kegiatan 6.01.03.02.02 — Pendampingan dan Asistensi ---
            ['nomor_kode' => '6.01.03.02.02.0002', 'nomenklatur' => 'Pendampingan, Asistensi, Verifikasi, dan Penilaian Reformasi Birokrasi'],
            ['nomor_kode' => '6.01.03.02.02.0003', 'nomenklatur' => 'Koordinasi, Monitoring dan Evaluasi serta Verifikasi Pencegahan dan Pemberantasan Korupsi'],
            ['nomor_kode' => '6.01.03.02.02.0004', 'nomenklatur' => 'Pendampingan, Asistensi dan Verifikasi Penegakan Integritas'],
            
            // ===== Program 7.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 7.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '7.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '7.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '7.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '7.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '7.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '7.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '7.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // --- Kegiatan 7.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '7.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '7.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '7.01.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '7.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '7.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],

            // --- Kegiatan 7.01.01.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '7.01.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '7.01.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],

            // --- Kegiatan 7.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '7.01.01.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '7.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '7.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '7.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '7.01.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],

            // --- Kegiatan 7.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '7.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '7.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '7.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '7.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '7.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '7.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // --- Kegiatan 7.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '7.01.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '7.01.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '7.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '7.01.01.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '7.01.01.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // --- Kegiatan 7.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '7.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '7.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '7.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // --- Kegiatan 7.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '7.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '7.01.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '7.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '7.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '7.01.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '7.01.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 7.01.02 — PROGRAM PENYELENGGARAAN PEMERINTAHAN DAN PELAYANAN PUBLIK =====
            // --- Kegiatan 7.01.02.02.01 — Koordinasi Penyelenggaraan Kegiatan Pemerintahan di Tingkat Kecamatan ---
            ['nomor_kode' => '7.01.02.02.01.0001', 'nomenklatur' => 'Koordinasi/Sinergi Perencanaan dan Pelaksanaan Kegiatan Pemerintahan dengan Perangkat Daerah dan Instansi Vertikal Terkait'],

            // --- Kegiatan 7.01.02.02.04 — Pelaksanaan Urusan Pemerintahan yang Dilimpahkan kepada Camat ---
            ['nomor_kode' => '7.01.02.02.04.0003', 'nomenklatur' => 'Pelaksanaan Urusan Pemerintahan yang Terkait dengan Kewenangan Lain yang Dilimpahkan'],

            // ===== Program 7.01.03 — PROGRAM PEMBERDAYAAN MASYARAKAT DESA DAN KELURAHAN =====
            // --- Kegiatan 7.01.03.02.02 — Kegiatan Pemberdayaan Kelurahan ---
            ['nomor_kode' => '7.01.03.02.02.0001', 'nomenklatur' => 'Peningkatan Partisipasi Masyarakat dalam Forum Musyawarah Perencanaan Pembangunan di Kelurahan'],
            ['nomor_kode' => '7.01.03.02.02.0002', 'nomenklatur' => 'Pembangunan Sarana dan Prasarana Kelurahan'],
            ['nomor_kode' => '7.01.03.02.02.0003', 'nomenklatur' => 'Pemberdayaan Masyarakat di Kelurahan'],
            ['nomor_kode' => '7.01.03.02.02.0004', 'nomenklatur' => 'Evaluasi Kelurahan'],

            // --- Kegiatan 7.01.03.02.03 — Pemberdayaan Lembaga Kemasyarakatan Tingkat Kecamatan ---
            ['nomor_kode' => '7.01.03.02.03.0001', 'nomenklatur' => 'Penyelenggaraan Lembaga Kemasyarakatan'],
            ['nomor_kode' => '7.01.03.02.03.0002', 'nomenklatur' => 'Peningkatan Kapasitas Lembaga Kemasyarakatan'],

            // --- Kegiatan 7.01.03.02.06 — Pemberdayaan dan Kesejahteraan Keluarga Tingkat Kecamatan dan Kelurahan ---
            ['nomor_kode' => '7.01.03.02.06.0003', 'nomenklatur' => 'Peningkatan Ketahanan Pangan Keluarga'],
            ['nomor_kode' => '7.01.03.02.06.0010', 'nomenklatur' => 'Pelatihan Keluarga Tanggap Bencana Alam'],

            // ===== Program 7.01.04 — PROGRAM KOORDINASI KETENTRAMAN DAN KETERTIBAN UMUM =====
            // --- Kegiatan 7.01.04.02.01 — Koordinasi Upaya Penyelenggaraan Ketenteraman dan Ketertiban Umum ---
            ['nomor_kode' => '7.01.04.02.01.0001', 'nomenklatur' => 'Sinergitas dengan Kepolisian Negara Republik Indonesia, Tentara Nasional Indonesia dan Instansi Vertikal di Wilayah Kecamatan'],
            ['nomor_kode' => '7.01.04.02.01.0002', 'nomenklatur' => 'Harmonisasi Hubungan dengan Tokoh Agama dan Tokoh Masyarakat'],

            // --- Kegiatan 7.01.04.02.02 — Koordinasi Penerapan dan Penegakan Peraturan Daerah dan Peraturan Kepala Daerah ---
            ['nomor_kode' => '7.01.04.02.02.0001', 'nomenklatur' => 'Koordinasi/Sinergi dengan Perangkat Daerah yang Tugas dan Fungsinya di Bidang Penegakan Peraturan Perundang-Undangan dan/atau Kepolisian Negara Republik Indonesia'],
            
            // ===== Program 8.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 8.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '8.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '8.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '8.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '8.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '8.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '8.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // --- Kegiatan 8.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '8.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '8.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],

            // --- Kegiatan 8.01.01.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],

            // --- Kegiatan 8.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],

            // --- Kegiatan 8.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '8.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '8.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '8.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '8.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '8.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '8.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // --- Kegiatan 8.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '8.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],

            // --- Kegiatan 8.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '8.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '8.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '8.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],

            // --- Kegiatan 8.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '8.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '8.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '8.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '8.01.01.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 8.01.02 — PROGRAM PENGELOLAAN KEUANGAN DAERAH =====
            ['nomor_kode' => '8.01.02.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Anggaran Pendapatan dan Belanja Daerah'],
            ['nomor_kode' => '8.01.02.02.01.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Kebijakan Fiskal Daerah'],
            ['nomor_kode' => '8.01.02.02.01.0003', 'nomenklatur' => 'Pengendalian dan Evaluasi Pelaksanaan Anggaran Daerah'],

            // ===== Program 8.01.03 — PROGRAM PENATAUSAHAAN DAN AKUNTANSI DAERAH =====
            ['nomor_kode' => '8.01.03.02.01.0001', 'nomenklatur' => 'Penyusunan dan Penyajian Laporan Keuangan Daerah'],
            ['nomor_kode' => '8.01.03.02.01.0002', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Keuangan Daerah'],

            // ===== Program 8.01.04 — PROGRAM VERIFIKASI DAN AKUNTABILITAS KEUANGAN DAERAH =====
            ['nomor_kode' => '8.01.04.02.01.0001', 'nomenklatur' => 'Pelaksanaan Verifikasi dan Pengawasan Keuangan Daerah'],
            ['nomor_kode' => '8.01.04.02.01.0002', 'nomenklatur' => 'Pelaksanaan Audit Internal Keuangan Daerah'],

            // ===== Program 8.01.05 — PROGRAM PENGELOLAAN BARANG MILIK DAERAH =====
            ['nomor_kode' => '8.01.05.02.01.0001', 'nomenklatur' => 'Penyusunan Kebutuhan Barang Milik Daerah'],
            ['nomor_kode' => '8.01.05.02.01.0002', 'nomenklatur' => 'Inventarisasi dan Penatausahaan Barang Milik Daerah'],
            ['nomor_kode' => '8.01.05.02.01.0003', 'nomenklatur' => 'Pengamanan Barang Milik Daerah'],

            // ===== Program 8.01.06 — PROGRAM LAIN-LAIN PENUNJANG =====
            ['nomor_kode' => '8.01.06.02.01.0001', 'nomenklatur' => 'Pengembangan Kapasitas SDM Pengelola Keuangan dan Barang Daerah'],
            ['nomor_kode' => '8.01.06.02.01.0002', 'nomenklatur' => 'Penyediaan Sistem Informasi Pengelolaan Keuangan dan Aset Daerah'],
        ];

        foreach ($rows as $r) {
            $parts = explode('.', $r['nomor_kode']);
            $urusanKode   = $parts[0];
            $bidangKode   = $parts[0] . '.' . $parts[1];
            $programKode  = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
            $kegiatanKode = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.' . $parts[3] . '.' . $parts[4];

            $idUrusan   = DB::table('kode_nomenklatur')->where('jenis_nomenklatur', 0)->where('nomor_kode', $urusanKode)->value('id');
            $idBidang   = DB::table('kode_nomenklatur')->where('jenis_nomenklatur', 1)->where('nomor_kode', $bidangKode)->value('id');
            $idProgram  = DB::table('kode_nomenklatur')->where('jenis_nomenklatur', 2)->where('nomor_kode', $programKode)->value('id');
            $idKegiatan = DB::table('kode_nomenklatur')->where('jenis_nomenklatur', 3)->where('nomor_kode', $kegiatanKode)->value('id');

            DB::table('kode_nomenklatur')->updateOrInsert(
                ['nomor_kode' => $r['nomor_kode']],
                ['jenis_nomenklatur' => 4, 'nomenklatur' => $r['nomenklatur'], 'updated_at' => $now, 'created_at' => $now]
            );
            $id = DB::table('kode_nomenklatur')->where('nomor_kode', $r['nomor_kode'])->value('id');

            DB::table('kode_nomenklatur_detail')->updateOrInsert(
                ['id_nomenklatur' => $id],
                [
                    'id_urusan' => $idUrusan,
                    'id_bidang_urusan' => $idBidang,
                    'id_program' => $idProgram,
                    'id_kegiatan' => $idKegiatan,
                    'id_sub_kegiatan' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}