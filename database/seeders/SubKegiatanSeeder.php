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

            // ===== Program 1.01.01 — PROGRAM PENGELOLAAN PERMUSEUMAN =====
            // --- Kegiatan 1.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.01.01.02.01.0014', 'nomenklatur' => 'Pengadaan Mebel Sekolah'],
            ['nomor_kode' => '1.01.01.02.01.0016', 'nomenklatur' => 'Pengadaan Perlengkapan Sekolah'],
            ['nomor_kode' => '1.01.01.02.01.0025', 'nomenklatur' => 'Pembinaan Minat, Bakat dan Kreativitas Siswa'],
            ['nomor_kode' => '1.01.01.02.01.0026', 'nomenklatur' => 'Penyediaan Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Sekolah Dasar'],
            ['nomor_kode' => '1.01.01.02.01.0027', 'nomenklatur' => 'Pengembangan Karir Pendidik dan Tenaga Kependidikan pada Satuan Pendidikan Sekolah Dasar'],
            ['nomor_kode' => '1.01.01.02.01.0028', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen Sekolah'],
            ['nomor_kode' => '1.01.01.02.01.0029', 'nomenklatur' => 'Pengelolaan Dana BOS Sekolah Dasar'],
            ['nomor_kode' => '1.01.01.02.01.0035', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.01.02.01.0036', 'nomenklatur' => 'Pengembangan konten digital untuk pendidikan'],
            ['nomor_kode' => '1.01.01.02.01.0037', 'nomenklatur' => 'Pelatihan Penggunaan Aplikasi Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.01.0041', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.01.02.01.0043', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.01.02.01.0048', 'nomenklatur' => 'Rehabilitasi Sedang/Berat Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.01.02.01.0049', 'nomenklatur' => 'Bimbingan Teknis, Pelatihan, dan/atau Magang/PKL untuk Peningkatan Kapasitas Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.01.0050', 'nomenklatur' => 'Penyelenggaraan Proses Belajar Bagi Peserta Didik'],
            ['nomor_kode' => '1.01.01.02.01.0054', 'nomenklatur' => 'Penyediaan Biaya Personil Peserta Didik Sekolah Dasar'],
            ['nomor_kode' => '1.01.01.02.01.0055', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik'],
            // --- Kegiatan 1.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.01.01.02.02.0002', 'nomenklatur' => 'Pembinaan Sumber Daya Manusia, Lembaga, dan Pranata Tradisional'],
            ['nomor_kode' => '1.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.01.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.01.01.02.02.0012', 'nomenklatur' => 'Pembangunan Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.01.02.02.0024', 'nomenklatur' => 'Rehabilitasi Sedang/Berat Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.01.02.02.0027', 'nomenklatur' => 'Pengadaan Perlengkapan Sekolah'],
            ['nomor_kode' => '1.01.01.02.02.0032', 'nomenklatur' => 'Penyediaan Biaya Personil Peserta Didik Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.01.02.02.0038', 'nomenklatur' => 'Pembinaan Minat, Bakat dan Kreativitas Siswa'],
            ['nomor_kode' => '1.01.01.02.02.0039', 'nomenklatur' => 'Penyediaan Pendidik dan Tenaga Kependidikan bagi Satuan Pendidikan Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.01.02.02.0040', 'nomenklatur' => 'Pengembangan Karir Pendidik dan Tenaga Kependidikan pada Satuan Pendidikan Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.01.02.02.0041', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen Sekolah'],
            ['nomor_kode' => '1.01.01.02.02.0042', 'nomenklatur' => 'Pengelolaan Dana BOS Sekolah Menengah Pertama'],
            ['nomor_kode' => '1.01.01.02.02.0048', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.01.02.02.0049', 'nomenklatur' => 'Pengembangan konten digital untuk pendidikan'],
            ['nomor_kode' => '1.01.01.02.02.0050', 'nomenklatur' => 'Pelatihan Penggunaan Aplikasi Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.02.0054', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.01.02.02.0055', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.01.02.02.0058', 'nomenklatur' => 'Penyelenggaraan Proses Belajar bagi Peserta Didik'],
            ['nomor_kode' => '1.01.01.02.02.0060', 'nomenklatur' => 'Bimbingan Teknis, Pelatihan, dan/atau Magang/PKL untuk Peningkatan Kapasitas Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.02.0067', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik'],
            // --- Kegiatan 1.01.01.02.03 — Pengelolaan Pendidikan Anak Usia Dini (PAUD) ---
            ['nomor_kode' => '1.01.01.02.03.0015', 'nomenklatur' => 'Penyediaan Pendidik dan Tenaga Kependidikan bagi Satuan PAUD'],
            ['nomor_kode' => '1.01.01.02.03.0016', 'nomenklatur' => 'Pengembangan Karir Pendidik dan Tenaga Kependidikan pada Satuan Pendidikan PAUD'],
            ['nomor_kode' => '1.01.01.02.03.0017', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen PAUD'],
            ['nomor_kode' => '1.01.01.02.03.0022', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.01.02.03.0026', 'nomenklatur' => 'Sosialisasi dan Advokasi Kebijakan Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.03.0029', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.01.02.03.0037', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.01.02.03.0039', 'nomenklatur' => 'Bimbingan Teknis, Pelatihan, dan/atau Magang/PKL untuk Peningkatan Kapasitas Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.03.0040', 'nomenklatur' => 'Pembangunan Unit Sekolah Baru (USB)'],
            ['nomor_kode' => '1.01.01.02.03.0045', 'nomenklatur' => 'Rehabilitasi Sedang/Berat Sarana, Prasarana dan Utilitas PAUD'],
            ['nomor_kode' => '1.01.01.02.03.0046', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik PAUD'],
            ['nomor_kode' => '1.01.01.02.03.0047', 'nomenklatur' => 'Penyelenggaraan Proses Belajar PAUD'],
            // --- Kegiatan 1.01.01.02.04 — Pengelolaan Pendidikan Nonformal/Kesetaraan ---
            ['nomor_kode' => '1.01.01.02.04.0016', 'nomenklatur' => 'Pembinaan Kelembagaan dan Manajemen Sekolah Nonformal/Kesetaraan'],
            ['nomor_kode' => '1.01.01.02.04.0024', 'nomenklatur' => 'Pembinaan Penggunaan Teknologi, Informasi dan Komunikasi (TIK) untuk Pendidikan'],
            ['nomor_kode' => '1.01.01.02.04.0027', 'nomenklatur' => 'Koordinasi, Perencanaan, Supervisi dan Evaluasi Layanan di Bidang Pendidikan'],
            ['nomor_kode' => '1.01.01.02.04.0030', 'nomenklatur' => 'Fasilitasi Komunitas Belajar Pendidik dan Tenaga Kependidikan'],
            ['nomor_kode' => '1.01.01.02.04.0031', 'nomenklatur' => 'Pemberian layanan pendampingan bagi satuan pendidikan untuk pencegahan perundungan, kekerasan, dan intoleransi'],
            ['nomor_kode' => '1.01.01.02.04.0046', 'nomenklatur' => 'Penyelenggaraan Proses Belajar bagi Peserta Didik'],
            ['nomor_kode' => '1.01.01.02.04.0049', 'nomenklatur' => 'Pemeliharaan Rutin Sarana, Prasarana dan Utilitas Sekolah'],
            ['nomor_kode' => '1.01.01.02.04.0055', 'nomenklatur' => 'Pengadaan Alat Praktik dan Peraga Peserta Didik Nonformal / Kesetaraan'],
            // --- Kegiatan 1.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.01.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 1.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.01.01.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.01.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 1.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.01.01.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.01.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 1.02.02 — PROGRAM PEMBERDAYAAN MASYARAKAT BIDANG KESEHATAN =====
            // --- Kegiatan 1.02.02.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.02.02.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.02.02.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.02.02.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.02.02.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.02.02.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.02.02.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.02.02.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
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
            // --- Kegiatan 1.02.02.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.02.02.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.02.02.02.02.0002', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan Ibu Bersalin'],
            ['nomor_kode' => '1.02.02.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.02.02.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.02.02.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.02.02.02.02.0006', 'nomenklatur' => 'Pengelolaan Pelayanan Kesehatan pada Usia Produktif'],
            ['nomor_kode' => '1.02.02.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
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
            // --- Kegiatan 1.02.02.02.03 — Penyelenggaraan Sistem Informasi Kesehatan Secara Terintegrasi ---
            ['nomor_kode' => '1.02.02.02.03.0001', 'nomenklatur' => 'Pengembangan Mutu dan Peningkatan Kompetensi Teknis Sumber Daya Manusia Kesehatan Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.02.02.02.03.0002', 'nomenklatur' => 'Pengelolaan Sistem Informasi Kesehatan'],
            // --- Kegiatan 1.02.02.02.04 — Penerbitan Izin Rumah Sakit Kelas C, D dan Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota ---
            ['nomor_kode' => '1.02.02.02.04.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Pengawasan Perizinan Rumah Sakit Kelas C, D dan Fasilitas Pelayanan Kesehatan Lainnya'],
            ['nomor_kode' => '1.02.02.02.04.0002', 'nomenklatur' => 'Peningkatan Tata Kelola Rumah Sakit dan Fasilitas Pelayanan Kesehatan Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '1.02.02.02.04.0003', 'nomenklatur' => 'Peningkatan Mutu Pelayanan Fasilitas Kesehatan'],
            ['nomor_kode' => '1.02.02.02.04.0004', 'nomenklatur' => 'Penyiapan Perumusan dan Pelaksanaan Pelayanan Kesehatan Rujukan'],
            // --- Kegiatan 1.02.02.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.02.02.02.05.0001', 'nomenklatur' => 'Pengendalian dan Pengawasan serta Tindak Lanjut Penerbitan Stiker Pembinaan pada Makanan Jajanan dan Sentra Makanan Jajanan'],
            ['nomor_kode' => '1.02.02.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.02.02.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.02.02.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.02.02.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.02.02.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 1.02.02.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.02.02.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.02.02.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.02.02.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.02.02.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.02.02.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.02.02.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.02.02.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.02.02.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.02.02.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 1.02.02.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.02.02.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.02.02.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.02.02.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.02.02.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.02.02.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.02.02.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.02.02.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.02.02.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '1.02.02.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 1.02.02.02.10 — Peningkatan Pelayanan BLUD ---
            ['nomor_kode' => '1.02.02.02.10.0001', 'nomenklatur' => 'Pelayanan dan Penunjang Pelayanan BLUD'],

            // ===== Program 1.03.04 — PROGRAM PENYELENGGARAAN PENATAAN RUANG =====
            // --- Kegiatan 1.03.04.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.03.04.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.03.04.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.03.04.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.03.04.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.03.04.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.03.04.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.03.04.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.03.04.02.01.0008', 'nomenklatur' => 'Penataan Bangunan dan Lingkungan Kawasan Cagar Budaya, Kawasan Pariwisata, Kawasan Sistem Perkotaan Nasional dan Kawasan Strategis Lainnya'],
            ['nomor_kode' => '1.03.04.02.01.0010', 'nomenklatur' => 'Fasilitasi Sertifikasi Tenaga Kerja Konstruksi Kualifikasi Jabatan Operator dan Teknisi atau Analis'],
            ['nomor_kode' => '1.03.04.02.01.0013', 'nomenklatur' => 'Sosialisasi Kebijakan dan Peraturan Perundang-Undangan Bidang Penataan Ruang'],
            ['nomor_kode' => '1.03.04.02.01.0021', 'nomenklatur' => 'Pembangunan Sistem Penyediaan Air Minum (SPAM) Jaringan Perpipaan di Kawasan Strategis Kabupaten/Kota'],
            ['nomor_kode' => '1.03.04.02.01.0022', 'nomenklatur' => 'Pembangunan Sistem Penyediaan Air Minum (SPAM) Bukan Jaringan Perpipaan'],
            ['nomor_kode' => '1.03.04.02.01.0023', 'nomenklatur' => 'Penyelenggaraan Penerbitan Persetujuan Bangunan Gedung (PBG), Sertifikat Laik Fungsi (SLF), Surat Bukti Kepemilikan Bangunan Gedung (SBKBG),  Rencana Teknis Pembongkaran Bangunan Gedung (RTB),  Tim Profesi Ahli (TPA), Tim Penilai Teknis (TPT), Penilik, dan Pendataan Bangunan Gedung melalui SIMBG'],
            ['nomor_kode' => '1.03.04.02.01.0024', 'nomenklatur' => 'Peningkatan Sistem Drainase Perkotaan'],
            ['nomor_kode' => '1.03.04.02.01.0025', 'nomenklatur' => 'Penyusunan Rencana, Kebijakan, Strategi dan Teknis Sistem Penyediaan Air Minum (SPAM)'],
            ['nomor_kode' => '1.03.04.02.01.0026', 'nomenklatur' => 'Rehabilitasi Pintu Air/Bendung Pengendali Banjir'],
            ['nomor_kode' => '1.03.04.02.01.0028', 'nomenklatur' => 'Rehabilitasi Sistem Drainase Perkotaan'],
            ['nomor_kode' => '1.03.04.02.01.0029', 'nomenklatur' => 'Pembangunan Sistem Drainase Perkotaan'],
            ['nomor_kode' => '1.03.04.02.01.0030', 'nomenklatur' => 'Penyusunan Rencana, Kebijakan, Strategi dan Teknis Sistem Drainase Perkotaan'],
            ['nomor_kode' => '1.03.04.02.01.0031', 'nomenklatur' => 'Operasi dan Pemeliharaan Sistem Drainase Perkotaan'],
            ['nomor_kode' => '1.03.04.02.01.0032', 'nomenklatur' => 'Peningkatan Sistem Pengelolaan Air Limbah Domestik (SPALD) Terpusat  Skala Permukiman'],
            ['nomor_kode' => '1.03.04.02.01.0033', 'nomenklatur' => 'Penyediaan Jasa Penyedotan Lumpur Tinja'],
            ['nomor_kode' => '1.03.04.02.01.0034', 'nomenklatur' => 'Pemeliharaan Berkala Jalan'],
            ['nomor_kode' => '1.03.04.02.01.0038', 'nomenklatur' => 'Pemeliharaan Rutin Jembatan'],
            ['nomor_kode' => '1.03.04.02.01.0039', 'nomenklatur' => 'Penyediaan Sub Sistem Pengolahan Air Limbah Domestik (SPALD) Setempat'],
            ['nomor_kode' => '1.03.04.02.01.0040', 'nomenklatur' => 'Pembangunan Jembatan'],
            ['nomor_kode' => '1.03.04.02.01.0041', 'nomenklatur' => 'Penyusunan Rencana, Kebijakan, Strategi dan Teknis Sistem Pengelolaan Air Limbah Domestik (SPALD)'],
            ['nomor_kode' => '1.03.04.02.01.0044', 'nomenklatur' => 'Rehabilitasi Jalan'],
            ['nomor_kode' => '1.03.04.02.01.0046', 'nomenklatur' => 'Pemeliharaan Rutin Jalan'],
            ['nomor_kode' => '1.03.04.02.01.0072', 'nomenklatur' => 'Evaluasi dan Rekomendasi Teknis (Rekomtek) Pemanfaatan Sumber Daya Air Wilayah Sungai Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.03.04.02.01.0075', 'nomenklatur' => 'Pembinaan dan Pemberdayaan Kelembagaan Pengelolaan SDA Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.03.04.02.01.0080', 'nomenklatur' => 'Operasi dan Pemeliharaan Tanggul dan Tebing Sungai'],
            ['nomor_kode' => '1.03.04.02.01.0091', 'nomenklatur' => 'Operasi dan Pemeliharaan Bendungan'],
            ['nomor_kode' => '1.03.04.02.01.0093', 'nomenklatur' => 'Normalisasi/Restorasi Sungai'],
            ['nomor_kode' => '1.03.04.02.01.0105', 'nomenklatur' => 'Rehabilitasi Seawall dan Bangunan Pengaman Pantai Lainnya'],
            ['nomor_kode' => '1.03.04.02.01.0106', 'nomenklatur' => 'Rehabilitasi Breakwater'],
            ['nomor_kode' => '1.03.04.02.01.0109', 'nomenklatur' => 'Pembangunan Bangunan Perkuatan Tebing'],
            ['nomor_kode' => '1.03.04.02.01.0112', 'nomenklatur' => 'Rehabilitasi Bendungan'],
            ['nomor_kode' => '1.03.04.02.01.0115', 'nomenklatur' => 'Pembangunan Tanggul Sungai'],
            ['nomor_kode' => '1.03.04.02.01.0117', 'nomenklatur' => 'Pembangunan Seawall dan Bangunan Pengaman Pantai Lainnya'],
            ['nomor_kode' => '1.03.04.02.01.0118', 'nomenklatur' => 'Penyusunan Pola dan Rencana Pengelolaan SDA WS Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.03.04.02.01.0124', 'nomenklatur' => 'Pembangunan Polder/Kolam Retensi'],
            // --- Kegiatan 1.03.04.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.03.04.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.03.04.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.03.04.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.03.04.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.03.04.02.02.0012', 'nomenklatur' => 'Penyediaan Perangkat Pendukung Layanan Informasi Jasa Konstruksi'],
            ['nomor_kode' => '1.03.04.02.02.0014', 'nomenklatur' => 'Rehabilitasi Jaringan Irigasi Permukaan'],
            ['nomor_kode' => '1.03.04.02.02.0021', 'nomenklatur' => 'Operasi dan Pemeliharaan Jaringan Irigasi Permukaan'],
            // --- Kegiatan 1.03.04.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '1.03.04.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '1.03.04.02.03.0004', 'nomenklatur' => 'Pelaksanaan Persetujuan Kesesuaian Kegiatan Pemanfaatan Ruang'],
            ['nomor_kode' => '1.03.04.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '1.03.04.02.03.0007', 'nomenklatur' => 'Sistem informasi dan komunikasi penataan ruang'],
            // --- Kegiatan 1.03.04.02.04 — Koordinasi dan Sinkronisasi Pengendalian Pemanfaatan Ruang Daerah Kabupaten/Kota ---
            ['nomor_kode' => '1.03.04.02.04.0004', 'nomenklatur' => 'Koordinasi Pelaksanaan Penataan Ruang'],
            ['nomor_kode' => '1.03.04.02.04.0008', 'nomenklatur' => 'Penilaian Pelaksanaan Kesesuaian Kegiatan Pemanfaatan Ruang dan/atau pernyataan mandiri pelaku UMK'],
            // --- Kegiatan 1.03.04.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.03.04.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.03.04.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.03.04.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.03.04.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 1.03.04.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.03.04.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.03.04.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '1.03.04.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.03.04.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.03.04.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.03.04.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.03.04.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.03.04.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.03.04.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.03.04.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.03.04.02.07.0003', 'nomenklatur' => 'Pengadaan Alat Besar'],
            ['nomor_kode' => '1.03.04.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.03.04.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 1.03.04.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.03.04.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.03.04.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.03.04.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.03.04.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.03.04.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.03.04.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.03.04.02.09.0003', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan dan Perizinan Alat Besar'],
            ['nomor_kode' => '1.03.04.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.03.04.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.03.04.02.09.0007', 'nomenklatur' => 'Pemeliharaan Aset Tetap Lainnya'],
            ['nomor_kode' => '1.03.04.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 1.04.05 — PROGRAM PENGELOLAAN TANAH KOSONG =====
            // --- Kegiatan 1.04.05.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.04.05.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.04.05.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.04.05.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.04.05.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.04.05.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.04.05.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.04.05.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.04.05.02.01.0009', 'nomenklatur' => 'Identifikasi Perumahan di Lokasi Rawan Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.05.02.01.0010', 'nomenklatur' => 'Pendataan dan Verifikasi Calon Penerima Rumah bagi Korban Bencana Kabupaten/Kota'],
            // --- Kegiatan 1.04.05.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.04.05.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.04.05.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.04.05.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.04.05.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.04.05.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.04.05.02.02.0008', 'nomenklatur' => 'Rembug Warga untuk Menentukan Calon Penerima Rumah bagi Korban Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.05.02.02.0009', 'nomenklatur' => 'Sosialisasi Pengembangan Perumahan Baru dan Mekanisme Akses Pembiayaan Perumahan'],
            ['nomor_kode' => '1.04.05.02.02.0010', 'nomenklatur' => 'Sosialisasi tentang Mekanisme Penggantian Hak atas Tanah dan/atau Bangunan'],
            // --- Kegiatan 1.04.05.02.03 — Pembangunan dan Rehabilitasi Rumah Korban Bencana atau Relokasi Program Kabupaten/Kota ---
            ['nomor_kode' => '1.04.05.02.03.0001', 'nomenklatur' => 'Rehabilitasi Rumah bagi Korban Bencana'],
            ['nomor_kode' => '1.04.05.02.03.0002', 'nomenklatur' => 'Perbaikan Rumah Tidak Layak Huni'],
            ['nomor_kode' => '1.04.05.02.03.0004', 'nomenklatur' => 'Pembangunan Rumah bagi Korban Bencana'],
            ['nomor_kode' => '1.04.05.02.03.0007', 'nomenklatur' => 'Pendataan dan Verifikasi Penyelenggaraan Kawasan Permukiman Kumuh'],
            ['nomor_kode' => '1.04.05.02.03.0009', 'nomenklatur' => 'Pelaksanaan Peremajaan Kawasan Permukiman Kumuh'],
            ['nomor_kode' => '1.04.05.02.03.0012', 'nomenklatur' => 'Pembangunan Rumah Baru Layak Huni untuk Peningkatan Kualitas Permukiman Kumuh dengan Luas di Bawah 10 (Sepuluh) Ha'],
            // --- Kegiatan 1.04.05.02.04 — Pendistribusian dan Serah Terima Rumah bagi Korban Bencana atau Relokasi Program Kabupaten/Kota ---
            ['nomor_kode' => '1.04.05.02.04.0005', 'nomenklatur' => 'Penatausahaan Serah Terima Rumah bagi Korban Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.04.05.02.04.0006', 'nomenklatur' => 'Pelaksanaan Pembagian Rumah bagi Korban Bencana Kabupaten/Kota'],
            // --- Kegiatan 1.04.05.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.04.05.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.04.05.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.04.05.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.04.05.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            // --- Kegiatan 1.04.05.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.04.05.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.04.05.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.04.05.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.04.05.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.04.05.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.04.05.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.04.05.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.04.05.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.04.05.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.04.05.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.04.05.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 1.04.05.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.04.05.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.04.05.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.04.05.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.04.05.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.04.05.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.04.05.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.04.05.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.04.05.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.04.05.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 1.05.06 — PROGRAM PENINGKATAN KETENTERAMAN DAN KETERTIBAN UMUM =====
            // --- Kegiatan 1.05.06.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.05.06.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.05.06.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.05.06.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.05.06.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.05.06.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.05.06.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.05.06.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.05.06.02.01.0008', 'nomenklatur' => 'Penyusunan SOP Ketertiban Umum dan Ketenteraman Masyarakat'],
            ['nomor_kode' => '1.05.06.02.01.0010', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Polisi Pamong Praja melalui Pendidikan dan Pelatihan Dasar Pol PPngsional Pol PP dan Uji Kompetensi bagi Pejabat Fungsional'],
            ['nomor_kode' => '1.05.06.02.01.0011', 'nomenklatur' => 'Pembentukan Tim Penilai angka kredit dan Sekretariat Pengelolaan Jabatan Fungsional Pol PP'],
            ['nomor_kode' => '1.05.06.02.01.0012', 'nomenklatur' => 'Peningkatan Kapasitas SDM Pol PP melalui Uji Kompetensi  untuk usulan perpindahan jabatan ke jabatan fungsional Pol PP, Promosi dan  kenaikan jenjang jabatan'],
            ['nomor_kode' => '1.05.06.02.01.0013', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Pelindungan Masyarakat'],
            ['nomor_kode' => '1.05.06.02.01.0014', 'nomenklatur' => 'Peningkatan Kapasitas SDM Satuan Polisi Pamong Praja dan Satlinmas melalui Pelatihan Teknis Satpol PP dan Satlinmas'],
            ['nomor_kode' => '1.05.06.02.01.0015', 'nomenklatur' => 'Pencegahan Gangguan Ketenteraman dan Ketertiban Umum Melalui Deteksi Dini dan Cegah Dini, Pembinaan dan Penyuluhan, Pelaksanaan Patroli, Pengamanan, dan Pengawalan'],
            ['nomor_kode' => '1.05.06.02.01.0016', 'nomenklatur' => 'Penindakan Atas Gangguan Ketenteraman dan Ketertiban Umum berdasarkan Perda dan Perkada Melalui Penertiban dan Penanganan Unjuk Rasa dan Kerusuhan Massa'],
            ['nomor_kode' => '1.05.06.02.01.0017', 'nomenklatur' => 'Penyediaan Layanan dasar dalam rangka Dampak Penegakan Peraturan Daerah dan Perturan kepala daerah'],
            ['nomor_kode' => '1.05.06.02.01.0018', 'nomenklatur' => 'Pengadaan dan Pemeliharaan Sarana dan Prasarana Ketentraman dan Ketertiban Umum'],
            // --- Kegiatan 1.05.06.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.05.06.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.05.06.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.05.06.02.02.0004', 'nomenklatur' => 'Pembinaan dan Penyuluhan terhadap Pelanggar Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.06.02.02.0005', 'nomenklatur' => 'Penyusunan SOP Penegakan Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.06.02.02.0006', 'nomenklatur' => 'Pengadaan dan Pemeliharaan Sarana dan Prasarana Penegakan Peraturan Daerah (Ruang Pemeriksanaan, Gelar Perkara, dan Ruang Penyimpanan Barang Bukti)'],
            ['nomor_kode' => '1.05.06.02.02.0007', 'nomenklatur' => 'Penyelidikan terhadap dugaan Pelanggaran Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.06.02.02.0008', 'nomenklatur' => 'Dukungan Pelaksanaan Sidang atas Pelanggaran Peraturan Daerah'],
            ['nomor_kode' => '1.05.06.02.02.0009', 'nomenklatur' => 'Pemberkasan Administrasi Penyidikan oleh PPNS Penegak Peraturan Daerah'],
            ['nomor_kode' => '1.05.06.02.02.0010', 'nomenklatur' => 'Sosialisasi Penegakan Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '1.05.06.02.02.0011', 'nomenklatur' => 'Penanganan  Atas Pelanggaran Peraturan Daerah dan Peraturan  Kepala daerah'],
            ['nomor_kode' => '1.05.06.02.02.0012', 'nomenklatur' => 'Pengawasan Atas Kepatuhan Terhadap Pelaksanaan Peraturan Daerah dan Peraturan Kepala Daerah'],
            // --- Kegiatan 1.05.06.02.03 — Pembinaan Penyidik Pegawai Negeri Sipil (PPNS) Kabupaten/Kota ---
            ['nomor_kode' => '1.05.06.02.03.0002', 'nomenklatur' => 'Pembentukan Sekretariat PPNS'],
            ['nomor_kode' => '1.05.06.02.03.0003', 'nomenklatur' => 'Kerja Sama Antar Lembaga dan Kemitraan dalam Pelaksanaan Penegakan Peraturan Daerah'],
            ['nomor_kode' => '1.05.06.02.03.0004', 'nomenklatur' => 'Pembentukan PPNS Penegak Peraturan Daerah'],
            ['nomor_kode' => '1.05.06.02.03.0005', 'nomenklatur' => 'Dukungan Operasional Sekretariat PPNS'],
            ['nomor_kode' => '1.05.06.02.03.0006', 'nomenklatur' => 'Pengembangan Kapasitas dan Karier PPNS'],
            // --- Kegiatan 1.05.06.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.05.06.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.05.06.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.05.06.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            // --- Kegiatan 1.05.06.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.05.06.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.05.06.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '1.05.06.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.05.06.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.05.06.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.05.06.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.05.06.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.05.06.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.05.06.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.05.06.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.05.06.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.05.06.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.05.06.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 1.05.06.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.05.06.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.05.06.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.05.06.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.05.06.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.05.06.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.05.06.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.05.06.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.05.06.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 1.05.07 — PROGRAM PENCEGAHAN, PENANGGULANGAN,PENYELAMATAN KEBAKARAN DAN PENYELAMATAN NONKEBAKARAN =====
            // --- Kegiatan 1.05.07.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.05.07.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.05.07.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.05.07.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.05.07.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.05.07.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.05.07.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.05.07.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.05.07.02.01.0017', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pencegahan, Penanggulangan Kebakaran dan Alat Pelindung Diri'],
            // --- Kegiatan 1.05.07.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.05.07.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.05.07.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.05.07.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.05.07.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.05.07.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            // --- Kegiatan 1.05.07.02.04 — Pemberdayaan Masyarakat dalam Pencegahan Kebakaran ---
            ['nomor_kode' => '1.05.07.02.04.0001', 'nomenklatur' => 'Pemberdayaan Masyarakat dalam Pencegahan dan Penanggulangan Kebakaran Melalui Sosialisasi dan Edukasi Masyarakat'],
            ['nomor_kode' => '1.05.07.02.04.0003', 'nomenklatur' => 'Dukungan Pemberdayaan Masyarakat/Relawan Pemadam Kebakaran Melalui Penyediaan Sarana dan PraSarana'],
            // --- Kegiatan 1.05.07.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.05.07.02.05.0001', 'nomenklatur' => 'Penyelenggaraan Operasi Pencarian dan Pertolongan pada Peristiwa yang Menimpa, Membahayakan, dan/atau Mengancam Keselamatan Manusia'],
            ['nomor_kode' => '1.05.07.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.05.07.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '1.05.07.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.05.07.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            // --- Kegiatan 1.05.07.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.05.07.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.05.07.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.05.07.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.05.07.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.05.07.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.05.07.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.05.07.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.05.07.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.05.07.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 1.05.07.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.05.07.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.05.07.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.05.07.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            // --- Kegiatan 1.05.07.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.05.07.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.05.07.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.05.07.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.05.07.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 1.05.08 — PROGRAM PENANGGULANGAN BENCANA =====
            // --- Kegiatan 1.05.08.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.05.08.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.05.08.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.05.08.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.05.08.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.05.08.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.05.08.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.05.08.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            // --- Kegiatan 1.05.08.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.05.08.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.05.08.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.05.08.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.05.08.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.05.08.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.05.08.02.02.0015', 'nomenklatur' => 'Penyediaan Peralatan Perlindungan dan Kesiapsiagaan Terhadap Bencana kabupaten/kota'],
            ['nomor_kode' => '1.05.08.02.02.0018', 'nomenklatur' => 'Gladi Kesiapsiagaan Terhadap Bencana kabupaten/kota'],
            ['nomor_kode' => '1.05.08.02.02.0021', 'nomenklatur' => 'Pengembangan Kapasitas Tim Reaksi Cepat (TRC) Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.08.02.02.0028', 'nomenklatur' => 'Pelatihan Pencegahan dan Mitigasi Bencana Kabupaten/Kota'],
            // --- Kegiatan 1.05.08.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '1.05.08.02.03.0001', 'nomenklatur' => 'Penyusunan Regulasi Penanggulangan Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.08.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '1.05.08.02.03.0010', 'nomenklatur' => 'Koordinasi penanganan Pascabencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.08.02.03.0014', 'nomenklatur' => 'Penguatan Kelembagaan Bencana Kabupaten/Kota'],
            ['nomor_kode' => '1.05.08.02.03.0015', 'nomenklatur' => 'Penyusunan Kajian Kebutuhan Pascabencana (JITUPASNA) dan Rencana Rehabilitasi dan Rekontruksi Pascabencana (R3P) Kab/Kota'],
            ['nomor_kode' => '1.05.08.02.03.0016', 'nomenklatur' => 'Penyusunan Rencana Aksi Penerapan Standar Pelayanan Minimal (SPM) Sub Urusan Bencana Kabupaten/Kota'],
            // --- Kegiatan 1.05.08.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.05.08.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.05.08.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 1.05.08.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.05.08.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.05.08.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '1.05.08.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.05.08.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.05.08.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.05.08.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.05.08.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.05.08.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.05.08.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.05.08.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.05.08.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.05.08.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.05.08.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.05.08.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '1.05.08.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.05.08.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.05.08.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 1.06.09 — PROGRAM PENGELOLAAN TAMAN MAKAM PAHLAWAN =====
            // --- Kegiatan 1.06.09.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '1.06.09.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '1.06.09.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '1.06.09.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '1.06.09.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '1.06.09.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '1.06.09.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '1.06.09.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '1.06.09.02.01.0009', 'nomenklatur' => 'Pemberian Layanan Data dan Pengaduan'],
            // --- Kegiatan 1.06.09.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '1.06.09.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '1.06.09.02.02.0002', 'nomenklatur' => 'Pemberian Layanan Kedaruratan'],
            ['nomor_kode' => '1.06.09.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '1.06.09.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '1.06.09.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '1.06.09.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '1.06.09.02.02.0008', 'nomenklatur' => 'Pemberian Bimbingan Sosial kepada Keluarga Penyandang Masalah Kesejahteraan Sosial (PMKS) Lainnya Bukan Korban HIV/AIDS dan NAPZA'],
            // --- Kegiatan 1.06.09.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '1.06.09.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '1.06.09.02.03.0002', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Tenaga Kesejahteraan Sosial Kecamatan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.09.02.03.0003', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Sumber Kesejahteraan Sosial Keluarga Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.09.02.03.0004', 'nomenklatur' => 'Peningkatan Kemampuan Potensi Sumber Kesejahteraan Sosial Kelembagaan Masyarakat Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '1.06.09.02.03.0005', 'nomenklatur' => 'Peningkatan Kemampuan Sumber Daya Manusia dan Penguatan Lembaga Konsultasi Kesejahteraan Keluarga (LK3)'],
            // --- Kegiatan 1.06.09.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '1.06.09.02.05.0003', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '1.06.09.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '1.06.09.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '1.06.09.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 1.06.09.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '1.06.09.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '1.06.09.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '1.06.09.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '1.06.09.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '1.06.09.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '1.06.09.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '1.06.09.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '1.06.09.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 1.06.09.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '1.06.09.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '1.06.09.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.06.09.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '1.06.09.02.07.0010', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 1.06.09.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.06.09.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '1.06.09.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '1.06.09.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 1.06.09.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '1.06.09.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '1.06.09.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '1.06.09.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '1.06.09.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '1.06.09.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 2.07.10 — PROGRAM PEMBERDAYAAN USAHA MENENGAH, USAHA KECIL, DAN USAHA MIKRO (UMKM) =====
            // --- Kegiatan 2.07.10.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.07.10.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.07.10.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.07.10.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.07.10.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.07.10.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.07.10.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.07.10.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            // --- Kegiatan 2.07.10.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.07.10.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.07.10.02.02.0002', 'nomenklatur' => 'Penyelesaian Perselisihan Hubungan Industrial, Mogok Kerja, dan Penutupan Perusahaan yang Berakibat/Berdampak pada Kepentingan di 1 (satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.07.10.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.07.10.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.07.10.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.07.10.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            // --- Kegiatan 2.07.10.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '2.07.10.02.03.0002', 'nomenklatur' => 'Pelayanan dan Penyediaan Informasi Pasar Kerja Online'],
            ['nomor_kode' => '2.07.10.02.03.0003', 'nomenklatur' => 'Job Fair/Bursa Kerja'],
            ['nomor_kode' => '2.07.10.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            // --- Kegiatan 2.07.10.02.04 — Pelindungan PMI (Pra dan Purna Penempatan) di Daerah Kabupaten/Kota ---
            ['nomor_kode' => '2.07.10.02.04.0001', 'nomenklatur' => 'Peningkatan Pelindungan dan Kompetensi Calon Pekerja Migran Indonesia (PMI)/Pekerja Migran Indonesia (PMI)'],
            ['nomor_kode' => '2.07.10.02.04.0003', 'nomenklatur' => 'Pemberdayaan Pekerja Migran Indonesia Purna Penempatan'],
            // --- Kegiatan 2.07.10.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.07.10.02.05.0001', 'nomenklatur' => 'Pengukuran Kompetensi dan Produktivitas Tenaga Kerja'],
            ['nomor_kode' => '2.07.10.02.05.0003', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '2.07.10.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.07.10.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.07.10.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.07.10.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.07.10.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.07.10.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.07.10.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.07.10.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.07.10.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.07.10.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.07.10.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 2.07.10.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.07.10.02.07.0001', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.07.10.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 2.07.10.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.07.10.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.07.10.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.07.10.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.07.10.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.07.10.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.07.10.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.07.10.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 2.08.11 — PROGRAM PERLINDUNGAN KHUSUS ANAK =====
            // --- Kegiatan 2.08.11.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.08.11.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.08.11.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.08.11.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.08.11.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.08.11.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.08.11.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.08.11.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.08.11.02.01.0008', 'nomenklatur' => 'Sosialisasi kebijakan penyelenggaraan PUG kewenangan Kab/Kota'],
            // --- Kegiatan 2.08.11.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.08.11.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.08.11.02.02.0002', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan Peningkatan Partisipasi Perempuan dan Politik, Hukum, Sosial dan Ekonomi'],
            ['nomor_kode' => '2.08.11.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.08.11.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.08.11.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.08.11.02.02.0007', 'nomenklatur' => 'Koordinasi Pelaksanaan Layanan AMPK'],
            // --- Kegiatan 2.08.11.02.03 — Penguatan dan Pengembangan Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota ---
            ['nomor_kode' => '2.08.11.02.03.0001', 'nomenklatur' => 'Advokasi Kebijakan dan Pendampingan kepada Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.11.02.03.0002', 'nomenklatur' => 'Peningkatan Kapasitas Sumber Daya Lembaga Penyedia Layanan Pemberdayaan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.11.02.03.0003', 'nomenklatur' => 'Pengembangan Komunikasi, Informasi dan Edukasi (KIE) Pemberdayaan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.11.02.03.0004', 'nomenklatur' => 'Penguatan Jejaring antar Lembaga Penyedia Layanan Perlindungan Perempuan Kewenangan Kabupaten/Kota'],
            ['nomor_kode' => '2.08.11.02.03.0005', 'nomenklatur' => 'Penguatan jejaring antar lembaga penyedia layanan perlindungan bagi AMPK tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.08.11.02.03.0006', 'nomenklatur' => 'Peningkatan kapasitas SDM  lembaga penyedia layanan perlindungan dan penanganan bagi AMPK tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.08.11.02.03.0007', 'nomenklatur' => 'Pengembangan KIE (komunikasi, informasi, dan edukasi) perlindungan khusus anak tingkat daerah kabupaten/kota'],
            ['nomor_kode' => '2.08.11.02.03.0008', 'nomenklatur' => 'Penyediaan Bantuan kebutuhan khusus bagi AMPK tingkat daerah kabupaten/kota'],
            // --- Kegiatan 2.08.11.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.08.11.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.08.11.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.08.11.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.08.11.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.08.11.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.08.11.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.08.11.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.08.11.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.08.11.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.08.11.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.08.11.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.08.11.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 2.08.11.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.08.11.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.08.11.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.08.11.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 2.08.11.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.08.11.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.08.11.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.08.11.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.08.11.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.08.11.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.08.11.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.08.11.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.08.11.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.08.11.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 2.09.12 — PROGRAM PENGAWASAN KEAMANAN PANGAN =====
            // --- Kegiatan 2.09.12.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.09.12.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.09.12.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.09.12.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.09.12.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.09.12.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.09.12.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.09.12.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.09.12.02.01.0008', 'nomenklatur' => 'Stabilisasi Pasokan dan Harga Pangan Tingkat Produsen dan Konsumen di Kabupaten/Kota'],
            ['nomor_kode' => '2.09.12.02.01.0009', 'nomenklatur' => 'Penguatan kelembagaan pengawas keamanan dan mutu pangan segar asal tumbuhan'],
            ['nomor_kode' => '2.09.12.02.01.0012', 'nomenklatur' => 'Penyediaan Informasi Harga Pangan Tingkat Produsen dan Konsumen Wilayah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.12.02.01.0013', 'nomenklatur' => 'Penyusunan Prognosa Neraca Pangan Wilayah Kabupaten/Kota'],
            ['nomor_kode' => '2.09.12.02.01.0014', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pemantauan Stok, Pasokan dan Harga Pangan Pokok Strategis'],
            ['nomor_kode' => '2.09.12.02.01.0015', 'nomenklatur' => 'Pemantauan Harga dan Pasokan Pangan'],
            ['nomor_kode' => '2.09.12.02.01.0016', 'nomenklatur' => 'Penyusunan Neraca Bahan Makanan (NBM)'],
            // --- Kegiatan 2.09.12.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.09.12.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.09.12.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.09.12.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.09.12.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.09.12.02.02.0006', 'nomenklatur' => 'Pengelolaan Cadangan Pangan Pemerintah Kab/Kota'],
            ['nomor_kode' => '2.09.12.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            // --- Kegiatan 2.09.12.02.04 — Pelaksanaan Pencapaian Target Konsumsi Pangan Perkapita/Tahun sesuai dengan Angka Kecukupan Gizi ---
            ['nomor_kode' => '2.09.12.02.04.0001', 'nomenklatur' => 'Penyusunan dan Penetapan Target Konsumsi Pangan Per Kapita Per Tahun'],
            ['nomor_kode' => '2.09.12.02.04.0002', 'nomenklatur' => 'Pemberdayaan Masyarakat dalam Penganekaragaman Konsumsi Pangan Berbasis Sumber Daya Lokal'],
            ['nomor_kode' => '2.09.12.02.04.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pemantauan dan Evaluasi Konsumsi per Kapita per Tahun'],
            // --- Kegiatan 2.09.12.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.09.12.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            // --- Kegiatan 2.09.12.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.09.12.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.09.12.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.09.12.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.09.12.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.09.12.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.09.12.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.09.12.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.09.12.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],
            // --- Kegiatan 2.09.12.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.09.12.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.09.12.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.09.12.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.09.12.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.09.12.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.09.12.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.09.12.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.09.12.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.09.12.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.09.12.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.09.12.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.09.12.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 2.11.13 — PROGRAM PENGELOLAAN PERSAMPAHAN =====
            // --- Kegiatan 2.11.13.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.11.13.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.11.13.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.11.13.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.11.13.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.11.13.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.11.13.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.11.13.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.11.13.02.01.0011', 'nomenklatur' => 'Penyusunan dokumen status lingkungan hidup daerah'],
            ['nomor_kode' => '2.11.13.02.01.0012', 'nomenklatur' => 'Penanganan sampah melalui pengangkutan'],
            ['nomor_kode' => '2.11.13.02.01.0015', 'nomenklatur' => 'Pengelolaan Laboratorium Lingkungan Hidup kabupaten/kota'],
            ['nomor_kode' => '2.11.13.02.01.0019', 'nomenklatur' => 'Pengurangan sampah melalui pendauran ulang sampah'],
            // --- Kegiatan 2.11.13.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.11.13.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.11.13.02.02.0002', 'nomenklatur' => 'Pembuatan dan Pelaksanaan KLHS RPJPD/RPJMD'],
            ['nomor_kode' => '2.11.13.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.11.13.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.11.13.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.11.13.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            // --- Kegiatan 2.11.13.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.11.13.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.11.13.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.11.13.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.11.13.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.11.13.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.11.13.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.11.13.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.11.13.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.11.13.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.11.13.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.11.13.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 2.11.13.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.11.13.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.11.13.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 2.11.13.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.11.13.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.11.13.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.11.13.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.11.13.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.11.13.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.11.13.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.11.13.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.11.13.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.11.13.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 2.12.14 — PROGRAM PENGELOLAAN PROFIL KEPENDUDUKAN =====
            // --- Kegiatan 2.12.14.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.12.14.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.12.14.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.12.14.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.12.14.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.12.14.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.12.14.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.12.14.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            // --- Kegiatan 2.12.14.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.12.14.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.12.14.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.12.14.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.12.14.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.12.14.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.12.14.02.02.0008', 'nomenklatur' => 'Sosialisasi Terkait Pencatatan Sipil'],
            // --- Kegiatan 2.12.14.02.03 — Penyelenggaraan Pendaftaran Penduduk ---
            ['nomor_kode' => '2.12.14.02.03.0001', 'nomenklatur' => 'Koordinasi Antar Lembaga Pemerintah dan Lembaga Non-Pemerintah di Kabupaten/Kota dalam Penertiban Pengelolaan Informasi Administrasi Kependudukan'],
            ['nomor_kode' => '2.12.14.02.03.0002', 'nomenklatur' => 'Pelayanan Secara Aktif Pendaftaran Peristiwa Kependudukan dan Pencatatan Peristiwa Penting Terkait Pendaftaran Penduduk'],
            ['nomor_kode' => '2.12.14.02.03.0003', 'nomenklatur' => 'Fasilitasi Terkait Pengelolaan Informasi Administrasi Kependudukan'],
            ['nomor_kode' => '2.12.14.02.03.0004', 'nomenklatur' => 'Penyelenggaraan Pemanfaatan Data Kependudukan'],
            ['nomor_kode' => '2.12.14.02.03.0005', 'nomenklatur' => 'Sosialisasi Pendaftaran Penduduk'],
            ['nomor_kode' => '2.12.14.02.03.0007', 'nomenklatur' => 'Komunikasi, Informasi, dan Edukasi kepada Pemangku Kepentingan dan Masyarakat'],
            ['nomor_kode' => '2.12.14.02.03.0008', 'nomenklatur' => 'Penyajian Data Kependudukan yang Akurat dan dapat Dipertanggungjawabkan'],
            // --- Kegiatan 2.12.14.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.12.14.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.12.14.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.12.14.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.12.14.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.12.14.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.12.14.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.12.14.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '2.12.14.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.12.14.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.12.14.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.12.14.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.12.14.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.12.14.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.12.14.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],
            // --- Kegiatan 2.12.14.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.12.14.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.12.14.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.12.14.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 2.12.14.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.12.14.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.12.14.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.12.14.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.12.14.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.12.14.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.12.14.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.12.14.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.12.14.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.12.14.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.12.14.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 2.14.15 — PROGRAM PEMBERDAYAAN DAN PENINGKATAN KELUARGA SEJAHTERA (KS) =====
            // --- Kegiatan 2.14.15.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.14.15.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.14.15.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.14.15.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.14.15.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.14.15.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.14.15.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.14.15.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.14.15.02.01.0008', 'nomenklatur' => 'Penyediaan dan Pengembangan Materi Pendidikan Kependudukan Jalur Pendidikan Nonformal Sesuai Isu Lokal Kabupaten/Kota'],
            ['nomor_kode' => '2.14.15.02.01.0009', 'nomenklatur' => 'Advokasi, Sosialisasi dan Fasilitasi Pelaksanaan Pendidikan Kependudukan Jalur Formal di Satuan Pendidikan Jenjang SD/MI dan SLTP/MTS, Jalur Nonformal dan Informal'],
            ['nomor_kode' => '2.14.15.02.01.0010', 'nomenklatur' => 'Pengelolaan Operasional dan Sarana di Balai Penyuluhan Bangga Kencana'],
            ['nomor_kode' => '2.14.15.02.01.0011', 'nomenklatur' => 'Pelaksanaan Mekanisme Operasional Program Bangga Kencana melalui Rapat Koordinasi Kecamatan (Rakorcam), Rapat Koordinasi Desa (Rakordes), dan Mini Lokakarya (Minilok)'],
            ['nomor_kode' => '2.14.15.02.01.0012', 'nomenklatur' => 'Advokasi tentang Pemanfaatan Kajian Dampak Kependudukan Beserta Model Solusi Strategis Sebagai Peringatan Dini Dampak Kependudukan kepada Pemangku Kepentingan'],
            ['nomor_kode' => '2.14.15.02.01.0013', 'nomenklatur' => 'Sosialisasi tentang Pemanfaatan Kajian Dampak Kependudukan Beserta Model Solusi Strategis Sebagai Peringatan Dini Dampak Kependudukan kepada Pemangku Kepentingan'],
            ['nomor_kode' => '2.14.15.02.01.0014', 'nomenklatur' => 'Advokasi Program Bangga kencana oleh pokja advokasi kepada Stakeholders dan Mitra Kerja'],
            ['nomor_kode' => '2.14.15.02.01.0015', 'nomenklatur' => 'Pembentukan dan operasional Sekolah Lansia di Kelompok BKL'],
            ['nomor_kode' => '2.14.15.02.01.0016', 'nomenklatur' => 'Implementasi Pendidikan Kependudukan Jalur Informal di Kelompok Kegiatan Masyarakat Binaan'],
            ['nomor_kode' => '2.14.15.02.01.0017', 'nomenklatur' => 'Pelaksanaan Sarasehan Hasil Pemutakhiran Data Keluarga'],
            ['nomor_kode' => '2.14.15.02.01.0018', 'nomenklatur' => 'Penguatan Kerja Sama Pelaksanaan Pendidikan Kependudukan Jalur Pendidikan Nonformal'],
            ['nomor_kode' => '2.14.15.02.01.0019', 'nomenklatur' => 'Implementasi Pendidikan Kependudukan Jalur Pendidikan Formal dan Nonformal'],
            ['nomor_kode' => '2.14.15.02.01.0020', 'nomenklatur' => 'Penyerasian Kebijakan Pembangunan Daerah Kabupaten/Kota terhadap  Pembangunan Keluarga, Kependudukan, dan Keluarga Berencana (Bangga Kencana)'],
            ['nomor_kode' => '2.14.15.02.01.0021', 'nomenklatur' => 'Kerjasama Pelaksanaan Pendidikan Kependudukan Jalur Pendidikan Formal'],
            ['nomor_kode' => '2.14.15.02.01.0022', 'nomenklatur' => 'Pelaksanaan penyediaan data dan sosialisasi Indeks Pembangunan Berwawasan Kependudukan (IPBK)'],
            ['nomor_kode' => '2.14.15.02.01.0023', 'nomenklatur' => 'Pelaksanaan Rapat Pengendalian Program Bangga Kencana'],
            ['nomor_kode' => '2.14.15.02.01.0024', 'nomenklatur' => 'Penyediaan Biaya Operasional bagi Pengelola dan Pelaksana (Kader) Ketahanan dan Kesejaheraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.15.02.01.0025', 'nomenklatur' => 'Pelaksanaan Koordinasi Evaluasi Pencapaian iBangga (Indeks Pembangunan Keluarga)'],
            ['nomor_kode' => '2.14.15.02.01.0026', 'nomenklatur' => 'Penyediaan Biaya Operasional bagi Kelompok Kegiatan Ketahanan dan Kesejahteraan Keluarga (BKB, BKR, BKL, PPKS, PIK-R dan Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA)'],
            ['nomor_kode' => '2.14.15.02.01.0027', 'nomenklatur' => 'Penyediaan dan Pengembangan Materi iBangga (Indeks Pembangunan Keluarga)'],
            ['nomor_kode' => '2.14.15.02.01.0028', 'nomenklatur' => 'Pembentukan Kelompok Ketahanan dan Kesejahteraan Keluarga (Bina Keluarga Balita (BKB), Bina Keluarga Remaja (BKR), Pusat Informasi dan Konseling Remaja (PIK-R) Bina Keluarga Lansia (BKL), Usaha Peningkatan Pendapatan Keluarga Akseptor (UPPKA) dan Pemberdayaan Ekonomi Keluarga)'],
            // --- Kegiatan 2.14.15.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.14.15.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.14.15.02.02.0002', 'nomenklatur' => 'Penyediaan dan Pengolahan Data Kependudukan'],
            ['nomor_kode' => '2.14.15.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.14.15.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.14.15.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.14.15.02.02.0006', 'nomenklatur' => 'Pengembangan Model Solusi Strategis Pengendalian Dampak Kependudukan'],
            ['nomor_kode' => '2.14.15.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.14.15.02.02.0009', 'nomenklatur' => 'Pembinaan dan Pengawasan Penyelenggaraan Sistem Informasi Keluarga'],
            ['nomor_kode' => '2.14.15.02.02.0010', 'nomenklatur' => 'Pemanfaatan Data Hasil Pemutakhiran Data Keluarga'],
            ['nomor_kode' => '2.14.15.02.02.0011', 'nomenklatur' => 'Penyediaan Data dan Informasi Keluarga'],
            ['nomor_kode' => '2.14.15.02.02.0012', 'nomenklatur' => 'Pencatatan dan Pengumpulan Data Keluarga'],
            ['nomor_kode' => '2.14.15.02.02.0013', 'nomenklatur' => 'Pengolahan dan Pelaporan Data Pengendalian Lapangan dan Pelayanan KB'],
            ['nomor_kode' => '2.14.15.02.02.0015', 'nomenklatur' => 'Pembentukan dan operasionalisasi  Rumah Data Kependudukan di Kampung KB  Untuk Memperkuat Integrasi Program Bangga Kencana di Sektor Lain'],
            ['nomor_kode' => '2.14.15.02.02.0016', 'nomenklatur' => 'Pelaksanaan Sistem Peringatan Dini Pengendalian Penduduk di tingkat kabupaten/kota'],
            ['nomor_kode' => '2.14.15.02.02.0017', 'nomenklatur' => 'Perumusan Parameter pengendalian penduduk dan KB'],
            ['nomor_kode' => '2.14.15.02.02.0018', 'nomenklatur' => 'Pembinaan dan Pengawasan Pencatatan dan Pelaporan Program Bangga Kencana'],
            ['nomor_kode' => '2.14.15.02.02.0019', 'nomenklatur' => 'Pemetaan Program Pembangunan Keluarga, Kependudukan, dan Keluarga Berencana (Bangga Kencana)'],
            ['nomor_kode' => '2.14.15.02.02.0020', 'nomenklatur' => 'Penyusunan Profil program Pembangunan Keluarga, Kependudukan, dan Keluarga Berencana (Bangga Kencana)'],
            // --- Kegiatan 2.14.15.02.03 — Pengendalian dan Pendistribusian Kebutuhan Alat dan Obat Kontrasepsi serta Pelaksanaan Pelayanan KB di Daerah Kabupaten/Kota ---
            ['nomor_kode' => '2.14.15.02.03.0001', 'nomenklatur' => 'Pengendalian Pendistribusian Alat dan Obat Kontrasepsi dan Sarana Penunjang Pelayanan KB ke Fasilitas Kesehatan Termasuk Jaringan dan Jejaringnya'],
            ['nomor_kode' => '2.14.15.02.03.0003', 'nomenklatur' => 'Peningkatan Kesertaan Penggunaan Metode Kontrasepsi Jangka Panjang (MKJP)'],
            ['nomor_kode' => '2.14.15.02.03.0004', 'nomenklatur' => 'Penyediaan Dukungan Ayoman Komplikasi Berat dan Kegagalan Penggunaan MKJP'],
            ['nomor_kode' => '2.14.15.02.03.0005', 'nomenklatur' => 'Penyusunan Rencana Kebutuhan Alat dan Obat Kontrasepsi (Alokon) dan Sarana Penunjang Pelayanan KB'],
            ['nomor_kode' => '2.14.15.02.03.0006', 'nomenklatur' => 'Penyediaan Sarana Penunjang Pelayanan KB'],
            ['nomor_kode' => '2.14.15.02.03.0007', 'nomenklatur' => 'Pembinaan Pasca Pelayanan bagi Peserta KB'],
            ['nomor_kode' => '2.14.15.02.03.0008', 'nomenklatur' => 'Pembinaan Pelayanan Keluarga Berencana dan Kesehatan Reproduksi di Fasilitas Kesehatan Termasuk Jaringan dan Jejaringnya'],
            ['nomor_kode' => '2.14.15.02.03.0010', 'nomenklatur' => 'Peningkatan Kompetensi Tenaga Pelayanan Keluarga Berencana dan Kesehatan Reproduksi'],
            ['nomor_kode' => '2.14.15.02.03.0011', 'nomenklatur' => 'Dukungan Operasional Pelayanan KB Bergerak'],
            ['nomor_kode' => '2.14.15.02.03.0013', 'nomenklatur' => 'Peningkatan Kesertaan KB Pria'],
            ['nomor_kode' => '2.14.15.02.03.0014', 'nomenklatur' => 'Pemerintah Daerah yang Mendapatkan Fasilitasi dan Pembinaan Pendampingan Ibu Hamil dan Ibu Pasca Persalinan'],
            ['nomor_kode' => '2.14.15.02.03.0015', 'nomenklatur' => 'Peningkatan Kompetensi Pengelola dan Petugas Logistik Alat dan Obat Kontrasepsi serta Sarana Penunjang Pelayanan KB'],
            ['nomor_kode' => '2.14.15.02.03.0016', 'nomenklatur' => 'Promosi dan Konseling KB Pasca Persalinan'],
            // --- Kegiatan 2.14.15.02.04 — Pemberdayaan dan Peningkatan Peran Serta Organisasi Kemasyarakatan Tingkat Daerah Kabupaten/Kota dalam Pelaksanaan Pelayanan dan Pembinaan Kesertaan Ber-KB ---
            ['nomor_kode' => '2.14.15.02.04.0001', 'nomenklatur' => 'Penguatan Peran Serta Organisasi Kemasyarakatan dan Mitra Kerja Lainnya dalam Pelaksanaan Pelayanan dan Pembinaan Kesertaan Ber-KB'],
            ['nomor_kode' => '2.14.15.02.04.0002', 'nomenklatur' => 'Integrasi Pembangunan Lintas Sektor di Kampung KB'],
            ['nomor_kode' => '2.14.15.02.04.0004', 'nomenklatur' => 'Pembinaan Terpadu Kampung KB'],
            ['nomor_kode' => '2.14.15.02.04.0005', 'nomenklatur' => 'Fasilitasi Pengelolaan Dapur Sehat Atasi Stunting (DASHAT) di Kampung Keluarga Berkualitas'],
            ['nomor_kode' => '2.14.15.02.04.0006', 'nomenklatur' => 'Pelaksanaan dan Pengelolaan Program Bangga Kencana di Kampung Keluarga Berkualitas'],
            // --- Kegiatan 2.14.15.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.14.15.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.14.15.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.14.15.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.14.15.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.14.15.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.14.15.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.14.15.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.14.15.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.14.15.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.14.15.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.14.15.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 2.14.15.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.14.15.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.14.15.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.14.15.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            // --- Kegiatan 2.14.15.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.14.15.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.14.15.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.14.15.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.14.15.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.14.15.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.14.15.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.14.15.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.14.15.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.14.15.02.09.0007', 'nomenklatur' => 'Pemeliharaan Aset Tetap Lainnya'],
            ['nomor_kode' => '2.14.15.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 2.15.16 — PROGRAM PENYELENGGARAAN LALU LINTAS DAN ANGKUTAN JALAN (LLAJ) =====
            // --- Kegiatan 2.15.16.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.15.16.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.15.16.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.15.16.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.15.16.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.15.16.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.15.16.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.15.16.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            // --- Kegiatan 2.15.16.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.15.16.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.15.16.02.02.0002', 'nomenklatur' => 'Penyediaan Perlengkapan Jalan di Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.16.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.15.16.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.15.16.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.15.16.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            // --- Kegiatan 2.15.16.02.03 — Pengelolaan Terminal Penumpang Tipe C ---
            ['nomor_kode' => '2.15.16.02.03.0007', 'nomenklatur' => 'Revitalisasi Terminal Tipe C (Fasilitas Utama dan Penunjang)'],
            ['nomor_kode' => '2.15.16.02.03.0010', 'nomenklatur' => 'Peningkatan Kapasitas Kompetensi SDM Pengelola Terminal Penumpang Tipe C'],
            // --- Kegiatan 2.15.16.02.04 — Penerbitan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir ---
            ['nomor_kode' => '2.15.16.02.04.0001', 'nomenklatur' => 'Fasilitasi Pemenuhan Persyaratan Perolehan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir Kewenangan Kabupaten/Kota dalam Sistem Pelayanan Perizinan Berusaha Terintegrasi Secara Elektronik'],
            ['nomor_kode' => '2.15.16.02.04.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pengawasan Pelaksanaan Izin Penyelenggaraan dan Pembangunan Fasilitas Parkir Kewenangan Kabupaten/Kota'],
            // --- Kegiatan 2.15.16.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.15.16.02.05.0001', 'nomenklatur' => 'Penyediaan Sarana dan Prasarana Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.16.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '2.15.16.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.15.16.02.05.0004', 'nomenklatur' => 'Penyediaan Bukti Lulus Uji Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.16.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.15.16.02.05.0007', 'nomenklatur' => 'Pemeliharaan Sarana dan Prasarana Pengujian Berkala Kendaraan Bermotor'],
            ['nomor_kode' => '2.15.16.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.15.16.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.15.16.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.15.16.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.15.16.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.15.16.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.15.16.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.15.16.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.15.16.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.15.16.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.15.16.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.15.16.02.06.0015', 'nomenklatur' => 'Forum Lalu Lintas dan Angkutan Jalan untuk Jaringan Jalan Kabupaten/Kota'],
            ['nomor_kode' => '2.15.16.02.06.0017', 'nomenklatur' => 'Penataan Manajemen dan Rekayasa Lalu Lintas untuk Jaringan Jalan Kabupaten/Kota'],
            // --- Kegiatan 2.15.16.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.15.16.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.15.16.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.15.16.02.07.0003', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penilaian Hasil Andalalin'],
            ['nomor_kode' => '2.15.16.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.15.16.02.07.0006', 'nomenklatur' => 'Pengawasan Pelaksanaan Rekomendasi Persetujuan Teknis Andalalin'],
            ['nomor_kode' => '2.15.16.02.07.0009', 'nomenklatur' => 'Pengadaan Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.15.16.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.15.16.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.15.16.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.15.16.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.15.16.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.15.16.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.15.16.02.09.0002', 'nomenklatur' => 'Pengendalian dan Pengawasan Ketersediaan Angkutan Umum untuk Jasa Angkutan Orang dan/atau Barang Antar Kota dalam 1 (Satu) Kabupaten/Kota'],
            ['nomor_kode' => '2.15.16.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.15.16.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.15.16.02.11 — Penetapan Rencana Umum Jaringan Trayek Perkotaan dalam 1 (Satu) Daerah Kabupaten/Kota ---
            ['nomor_kode' => '2.15.16.02.11.0003', 'nomenklatur' => 'Pengendalian Pelaksanaan Rencana Umum Jaringan Trayek Perkotaan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            // --- Kegiatan 2.15.16.02.16 — Penetapan Tarif Kelas Ekonomi untuk Angkutan Orang yang Melayani Trayek serta Angkutan Perkotaan dan Perdesaan dalam 1 (Satu) Daerah Kabupaten/Kota ---
            ['nomor_kode' => '2.15.16.02.16.0002', 'nomenklatur' => 'Penyediaan Data dan Informasi Tarif Kelas Ekonomi Angkutan Orang dan Angkutan Perkotaan dan Perdesaan dalam 1 (Satu) Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.15.16.02.16.0003', 'nomenklatur' => 'Pengendalian dan Pengawasan Tarif Kelas Ekonomi Angkutan Orang dan Angkutan Perkotaan dan Perdesaan dalam 1 (Satu) Daerah Kabupaten/Kota'],

            // ===== Program 2.16.17 — PROGRAM PENYELENGGARAAN PERSANDIAN UNTUK PENGAMANAN INFORMASI =====
            // --- Kegiatan 2.16.17.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.16.17.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.16.17.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.16.17.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.16.17.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.16.17.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.16.17.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.16.17.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.16.17.02.01.0008', 'nomenklatur' => 'Peningkatan Peran Statistik Sektoral terhadap Sistem Statistik Nasional'],
            ['nomor_kode' => '2.16.17.02.01.0009', 'nomenklatur' => 'Peningkatan Kualitas Data Statistik Sektoral'],
            ['nomor_kode' => '2.16.17.02.01.0010', 'nomenklatur' => 'Penyelenggaraan Statistik Sektoral yang sesuai dengan Prinsip Satu Data Indonesia'],
            ['nomor_kode' => '2.16.17.02.01.0011', 'nomenklatur' => 'Pelaksanaan Proses Bisnis Statistik Sektoral Sesuai Standar'],
            ['nomor_kode' => '2.16.17.02.01.0014', 'nomenklatur' => 'Relasi Media'],
            ['nomor_kode' => '2.16.17.02.01.0015', 'nomenklatur' => 'Kemitraan Komunikasi dengan Komunitas Informasi Masyarakat'],
            ['nomor_kode' => '2.16.17.02.01.0017', 'nomenklatur' => 'Pelayanan Informasi Publik'],
            ['nomor_kode' => '2.16.17.02.01.0018', 'nomenklatur' => 'Sosialisasi Peraturan Bidang Informasi dan Komunikasi Publik'],
            ['nomor_kode' => '2.16.17.02.01.0019', 'nomenklatur' => 'Monitoring Informasi Kebijakan, Opini, dan Aspirasi Publik'],
            ['nomor_kode' => '2.16.17.02.01.0020', 'nomenklatur' => 'Diseminasi Informasi'],
            ['nomor_kode' => '2.16.17.02.01.0021', 'nomenklatur' => 'Pengelolaan Media Komunikasi Publik'],
            ['nomor_kode' => '2.16.17.02.01.0022', 'nomenklatur' => 'Penyusunan Strategi Komunikasi Publik'],
            ['nomor_kode' => '2.16.17.02.01.0023', 'nomenklatur' => 'Penyusunan Konten'],
            ['nomor_kode' => '2.16.17.02.01.0024', 'nomenklatur' => 'Penguatan Kapasitas Sumber Daya Manusia Komunikasi Publik'],
            // --- Kegiatan 2.16.17.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.16.17.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.16.17.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.16.17.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '2.16.17.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.16.17.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.16.17.02.02.0013', 'nomenklatur' => 'Koordinasi Pemanfaatan Pusat Data Nasional'],
            ['nomor_kode' => '2.16.17.02.02.0015', 'nomenklatur' => 'Fasilitasi penyelenggaraan SPBE di lingkungan Pemda'],
            ['nomor_kode' => '2.16.17.02.02.0016', 'nomenklatur' => 'Penyelenggaraan  pusat kendali Pemerintah Daerah'],
            ['nomor_kode' => '2.16.17.02.02.0017', 'nomenklatur' => 'Koordinasi Pengelolaan Data dan Informasi'],
            ['nomor_kode' => '2.16.17.02.02.0018', 'nomenklatur' => 'Koordinasi penyusunan dan/atau reviu arsitektur dan peta rencana SPBE Pemerintah Daerah'],
            ['nomor_kode' => '2.16.17.02.02.0019', 'nomenklatur' => 'Koordinasi pelaksanaan Manajemen SPBE'],
            ['nomor_kode' => '2.16.17.02.02.0020', 'nomenklatur' => 'Pembangunan dan/atau Pengembangan Aplikasi Khusus yang sesuai dengan arsitektur dan peta rencana SPBE pemerintah daerah'],
            ['nomor_kode' => '2.16.17.02.02.0021', 'nomenklatur' => 'Penyelenggaraan Sistem Penghubung Layanan Pemerintah Daerah'],
            ['nomor_kode' => '2.16.17.02.02.0024', 'nomenklatur' => 'Penyelenggaraan Jaringan Intra Pemerintah Daerah Kab/Kota'],
            ['nomor_kode' => '2.16.17.02.02.0029', 'nomenklatur' => 'Koordinasi pemanfaatan Aplikasi Umum SPBE'],
            ['nomor_kode' => '2.16.17.02.02.0030', 'nomenklatur' => 'Penyediaan Akses Internet untuk Perangkat Daerah dalam rangka penyelenggaraan SPBE'],
            // --- Kegiatan 2.16.17.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '2.16.17.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '2.16.17.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '2.16.17.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
            // --- Kegiatan 2.16.17.02.04 — Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah ---
            ['nomor_kode' => '2.16.17.02.04.0007', 'nomenklatur' => 'Pelaporan Pengelolaan Retribusi Daerah'],
            // --- Kegiatan 2.16.17.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.16.17.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.16.17.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.16.17.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.16.17.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.16.17.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.16.17.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.16.17.02.06.0003', 'nomenklatur' => 'Penyediaan Peralatan Rumah Tangga'],
            ['nomor_kode' => '2.16.17.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.16.17.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.16.17.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.16.17.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.16.17.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.16.17.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 2.16.17.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.16.17.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.16.17.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.16.17.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.16.17.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            ['nomor_kode' => '2.16.17.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.16.17.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.16.17.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.16.17.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.16.17.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.16.17.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.16.17.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.16.17.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.16.17.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.16.17.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.16.17.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.16.17.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 2.18.19 — PROGRAM PENGELOLAAN DATA DAN SISTEM INFORMASI PENANAMAN MODAL =====
            // --- Kegiatan 2.18.19.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.18.19.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.18.19.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.18.19.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.18.19.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.18.19.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.18.19.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.18.19.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.18.19.02.01.0008', 'nomenklatur' => 'Pemantauan, analisis, evaluasi, dan pelaporan di bidang perizinan berusaha berbasis risiko'],
            // --- Kegiatan 2.18.19.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.18.19.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.18.19.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.18.19.02.02.0004', 'nomenklatur' => 'Penyusunan Peta Potensi Investasi Kabupaten/Kota'],
            ['nomor_kode' => '2.18.19.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.18.19.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            // --- Kegiatan 2.18.19.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '2.18.19.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '2.18.19.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            // --- Kegiatan 2.18.19.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.18.19.02.05.0001', 'nomenklatur' => 'Peningkatan Sarana dan Prasarana Disiplin Pegawai'],
            ['nomor_kode' => '2.18.19.02.05.0002', 'nomenklatur' => 'Pengadaan Pakaian Dinas beserta Atribut Kelengkapannya'],
            ['nomor_kode' => '2.18.19.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '2.18.19.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '2.18.19.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.18.19.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.18.19.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.18.19.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.18.19.02.06.0002', 'nomenklatur' => 'Penyediaan Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.18.19.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.18.19.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.18.19.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.18.19.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.18.19.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.18.19.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            ['nomor_kode' => '2.18.19.02.06.0010', 'nomenklatur' => 'Penatausahaan Arsip Dinamis pada SKPD'],
            ['nomor_kode' => '2.18.19.02.06.0011', 'nomenklatur' => 'Dukungan Pelaksanaan Sistem Pemerintahan Berbasis Elektronik pada SKPD'],
            // --- Kegiatan 2.18.19.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.18.19.02.07.0002', 'nomenklatur' => 'Pengadaan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.18.19.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.18.19.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.18.19.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.18.19.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.18.19.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.18.19.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.18.19.02.08.0003', 'nomenklatur' => 'Penyediaan Jasa Peralatan dan Perlengkapan Kantor'],
            ['nomor_kode' => '2.18.19.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.18.19.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.18.19.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.18.19.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.18.19.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.18.19.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.18.19.02.09.0008', 'nomenklatur' => 'Pemeliharaan Aset Tak Berwujud'],
            ['nomor_kode' => '2.18.19.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.18.19.02.09.0010', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 2.19.20 — PROGRAM PENGEMBANGAN SUMBER DAYA PARIWISATA DAN EKONOMI KREATIF =====
            // --- Kegiatan 2.19.20.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.19.20.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.19.20.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.19.20.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.19.20.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.19.20.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.19.20.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.19.20.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            ['nomor_kode' => '2.19.20.02.01.0008', 'nomenklatur' => 'Partisipasi dan Keikutsertaan dalam Kegiatan Kepramukaan'],
            ['nomor_kode' => '2.19.20.02.01.0010', 'nomenklatur' => 'Pelaksanaan Koordinasi Strategis Lintas Sektor Penyelenggaraan Pelayanan Kepemudaan melalui pembentukan tim koordinasi kabupaten/kota Penyelenggaraan Pelayanan Kepemudaan serta penyusunan dan implementasi Rencana Aksi Daerah/RAD Tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.20.02.01.0011', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan Penyelenggaraan Pengembangan Kewirausahaan Pemuda Bagi Wirausaha pemula Tingkat Kabupaten/kota'],
            ['nomor_kode' => '2.19.20.02.01.0012', 'nomenklatur' => 'Pemberian Penghargaan Kepemudaan bagi yang berprestasi dan/atau berjasa dalam memajukan potensi pemuda'],
            ['nomor_kode' => '2.19.20.02.01.0013', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Penyelenggaraan Pengembangan kepemimpinan pemuda tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.20.02.01.0014', 'nomenklatur' => 'Pelaksanaan koordinasi dan sinkronisasi Pemenuhan Hak Pemuda di tingkat kabupaten/kota'],
            ['nomor_kode' => '2.19.20.02.01.0015', 'nomenklatur' => 'Koordinasi, Sinkronisasi dan Penyelenggaraan Pengembangan Kepeloporan Pemuda bagi Pemuda Pelopor Tingkat Kabupaten/kota'],
            ['nomor_kode' => '2.19.20.02.01.0016', 'nomenklatur' => 'Penyediaan dan Pengelolaan Prasarana dan Sarana Kepemudaan tingkat kabupaten/kota'],
            // --- Kegiatan 2.19.20.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.19.20.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.19.20.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.19.20.02.02.0004', 'nomenklatur' => 'Koordinasi, Sinkronisasi, dan penyelenggaraan Pemberdayaan organisasi kepemudaan melalui kemitraan berbasis peneguhan kemandirian ekonomi pemuda tingkat Kabupaten/Kota'],
            ['nomor_kode' => '2.19.20.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.19.20.02.02.0006', 'nomenklatur' => 'Keikutsertaan anggota kontingen kabupaten/kota dalam Penyelenggaraan pekan dan kejuaraan olahraga'],
            ['nomor_kode' => '2.19.20.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.19.20.02.02.0008', 'nomenklatur' => 'Peningkatan Kapasitas SDM Pengelola Kawasan Strategis Pariwisata Kabupaten/Kota'],
            // --- Kegiatan 2.19.20.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '2.19.20.02.03.0004', 'nomenklatur' => 'Pengadaan/Pemeliharaan/Rehabilitasi Sarana dan Prasarana dalam Pengelolaan Destinasi Pariwisata Kabupaten/Kota'],
            ['nomor_kode' => '2.19.20.02.03.0006', 'nomenklatur' => 'Seleksi Atlet Daerah'],
            ['nomor_kode' => '2.19.20.02.03.0007', 'nomenklatur' => 'Pemanfaatan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '2.19.20.02.03.0009', 'nomenklatur' => 'Pembinaan dan Pengembangan Olahragawan Berprestasi kabupaten/kota'],
            ['nomor_kode' => '2.19.20.02.03.0010', 'nomenklatur' => 'pembentukan dan Penyediaan sistem data Keolahragaan terpadu di kabupaten/kota'],
            // --- Kegiatan 2.19.20.02.04 — Administrasi Pendapatan Daerah Kewenangan Perangkat Daerah ---
            ['nomor_kode' => '2.19.20.02.04.0006', 'nomenklatur' => 'Peningkatan Kerja Sama Organisasi Keolahragaan Kabupaten/Kota dengan Lembaga Terkait'],
            ['nomor_kode' => '2.19.20.02.04.0007', 'nomenklatur' => 'Pelaporan Pengelolaan Retribusi Daerah'],
            // --- Kegiatan 2.19.20.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.19.20.02.05.0007', 'nomenklatur' => 'Pengembangan Olahraga Wisata, Tantangan dan Petualangan'],
            ['nomor_kode' => '2.19.20.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.19.20.02.05.0010', 'nomenklatur' => 'Pemassalan olahraga dan penyelenggaraan festival Olahraga Rekreasi yang berjenjang dan berkelanjutan pada tingkat daerah, nasional, dan internasional'],
            // --- Kegiatan 2.19.20.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.19.20.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.19.20.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.19.20.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.19.20.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.19.20.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.19.20.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '2.19.20.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            // --- Kegiatan 2.19.20.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.19.20.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '2.19.20.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.19.20.02.07.0011', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.19.20.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.19.20.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.19.20.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.19.20.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.19.20.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.19.20.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.19.20.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.19.20.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.19.20.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.19.20.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],
            ['nomor_kode' => '2.19.20.02.09.0011', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Sarana dan Prasarana Pendukung Gedung Kantor atau Bangunan Lainnya'],

            // ===== Program 2.23.21 — PROGRAM PERIZINAN PENGGUNAAN ARSIP =====
            // --- Kegiatan 2.23.21.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '2.23.21.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '2.23.21.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '2.23.21.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '2.23.21.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '2.23.21.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '2.23.21.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '2.23.21.02.01.0011', 'nomenklatur' => 'Pengembangan Perpustakaan di Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.23.21.02.01.0016', 'nomenklatur' => 'Peningkatan Kapasitas Tenaga Perpustakaan dan Pustakawan Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.23.21.02.01.0017', 'nomenklatur' => 'Penyusunan Data dan Informasi Perpustakaan'],
            ['nomor_kode' => '2.23.21.02.01.0018', 'nomenklatur' => 'Pengelolaan dan Pengembangan Bahan Perpustakaan'],
            ['nomor_kode' => '2.23.21.02.01.0020', 'nomenklatur' => 'Pengembangan dan Pemeliharaan Layanan Perpustakaan Elektronik'],
            // --- Kegiatan 2.23.21.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '2.23.21.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '2.23.21.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '2.23.21.02.02.0004', 'nomenklatur' => 'Akuisisi, Pengolahan, Preservasi, dan Akses Arsip Statis'],
            ['nomor_kode' => '2.23.21.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '2.23.21.02.02.0006', 'nomenklatur' => 'Pemilihan Duta Baca/Bunda Baca/Bunda Literasi Tingkat Daerah Kabupaten/Kota'],
            ['nomor_kode' => '2.23.21.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            ['nomor_kode' => '2.23.21.02.02.0009', 'nomenklatur' => 'Pemberian Penghargaan Gerakan Budaya Gemar Membaca'],
            ['nomor_kode' => '2.23.21.02.02.0010', 'nomenklatur' => 'Sosialisasi Budaya Baca dan Literasi pada Satuan Pendidikan Dasar dan Masyarakat'],
            // --- Kegiatan 2.23.21.02.03 — Pengelolaan Simpul Jaringan Informasi Kearsipan Nasional Tingkat Kabupaten/Kota ---
            ['nomor_kode' => '2.23.21.02.03.0001', 'nomenklatur' => 'Penyediaan Informasi, Akses dan Layanan Kearsipan Tingkat Daerah Kabupaten/Kota Melalui JIKN'],
            ['nomor_kode' => '2.23.21.02.03.0002', 'nomenklatur' => 'Pemberdayaan Kapasitas Unit Kearsipan dan Lembaga Kearsipan Daerah Kabupaten/Kota'],
            // --- Kegiatan 2.23.21.02.04 — Autentikasi Arsip Statis dan Arsip Hasil Alih Media Kabupaten/Kota ---
            ['nomor_kode' => '2.23.21.02.04.0001', 'nomenklatur' => 'Penilaian dan Penetapan Autentisitas Arsip Statis Sesuai Persyaratan Penjaminan Keabsahan Arsip'],
            // --- Kegiatan 2.23.21.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '2.23.21.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '2.23.21.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            // --- Kegiatan 2.23.21.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '2.23.21.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '2.23.21.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '2.23.21.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '2.23.21.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '2.23.21.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '2.23.21.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            // --- Kegiatan 2.23.21.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '2.23.21.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.23.21.02.07.0010', 'nomenklatur' => 'Pengadaan Sarana dan Prasarana Gedung Kantor atau Bangunan Lainnya'],
            // --- Kegiatan 2.23.21.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.23.21.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '2.23.21.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '2.23.21.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            // --- Kegiatan 2.23.21.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '2.23.21.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '2.23.21.02.09.0002', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, Pajak dan Perizinan Kendaraan Dinas Operasional atau Lapangan'],
            ['nomor_kode' => '2.23.21.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '2.23.21.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '2.23.21.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 3.25.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.25.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.25.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.25.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.25.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.25.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.25.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.25.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.25.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            
            // --- Kegiatan 3.25.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '3.25.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '3.25.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '3.25.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '3.25.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            
            // --- Kegiatan 3.25.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '3.25.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '3.25.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '3.25.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            
            // --- Kegiatan 3.25.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '3.25.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '3.25.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '3.25.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '3.25.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '3.25.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '3.25.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '3.25.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],

            // ===== Program 3.25.02 — PROGRAM PENYULUHAN PERTANIAN =====
            // --- Kegiatan 3.25.02.02.01 — Penyelenggaraan Penyuluhan Pertanian ---
            ['nomor_kode' => '3.25.02.02.01.0001', 'nomenklatur' => 'Penyusunan Program Penyuluhan Pertanian'],
            ['nomor_kode' => '3.25.02.02.01.0002', 'nomenklatur' => 'Pelaksanaan Penyuluhan Pertanian'],
            ['nomor_kode' => '3.25.02.02.01.0003', 'nomenklatur' => 'Evaluasi dan Pelaporan Penyuluhan Pertanian'],
            
            // --- Kegiatan 3.25.02.02.02 — Peningkatan Kapasitas Penyuluh Pertanian ---
            ['nomor_kode' => '3.25.02.02.02.0001', 'nomenklatur' => 'Pendidikan dan Pelatihan Penyuluh Pertanian'],
            ['nomor_kode' => '3.25.02.02.02.0002', 'nomenklatur' => 'Pembinaan dan Pengembangan Penyuluh Pertanian'],
            ['nomor_kode' => '3.25.02.02.02.0003', 'nomenklatur' => 'Sertifikasi Penyuluh Pertanian'],

            // ===== Program 3.26.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.26.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.26.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.26.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.26.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.26.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.26.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.26.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.26.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 3.27.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.27.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.27.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.27.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.27.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 3.27.02 — PROGRAM PENGELOLAAN PENANGKAPAN IKAN DI WILAYAH SUNGAI, DANAU, WADUK, RAWA, DAN GENANGAN AIR LAINNYA =====
            // --- Kegiatan 3.27.02.02.01 — Pengelolaan Penangkapan Ikan ---
            ['nomor_kode' => '3.27.02.02.01.0001', 'nomenklatur' => 'Penyediaan Data dan Informasi Sumber Daya Ikan'],
            ['nomor_kode' => '3.27.02.02.01.0002', 'nomenklatur' => 'Penyediaan Sarana Usaha Perikanan Tangkap'],
            ['nomor_kode' => '3.27.02.02.01.0003', 'nomenklatur' => 'Pembinaan Nelayan dan Kelompok Nelayan'],
            
            // --- Kegiatan 3.27.02.02.02 — Pemberdayaan Nelayan Kecil ---
            ['nomor_kode' => '3.27.02.02.02.0001', 'nomenklatur' => 'Pengembangan Kapasitas Nelayan Kecil'],
            ['nomor_kode' => '3.27.02.02.02.0002', 'nomenklatur' => 'Pelaksanaan Fasilitasi Pembentukan dan Pengembangan Kelembagaan Nelayan Kecil'],
            ['nomor_kode' => '3.27.02.02.02.0003', 'nomenklatur' => 'Pelaksanaan Fasilitasi Bantuan Pendanaan, Bantuan Pembiayaan, Kemitraan Usaha'],
            
            // --- Kegiatan 3.27.02.02.03 — Pengelolaan dan Penyelenggaraan Tempat Pelelangan Ikan (TPI) ---
            ['nomor_kode' => '3.27.02.02.03.0001', 'nomenklatur' => 'Peningkatan Ketersediaan Ikan untuk Konsumsi dan Usaha Pengolahan'],
            ['nomor_kode' => '3.27.02.02.03.0002', 'nomenklatur' => 'Pelayanan Penyelenggaraan Tempat Pelelangan Ikan (TPI)'],
            ['nomor_kode' => '3.27.02.02.03.0003', 'nomenklatur' => 'Pembinaan dan Pengawasan TPI'],
            
            // --- Kegiatan 3.27.02.02.04 — Pengelolaan Pembudidayaan Ikan ---
            ['nomor_kode' => '3.27.02.02.04.0001', 'nomenklatur' => 'Penyediaan Data dan Informasi Pembudidayaan Ikan'],
            ['nomor_kode' => '3.27.02.02.04.0002', 'nomenklatur' => 'Penyediaan Prasarana Pembudidayaan Ikan'],
            ['nomor_kode' => '3.27.02.02.04.0003', 'nomenklatur' => 'Penjaminan Ketersediaan Sarana Pembudidayaan Ikan'],
            ['nomor_kode' => '3.27.02.02.04.0004', 'nomenklatur' => 'Pembinaan dan Pemantauan Pembudidayaan Ikan'],

            // ===== Program 3.28.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.28.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.28.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.28.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.28.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.28.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.28.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.28.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.28.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 3.29.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.29.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.29.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.29.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.29.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.29.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.29.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.29.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.29.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 3.30.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.30.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.30.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.30.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.30.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 3.31.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 3.31.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '3.31.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '3.31.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '3.31.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '3.31.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '3.31.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '3.31.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '3.31.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],




            // ===== Program 4.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 4.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '4.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '4.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '4.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            
            // --- Kegiatan 4.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '4.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '4.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '4.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '4.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            
            // --- Kegiatan 4.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '4.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '4.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '4.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            
            // --- Kegiatan 4.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '4.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '4.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '4.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '4.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '4.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '4.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '4.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            
            // --- Kegiatan 4.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '4.01.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '4.01.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '4.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            
            // --- Kegiatan 4.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '4.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '4.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '4.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            
            // --- Kegiatan 4.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '4.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '4.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '4.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '4.01.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 4.01.02 — PROGRAM PENINGKATAN INVESTASI DAN REALISASI INVESTASI =====
            // --- Kegiatan 4.01.02.02.01 — Peningkatan Iklim Investasi dan Realisasi Investasi ---
            ['nomor_kode' => '4.01.02.02.01.0001', 'nomenklatur' => 'Promosi Investasi'],
            ['nomor_kode' => '4.01.02.02.01.0002', 'nomenklatur' => 'Fasilitasi Penanaman Modal'],
            ['nomor_kode' => '4.01.02.02.01.0003', 'nomenklatur' => 'Pembinaan dan Pengawasan Pelaksanaan Penanaman Modal'],
            ['nomor_kode' => '4.01.02.02.01.0004', 'nomenklatur' => 'Koordinasi Bidang Penanaman Modal'],

            // ===== Program 4.01.03 — PROGRAM PENGEMBANGAN KOPERASI DAN USAHA MIKRO KECIL MENENGAH =====
            // --- Kegiatan 4.01.03.02.01 — Pengembangan Kelembagaan Koperasi ---
            ['nomor_kode' => '4.01.03.02.01.0001', 'nomenklatur' => 'Pembinaan Koperasi Simpan Pinjam/Unit Simpan Pinjam'],
            ['nomor_kode' => '4.01.03.02.01.0002', 'nomenklatur' => 'Pembinaan Koperasi Produksi'],
            ['nomor_kode' => '4.01.03.02.01.0003', 'nomenklatur' => 'Pembinaan Koperasi Konsumsi'],
            ['nomor_kode' => '4.01.03.02.01.0004', 'nomenklatur' => 'Pembinaan Koperasi Serba Usaha'],
            
            // --- Kegiatan 4.01.03.02.02 — Pengembangan Usaha Mikro Kecil Menengah ---
            ['nomor_kode' => '4.01.03.02.02.0001', 'nomenklatur' => 'Fasilitasi Pengembangan Usaha Mikro'],
            ['nomor_kode' => '4.01.03.02.02.0002', 'nomenklatur' => 'Fasilitasi Pengembangan Usaha Kecil'],
            ['nomor_kode' => '4.01.03.02.02.0003', 'nomenklatur' => 'Fasilitasi Pengembangan Usaha Menengah'],
            ['nomor_kode' => '4.01.03.02.02.0004', 'nomenklatur' => 'Pelatihan dan Pendampingan UMKM'],

            // ===== Program 4.01.04 — PROGRAM PENGEMBANGAN PERDAGANGAN =====
            // --- Kegiatan 4.01.04.02.01 — Stabilisasi Harga Barang Kebutuhan Pokok dan Barang Penting ---
            ['nomor_kode' => '4.01.04.02.01.0001', 'nomenklatur' => 'Monitoring Harga dan Distribusi Barang Kebutuhan Pokok'],
            ['nomor_kode' => '4.01.04.02.01.0002', 'nomenklatur' => 'Operasi Pasar dalam rangka Stabilisasi Harga'],
            ['nomor_kode' => '4.01.04.02.01.0003', 'nomenklatur' => 'Pembinaan Sarana Distribusi Perdagangan'],
            
            // --- Kegiatan 4.01.04.02.02 — Pengembangan Ekspor ---
            ['nomor_kode' => '4.01.04.02.02.0001', 'nomenklatur' => 'Promosi Dagang Luar Negeri'],
            ['nomor_kode' => '4.01.04.02.02.0002', 'nomenklatur' => 'Fasilitasi Ekspor'],
            ['nomor_kode' => '4.01.04.02.02.0003', 'nomenklatur' => 'Pengembangan Produk Ekspor'],

            // ===== Program 4.01.05 — PROGRAM PENGEMBANGAN INDUSTRI =====
            // --- Kegiatan 4.01.05.02.01 — Pembinaan dan Pengembangan Industri ---
            ['nomor_kode' => '4.01.05.02.01.0001', 'nomenklatur' => 'Pembinaan Industri Kecil dan Menengah'],
            ['nomor_kode' => '4.01.05.02.01.0002', 'nomenklatur' => 'Pengembangan Sentra Industri'],
            ['nomor_kode' => '4.01.05.02.01.0003', 'nomenklatur' => 'Fasilitasi Peningkatan Kapasitas Industri'],
            ['nomor_kode' => '4.01.05.02.01.0004', 'nomenklatur' => 'Pembinaan Standardisasi dan Sertifikasi Industri'],

            // ===== Program 4.02.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 4.02.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '4.02.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '4.02.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '4.02.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 4.02.02 — PROGRAM PENGEMBANGAN PARIWISATA =====
            // --- Kegiatan 4.02.02.02.01 — Pengembangan Destinasi Pariwisata ---
            ['nomor_kode' => '4.02.02.02.01.0001', 'nomenklatur' => 'Pengembangan Daya Tarik Wisata'],
            ['nomor_kode' => '4.02.02.02.01.0002', 'nomenklatur' => 'Pengembangan Aksesibilitas Pariwisata'],
            ['nomor_kode' => '4.02.02.02.01.0003', 'nomenklatur' => 'Pengembangan Amenitas Pariwisata'],
            ['nomor_kode' => '4.02.02.02.01.0004', 'nomenklatur' => 'Pengembangan Kelembagaan Pariwisata'],
            
            // --- Kegiatan 4.02.02.02.02 — Pengembangan Pemasaran Pariwisata ---
            ['nomor_kode' => '4.02.02.02.02.0001', 'nomenklatur' => 'Promosi Pariwisata'],
            ['nomor_kode' => '4.02.02.02.02.0002', 'nomenklatur' => 'Pengembangan Informasi Pariwisata'],
            ['nomor_kode' => '4.02.02.02.02.0003', 'nomenklatur' => 'Pengembangan Kemitraan Pemasaran Pariwisata'],
            
            // --- Kegiatan 4.02.02.02.03 — Pengembangan Industri Pariwisata ---
            ['nomor_kode' => '4.02.02.02.03.0001', 'nomenklatur' => 'Pembinaan Usaha Pariwisata'],
            ['nomor_kode' => '4.02.02.02.03.0002', 'nomenklatur' => 'Pengembangan Sumber Daya Manusia Pariwisata'],
            ['nomor_kode' => '4.02.02.02.03.0003', 'nomenklatur' => 'Standardisasi Usaha dan Profesi Pariwisata'],

            // ===== Program 4.02.03 — PROGRAM PENGEMBANGAN EKONOMI KREATIF =====
            // --- Kegiatan 4.02.03.02.01 — Pengembangan Ekonomi Kreatif ---
            ['nomor_kode' => '4.02.03.02.01.0001', 'nomenklatur' => 'Pengembangan Subsektor Ekonomi Kreatif'],
            ['nomor_kode' => '4.02.03.02.01.0002', 'nomenklatur' => 'Fasilitasi Pelaku Ekonomi Kreatif'],
            ['nomor_kode' => '4.02.03.02.01.0003', 'nomenklatur' => 'Pengembangan Ekosistem Ekonomi Kreatif'],
            ['nomor_kode' => '4.02.03.02.01.0004', 'nomenklatur' => 'Promosi dan Pemasaran Produk Ekonomi Kreatif'],



            // ===== Program 5.01.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 5.01.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.01.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.01.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],
            
            // --- Kegiatan 5.01.01.02.02 — Administrasi Keuangan Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.02.0001', 'nomenklatur' => 'Penyediaan Gaji dan Tunjangan ASN'],
            ['nomor_kode' => '5.01.01.02.02.0003', 'nomenklatur' => 'Pelaksanaan Penatausahaan dan Pengujian/Verifikasi Keuangan SKPD'],
            ['nomor_kode' => '5.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '5.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            
            // --- Kegiatan 5.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '5.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '5.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            
            // --- Kegiatan 5.01.01.02.06 — Administrasi Umum Perangkat Daerah ---
            ['nomor_kode' => '5.01.01.02.06.0001', 'nomenklatur' => 'Penyediaan Komponen Instalasi Listrik/Penerangan Bangunan Kantor'],
            ['nomor_kode' => '5.01.01.02.06.0004', 'nomenklatur' => 'Penyediaan Bahan Logistik Kantor'],
            ['nomor_kode' => '5.01.01.02.06.0005', 'nomenklatur' => 'Penyediaan Barang Cetakan dan Penggandaan'],
            ['nomor_kode' => '5.01.01.02.06.0006', 'nomenklatur' => 'Penyediaan Bahan Bacaan dan Peraturan Perundang-undangan'],
            ['nomor_kode' => '5.01.01.02.06.0007', 'nomenklatur' => 'Penyediaan Bahan/Material'],
            ['nomor_kode' => '5.01.01.02.06.0008', 'nomenklatur' => 'Fasilitasi Kunjungan Tamu'],
            ['nomor_kode' => '5.01.01.02.06.0009', 'nomenklatur' => 'Penyelenggaraan Rapat Koordinasi dan Konsultasi SKPD'],
            
            // --- Kegiatan 5.01.01.02.07 — Pengadaan Barang Milik Daerah Penunjang Urusan Pemerintah Daerah ---
            ['nomor_kode' => '5.01.01.02.07.0001', 'nomenklatur' => 'Pengadaan Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '5.01.01.02.07.0005', 'nomenklatur' => 'Pengadaan Mebel'],
            ['nomor_kode' => '5.01.01.02.07.0006', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin Lainnya'],
            
            // --- Kegiatan 5.01.01.02.08 — Penyediaan Jasa Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '5.01.01.02.08.0001', 'nomenklatur' => 'Penyediaan Jasa Surat Menyurat'],
            ['nomor_kode' => '5.01.01.02.08.0002', 'nomenklatur' => 'Penyediaan Jasa Komunikasi, Sumber Daya Air dan Listrik'],
            ['nomor_kode' => '5.01.01.02.08.0004', 'nomenklatur' => 'Penyediaan Jasa Pelayanan Umum Kantor'],
            

            // --- Kegiatan 5.01.01.02.09 — Pemeliharaan Barang Milik Daerah Penunjang Urusan Pemerintahan Daerah ---
            ['nomor_kode' => '5.01.01.02.09.0001', 'nomenklatur' => 'Penyediaan Jasa Pemeliharaan, Biaya Pemeliharaan, dan Pajak Kendaraan Perorangan Dinas atau Kendaraan Dinas Jabatan'],
            ['nomor_kode' => '5.01.01.02.09.0005', 'nomenklatur' => 'Pemeliharaan Mebel'],
            ['nomor_kode' => '5.01.01.02.09.0006', 'nomenklatur' => 'Pemeliharaan Peralatan dan Mesin Lainnya'],
            ['nomor_kode' => '5.01.01.02.09.0009', 'nomenklatur' => 'Pemeliharaan/Rehabilitasi Gedung Kantor dan Bangunan Lainnya'],

            // ===== Program 5.01.02 — PROGRAM PERENCANAAN, PENGENDALIAN DAN EVALUASI PEMBANGUNAN DAERAH =====
            // --- Kegiatan 5.01.02.02.01 — Perencanaan Pembangunan Daerah ---
            ['nomor_kode' => '5.01.02.02.01.0001', 'nomenklatur' => 'Koordinasi Penyusunan RPJPD'],
            ['nomor_kode' => '5.01.02.02.01.0002', 'nomenklatur' => 'Koordinasi Penyusunan RPJMD'],
            ['nomor_kode' => '5.01.02.02.01.0003', 'nomenklatur' => 'Koordinasi Penyusunan RKPD'],
            ['nomor_kode' => '5.01.02.02.01.0004', 'nomenklatur' => 'Koordinasi Penyusunan Rencana Kerja SKPD'],
            ['nomor_kode' => '5.01.02.02.01.0005', 'nomenklatur' => 'Penyusunan Rencana Tata Ruang Wilayah'],
            
            // --- Kegiatan 5.01.02.02.02 — Pengendalian Pelaksanaan Rencana Pembangunan Daerah ---
            ['nomor_kode' => '5.01.02.02.02.0001', 'nomenklatur' => 'Pengendalian dan Evaluasi Pelaksanaan RPJPD'],
            ['nomor_kode' => '5.01.02.02.02.0002', 'nomenklatur' => 'Pengendalian dan Evaluasi Pelaksanaan RPJMD'],
            ['nomor_kode' => '5.01.02.02.02.0003', 'nomenklatur' => 'Pengendalian dan Evaluasi Pelaksanaan RKPD'],
            
            // --- Kegiatan 5.01.02.02.03 — Evaluasi dan Pelaporan Pembangunan Daerah ---
            ['nomor_kode' => '5.01.02.02.03.0001', 'nomenklatur' => 'Monitoring dan Evaluasi Pembangunan Daerah'],
            ['nomor_kode' => '5.01.02.02.03.0002', 'nomenklatur' => 'Penyusunan Laporan Penyelenggaraan Pemerintahan Daerah'],
            ['nomor_kode' => '5.01.02.02.03.0003', 'nomenklatur' => 'Evaluasi Kinerja Penyelenggaraan Pemerintahan Daerah'],

            // ===== Program 5.01.03 — PROGRAM KOORDINASI DAN SINKRONISASI PERENCANAAN PEMBANGUNAN DAERAH =====
            // --- Kegiatan 5.01.03.02.01 — Koordinasi Perencanaan Pembangunan ---
            ['nomor_kode' => '5.01.03.02.01.0001', 'nomenklatur' => 'Koordinasi Perencanaan Pembangunan Antar SKPD'],
            ['nomor_kode' => '5.01.03.02.01.0002', 'nomenklatur' => 'Sinkronisasi Perencanaan Pembangunan Daerah'],
            ['nomor_kode' => '5.01.03.02.01.0003', 'nomenklatur' => 'Fasilitasi Perencanaan Partisipatif'],

            // ===== Program 5.02.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 5.02.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '5.02.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.02.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.02.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 5.02.02 — PROGRAM PENGELOLAAN KEUANGAN DAERAH =====
            // --- Kegiatan 5.02.02.02.01 — Pengelolaan Anggaran Daerah ---
            ['nomor_kode' => '5.02.02.02.01.0001', 'nomenklatur' => 'Penyusunan RAPBD'],
            ['nomor_kode' => '5.02.02.02.01.0002', 'nomenklatur' => 'Koordinasi Penyusunan Kebijakan Umum APBD'],
            ['nomor_kode' => '5.02.02.02.01.0003', 'nomenklatur' => 'Penyusunan Prioritas dan Plafon Anggaran Sementara'],
            
            // --- Kegiatan 5.02.02.02.02 — Pengelolaan Kas Daerah ---
            ['nomor_kode' => '5.02.02.02.02.0001', 'nomenklatur' => 'Pengelolaan Kas Umum Daerah'],
            ['nomor_kode' => '5.02.02.02.02.0002', 'nomenklatur' => 'Pengelolaan Investasi Jangka Pendek'],
            ['nomor_kode' => '5.02.02.02.02.0003', 'nomenklatur' => 'Pengendalian Pelaksanaan APBD'],

            // ===== Program 5.02.03 — PROGRAM PENGELOLAAN BARANG MILIK DAERAH =====
            // --- Kegiatan 5.02.03.02.01 — Perencanaan Kebutuhan dan Penganggaran Barang Milik Daerah ---
            ['nomor_kode' => '5.02.03.02.01.0001', 'nomenklatur' => 'Penyusunan Rencana Kebutuhan Barang Milik Daerah'],
            ['nomor_kode' => '5.02.03.02.01.0002', 'nomenklatur' => 'Penyusunan Rencana Kebutuhan Pemeliharaan Barang Milik Daerah'],
            
            // --- Kegiatan 5.02.03.02.02 — Pengadaan Barang Milik Daerah ---
            ['nomor_kode' => '5.02.03.02.02.0001', 'nomenklatur' => 'Pengadaan Tanah'],
            ['nomor_kode' => '5.02.03.02.02.0002', 'nomenklatur' => 'Pengadaan Peralatan dan Mesin'],
            ['nomor_kode' => '5.02.03.02.02.0003', 'nomenklatur' => 'Pengadaan Gedung dan Bangunan'],

            // ===== Program 5.02.04 — PROGRAM PENGELOLAAN PENDAPATAN DAERAH =====
            // --- Kegiatan 5.02.04.02.01 — Intensifikasi dan Ekstensifikasi Penerimaan Daerah ---
            ['nomor_kode' => '5.02.04.02.01.0001', 'nomenklatur' => 'Intensifikasi Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0002', 'nomenklatur' => 'Ekstensifikasi Pajak Daerah'],
            ['nomor_kode' => '5.02.04.02.01.0003', 'nomenklatur' => 'Optimalisasi Retribusi Daerah'],

            // ===== Program 5.03.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 5.03.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '5.03.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.03.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.03.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 5.03.02 — PROGRAM KEPEGAWAIAN DAERAH =====
            // --- Kegiatan 5.03.02.02.01 — Perencanaan, Pengadaan, dan Distribusi PNS ---
            ['nomor_kode' => '5.03.02.02.01.0001', 'nomenklatur' => 'Penyusunan Kebutuhan PNS'],
            ['nomor_kode' => '5.03.02.02.01.0002', 'nomenklatur' => 'Seleksi Calon PNS'],
            ['nomor_kode' => '5.03.02.02.01.0003', 'nomenklatur' => 'Pengangkatan dan Penempatan PNS'],
            
            // --- Kegiatan 5.03.02.02.02 — Mutasi PNS ---
            ['nomor_kode' => '5.03.02.02.02.0001', 'nomenklatur' => 'Promosi dan Mutasi PNS'],
            ['nomor_kode' => '5.03.02.02.02.0002', 'nomenklatur' => 'Rotasi dan Alih Tugas PNS'],
            ['nomor_kode' => '5.03.02.02.02.0003', 'nomenklatur' => 'Pemberhentian PNS'],

            // ===== Program 5.04.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 5.04.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '5.04.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.04.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.04.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.04.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.04.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '5.04.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.04.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 5.04.02 — PROGRAM PENGEMBANGAN SUMBER DAYA MANUSIA =====
            // --- Kegiatan 5.04.02.02.01 — Pendidikan dan Pelatihan Aparatur ---
            ['nomor_kode' => '5.04.02.02.01.0001', 'nomenklatur' => 'Diklat Prajabatan'],
            ['nomor_kode' => '5.04.02.02.01.0002', 'nomenklatur' => 'Diklat Dalam Jabatan'],
            ['nomor_kode' => '5.04.02.02.01.0003', 'nomenklatur' => 'Diklat Teknis Fungsional'],
            ['nomor_kode' => '5.04.02.02.01.0004', 'nomenklatur' => 'Diklat Kepemimpinan'],

            // ===== Program 5.05.01 — PROGRAM PENUNJANG URUSAN PEMERINTAHAN DAERAH KABUPATEN/KOTA =====
            // --- Kegiatan 5.05.01.02.01 — Perencanaan, Penganggaran, dan Evaluasi Kinerja Perangkat Daerah ---
            ['nomor_kode' => '5.05.01.02.01.0001', 'nomenklatur' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah'],
            ['nomor_kode' => '5.05.01.02.01.0002', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen RKA-SKPD'],
            ['nomor_kode' => '5.05.01.02.01.0003', 'nomenklatur' => 'Koordinasi dan Penyusunan Dokumen Perubahan RKA-SKPD'],
            ['nomor_kode' => '5.05.01.02.01.0004', 'nomenklatur' => 'Koordinasi dan Penyusunan DPA-SKPD'],
            ['nomor_kode' => '5.05.01.02.01.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Perubahan DPA- SKPD'],
            ['nomor_kode' => '5.05.01.02.01.0006', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Capaian Kinerja dan Ikhtisar Realisasi Kinerja SKPD'],
            ['nomor_kode' => '5.05.01.02.01.0007', 'nomenklatur' => 'Evaluasi Kinerja Perangkat Daerah'],

            // ===== Program 5.05.02 — PROGRAM PENELITIAN DAN PENGEMBANGAN DAERAH =====
            // --- Kegiatan 5.05.02.02.01 — Penelitian dan Pengembangan Pemerintahan Daerah ---
            ['nomor_kode' => '5.05.02.02.01.0001', 'nomenklatur' => 'Penelitian Bidang Pemerintahan'],
            ['nomor_kode' => '5.05.02.02.01.0002', 'nomenklatur' => 'Penelitian Bidang Kemasyarakatan'],
            ['nomor_kode' => '5.05.02.02.01.0003', 'nomenklatur' => 'Penelitian Bidang Ekonomi'],
            
            // --- Kegiatan 5.05.02.02.02 — Pengembangan Inovasi Daerah ---
            ['nomor_kode' => '5.05.02.02.02.0001', 'nomenklatur' => 'Pengembangan Inovasi Pelayanan Publik'],
            ['nomor_kode' => '5.05.02.02.02.0002', 'nomenklatur' => 'Pengembangan Inovasi Tata Kelola Pemerintahan'],
            ['nomor_kode' => '5.05.02.02.02.0003', 'nomenklatur' => 'Diseminasi Hasil Penelitian dan Pengembangan'],

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
            ['nomor_kode' => '6.01.02.02.01.0007', 'nomenklatur' => 'Monitoring dan Evaluasi Tindak Lanjut Hasil Pemeriksaan BPK RI dan Tindak Lanjut Hasil Pemeriksaan'],
            
            // --- Kegiatan 6.01.02.02.02 — Penyelenggaraan Pengawasan dengan Tujuan Tertentu ---
            ['nomor_kode' => '6.01.02.02.02.0002', 'nomenklatur' => 'Pengawasan dengan Tujuan Tertentu'],

            // ===== Program 6.01.03 — PROGRAM PERUMUSAN KEBIJAKAN, PENDAMPINGAN DAN ASISTENSI =====
            // --- Kegiatan 6.01.03.02.01 — Perumusan Kebijakan Teknis di Bidang Pengawasan dan Fasilitasi Pengawasan ---
            ['nomor_kode' => '6.01.03.02.01.0001', 'nomenklatur' => 'Perumusan Kebijakan Teknis di Bidang Pengawasan'],
            ['nomor_kode' => '6.01.03.02.01.0002', 'nomenklatur' => 'Perumusan Kebijakan Teknis di Bidang Fasilitasi Pengawasan'],
            
            // --- Kegiatan 6.01.03.02.02 — Pendampingan dan Asistensi ---
            ['nomor_kode' => '6.01.03.02.02.0002', 'nomenklatur' => 'Pendampingan, Asistensi, Verifikasi, dan Penilaian Reformasi Birokrasi'],
            ['nomor_kode' => '6.01.03.02.02.0003', 'nomenklatur' => 'Koordinasi, Monitoring dan Evaluasi serta Verifikasi Pencegahan dan Pemberantasan Korupsi'],

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
            ['nomor_kode' => '7.01.01.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
            
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

            // ===== Program 7.01.02 — PROGRAM PENYELENGGARAAN PEMERINTAHAN DAN PELAYANAN PUBLIK =====
            // --- Kegiatan 7.01.02.02.01 — Koordinasi Penyelenggaraan Kegiatan Pemerintahan di Tingkat Kecamatan ---
            ['nomor_kode' => '7.01.02.02.01.0001', 'nomenklatur' => 'Koordinasi Penyelenggaraan Kegiatan Pemerintahan di Tingkat Kecamatan'],
            ['nomor_kode' => '7.01.02.02.01.0002', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Penyelenggaraan Kegiatan Pemerintahan di Tingkat Kecamatan'],
            ['nomor_kode' => '7.01.02.02.01.0003', 'nomenklatur' => 'Pembinaan dan Pengawasan Penyelenggaraan Kegiatan Pemerintahan di Tingkat Kecamatan'],
            ['nomor_kode' => '7.01.02.02.01.0004', 'nomenklatur' => 'Evaluasi dan Pelaporan Penyelenggaraan Kegiatan Pemerintahan di Tingkat Kecamatan'],
            
            // --- Kegiatan 7.01.02.02.04 — Pelaksanaan Urusan Pemerintahan yang Dilimpahkan kepada Camat ---
            ['nomor_kode' => '7.01.02.02.04.0001', 'nomenklatur' => 'Pelaksanaan Urusan Pemerintahan yang Dilimpahkan kepada Camat'],
            ['nomor_kode' => '7.01.02.02.04.0002', 'nomenklatur' => 'Koordinasi Pelaksanaan Urusan Pemerintahan yang Dilimpahkan kepada Camat'],
            ['nomor_kode' => '7.01.02.02.04.0003', 'nomenklatur' => 'Pelaksanaan Urusan Pemerintahan yang Terkait dengan Kewenangan Lain yang Dilimpahkan'],

            // ===== Program 7.01.03 — PROGRAM PEMBERDAYAAN MASYARAKAT DESA DAN KELURAHAN =====
            // --- Kegiatan 7.01.03.02.02 — Kegiatan Pemberdayaan Kelurahan ---
            ['nomor_kode' => '7.01.03.02.02.0001', 'nomenklatur' => 'Kegiatan Pemberdayaan Kelurahan'],
            ['nomor_kode' => '7.01.03.02.02.0002', 'nomenklatur' => 'Pembangunan Sarana dan Prasarana Kelurahan'],
            ['nomor_kode' => '7.01.03.02.02.0003', 'nomenklatur' => 'Peningkatan Kapasitas Aparatur Kelurahan'],
            ['nomor_kode' => '7.01.03.02.02.0004', 'nomenklatur' => 'Koordinasi dan Sinkronisasi Pemberdayaan Kelurahan'],
            
            // --- Kegiatan 7.01.03.02.03 — Pemberdayaan Lembaga Kemasyarakatan Tingkat Kecamatan ---
            ['nomor_kode' => '7.01.03.02.03.0001', 'nomenklatur' => 'Penyelenggaraan Lembaga Kemasyarakatan'],
            ['nomor_kode' => '7.01.03.02.03.0002', 'nomenklatur' => 'Peningkatan Kapasitas Lembaga Kemasyarakatan'],
            ['nomor_kode' => '7.01.03.02.03.0003', 'nomenklatur' => 'Pembinaan dan Pengembangan Lembaga Kemasyarakatan'],
            
            // --- Kegiatan 7.01.03.02.06 — Pemberdayaan dan Kesejahteraan Keluarga Tingkat Kecamatan dan Kelurahan ---
            ['nomor_kode' => '7.01.03.02.06.0001', 'nomenklatur' => 'Pemberdayaan dan Kesejahteraan Keluarga'],
            ['nomor_kode' => '7.01.03.02.06.0002', 'nomenklatur' => 'Peningkatan Ketahanan Pangan Keluarga'],
            ['nomor_kode' => '7.01.03.02.06.0003', 'nomenklatur' => 'Pelatihan Keluarga Tanggap Bencana Alam'],
            ['nomor_kode' => '7.01.03.02.06.0004', 'nomenklatur' => 'Pembinaan Kelompok Usaha Ekonomi Produktif'],

            // ===== Program 7.01.04 — PROGRAM KOORDINASI KETENTRAMAN DAN KETERTIBAN UMUM =====
            // --- Kegiatan 7.01.04.02.01 — Koordinasi Upaya Penyelenggaraan Ketenteraman dan Ketertiban Umum ---
            ['nomor_kode' => '7.01.04.02.01.0001', 'nomenklatur' => 'Koordinasi Upaya Penyelenggaraan Ketenteraman dan Ketertiban Umum'],
            ['nomor_kode' => '7.01.04.02.01.0002', 'nomenklatur' => 'Pembinaan dan Pengawasan Ketenteraman dan Ketertiban Umum'],
            ['nomor_kode' => '7.01.04.02.01.0003', 'nomenklatur' => 'Evaluasi dan Pelaporan Ketenteraman dan Ketertiban Umum'],
            
            // --- Kegiatan 7.01.04.02.02 — Koordinasi Penerapan dan Penegakan Peraturan Daerah dan Peraturan Kepala Daerah ---
            ['nomor_kode' => '7.01.04.02.02.0001', 'nomenklatur' => 'Koordinasi Penerapan dan Penegakan Peraturan Daerah dan Peraturan Kepala Daerah'],
            ['nomor_kode' => '7.01.04.02.02.0002', 'nomenklatur' => 'Pembinaan dan Pengawasan Penegakan Peraturan Daerah'],
            ['nomor_kode' => '7.01.04.02.02.0003', 'nomenklatur' => 'Sosialisasi Peraturan Daerah dan Peraturan Kepala Daerah'],

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
            ['nomor_kode' => '8.01.01.02.02.0004', 'nomenklatur' => 'Koordinasi dan Pelaksanaan Akuntansi SKPD'],
            ['nomor_kode' => '8.01.01.02.02.0005', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD'],
            ['nomor_kode' => '8.01.01.02.02.0007', 'nomenklatur' => 'Koordinasi dan Penyusunan Laporan Keuangan Bulanan/ Triwulanan/ Semesteran SKPD'],
            
            // --- Kegiatan 8.01.01.02.03 — Administrasi Barang Milik Daerah pada Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.03.0001', 'nomenklatur' => 'Penyusunan Perencanaan Kebutuhan Barang Milik Daerah SKPD'],
            ['nomor_kode' => '8.01.01.02.03.0005', 'nomenklatur' => 'Rekonsiliasi dan Penyusunan Laporan Barang Milik Daerah pada SKPD'],
            ['nomor_kode' => '8.01.01.02.03.0006', 'nomenklatur' => 'Penatausahaan Barang Milik Daerah pada SKPD'],
            
            // --- Kegiatan 8.01.01.02.05 — Administrasi Kepegawaian Perangkat Daerah ---
            ['nomor_kode' => '8.01.01.02.05.0003', 'nomenklatur' => 'Pendataan dan Pengolahan Administrasi Kepegawaian'],
            ['nomor_kode' => '8.01.01.02.05.0005', 'nomenklatur' => 'Monitoring, Evaluasi, dan Penilaian Kinerja Pegawai'],
            ['nomor_kode' => '8.01.01.02.05.0009', 'nomenklatur' => 'Pendidikan dan Pelatihan Pegawai Berdasarkan Tugas dan Fungsi'],
            ['nomor_kode' => '8.01.01.02.05.0011', 'nomenklatur' => 'Bimbingan Teknis Implementasi Peraturan Perundang-Undangan'],
            
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

            // ===== Program 8.01.02 — PROGRAM PENGUATAN IDEOLOGI PANCASILA DAN KARAKTER KEBANGSAAN =====
            // --- Kegiatan 8.01.02.02.01 — Perumusan Kebijakan Teknis dan Pemantapan Pelaksanaan Bidang Ideologi Pancasila dan Karakter Kebangsaan ---
            ['nomor_kode' => '8.01.02.02.01.0001', 'nomenklatur' => 'Penyusunan Program Kerja di Bidang Ideologi Wawasan Kebangsaan, Bela Negara, Karakter Bangsa'],
            ['nomor_kode' => '8.01.02.02.01.0002', 'nomenklatur' => 'Penyusunan Bahan Rumusan Kebijakan di Bidang Ideologi Pancasila dan Karakter Bangsa'],
            ['nomor_kode' => '8.01.02.02.01.0003', 'nomenklatur' => 'Pelaksanaan Kebijakan di Bidang Ideologi Wawasan Kebangsaan, Bela Negara, Karakter Bangsa'],
            ['nomor_kode' => '8.01.02.02.01.0004', 'nomenklatur' => 'Pelaksanaan Koordinasi di Bidang Ideologi Wawasan Kebangsaan, Bela Negara, Karakter Bangsa'],
            ['nomor_kode' => '8.01.02.02.01.0005', 'nomenklatur' => 'Pelaksanaan Monitoring, Evaluasi dan Pelaporan di Bidang Ideologi Wawasan Kebangsaan, Bela Negara, Karakter Bangsa'],
            ['nomor_kode' => '8.01.02.02.01.0008', 'nomenklatur' => 'Pembentukan Paskibraka'],
            ['nomor_kode' => '8.01.02.02.01.0009', 'nomenklatur' => 'Pembinaan Lanjutan kepada Purnapaskibraka Duta Pancasila'],
            ['nomor_kode' => '8.01.02.02.01.0010', 'nomenklatur' => 'Pelaksanaan tugas Purnapaskibraka Duta Pancasila'],

            // ===== Program 8.01.03 — PROGRAM PENINGKATAN PERAN PARTAI POLITIK DAN LEMBAGA PENDIDIKAN =====
            // --- Kegiatan 8.01.03.02.01 — Perumusan Kebijakan Teknis dan Pemantapan Pelaksanaan Bidang Pendidikan Politik ---
            ['nomor_kode' => '8.01.03.02.01.0001', 'nomenklatur' => 'Penyusunan Program Kerja di Bidang Pendidikan Politik, Etika Budaya Politik, Peningkatan Demokrasi'],
            ['nomor_kode' => '8.01.03.02.01.0002', 'nomenklatur' => 'Penyusunan Bahan Rumusan Kebijakan di Bidang Pendidikan Politik, Etika Budaya Politik, Peningkatan Demokrasi'],
            ['nomor_kode' => '8.01.03.02.01.0003', 'nomenklatur' => 'Pelaksanaan Kebijakan di Bidang Pendidikan Politik, Etika Budaya Politik, Peningkatan Demokrasi'],
            ['nomor_kode' => '8.01.03.02.01.0004', 'nomenklatur' => 'Pelaksanaan Koordinasi di Bidang Pendidikan Politik, Etika Budaya Politik, Peningkatan Demokrasi'],
            ['nomor_kode' => '8.01.03.02.01.0005', 'nomenklatur' => 'Pelaksanaan Monitoring, Evaluasi dan Pelaporan di Bidang Pendidikan Politik, Etika Budaya Politik, Peningkatan Demokrasi'],

            // ===== Program 8.01.04 — PROGRAM PEMBERDAYAAN DAN PENGAWASAN ORGANISASI KEMASYARAKATAN =====
            // --- Kegiatan 8.01.04.02.01 — Perumusan Kebijakan Teknis dan Pemantapan Pelaksanaan Bidang Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan ---
            ['nomor_kode' => '8.01.04.02.01.0001', 'nomenklatur' => 'Penyusunan Program Kerja di Bidang Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan'],
            ['nomor_kode' => '8.01.04.02.01.0002', 'nomenklatur' => 'Penyusunan Bahan Rumusan Kebijakan di Bidang Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan'],
            ['nomor_kode' => '8.01.04.02.01.0003', 'nomenklatur' => 'Pelaksanaan Kebijakan di Bidang Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan'],
            ['nomor_kode' => '8.01.04.02.01.0004', 'nomenklatur' => 'Pelaksanaan Koordinasi di Bidang Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan'],
            ['nomor_kode' => '8.01.04.02.01.0005', 'nomenklatur' => 'Pelaksanaan Monitoring, Evaluasi dan Pelaporan di Bidang Pemberdayaan dan Pengawasan Organisasi Kemasyarakatan'],

            // ===== Program 8.01.05 — PROGRAM PEMBINAAN DAN PENGEMBANGAN KETAHANAN EKONOMI, SOSIAL, DAN BUDAYA =====
            // --- Kegiatan 8.01.05.02.01 — Perumusan Kebijakan Teknis dan Pemantapan Pelaksanaan Bidang Ketahanan Ekonomi, Sosial dan Budaya ---
            ['nomor_kode' => '8.01.05.02.01.0001', 'nomenklatur' => 'Penyusunan Program Kerja di Bidang Ketahanan Ekonomi, Sosial, Budaya dan Fasilitasi Pencegahan Penyalahgunaan Narkoba'],
            ['nomor_kode' => '8.01.05.02.01.0002', 'nomenklatur' => 'Penyusunan Bahan Rumusan Kebijakan di Bidang Ketahanan Ekonomi, Sosial, Budaya dan Fasilitasi Pencegahan Penyalahgunaan Narkoba'],
            ['nomor_kode' => '8.01.05.02.01.0003', 'nomenklatur' => 'Pelaksanaan Kebijakan di Bidang Ketahanan Ekonomi, Sosial, Budaya dan Fasilitasi Pencegahan Penyalahgunaan Narkoba'],
            ['nomor_kode' => '8.01.05.02.01.0004', 'nomenklatur' => 'Pelaksanaan Koordinasi di Bidang Ketahanan Ekonomi, Sosial, Budaya dan Fasilitasi Pencegahan Penyalahgunaan Narkoba'],
            ['nomor_kode' => '8.01.05.02.01.0005', 'nomenklatur' => 'Pelaksanaan Monitoring, Evaluasi dan Pelaporan di Bidang Ketahanan Ekonomi, Sosial, Budaya dan Fasilitasi Pencegahan Penyalahgunaan Narkoba'],

            // ===== Program 8.01.06 — PROGRAM PENINGKATAN KEWASPADAAN NASIONAL DAN PENINGKATAN KUALITAS DAN FASILITASI PENANGANAN KONFLIK SOSIAL =====
            // --- Kegiatan 8.01.06.02.01 — Perumusan Kebijakan Teknis dan Pelaksanaan Pemantapan Kewaspadaan Nasional dan Penanganan Konflik Sosial ---
            ['nomor_kode' => '8.01.06.02.01.0001', 'nomenklatur' => 'Penyusunan Program Kerja di Bidang Kewaspadaan Nasional dan Penanganan Konflik Sosial'],
            ['nomor_kode' => '8.01.06.02.01.0002', 'nomenklatur' => 'Penyusunan Bahan Rumusan Kebijakan di Bidang Kewaspadaan Nasional dan Penanganan Konflik Sosial'],
            ['nomor_kode' => '8.01.06.02.01.0003', 'nomenklatur' => 'Pelaksanaan Kebijakan di Bidang Kewaspadaan Nasional dan Penanganan Konflik Sosial'],
            ['nomor_kode' => '8.01.06.02.01.0004', 'nomenklatur' => 'Pelaksanaan Koordinasi di Bidang Kewaspadaan Nasional dan Penanganan Konflik Sosial'],
            ['nomor_kode' => '8.01.06.02.01.0005', 'nomenklatur' => 'Pelaksanaan Monitoring, Evaluasi dan Pelaporan di Bidang Kewaspadaan Nasional dan Penanganan Konflik Sosial'],
            ['nomor_kode' => '8.01.06.02.01.0006', 'nomenklatur' => 'Pelaksanaan Forum Koordinasi Pimpinan Daerah Kabupaten/Kota'],
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