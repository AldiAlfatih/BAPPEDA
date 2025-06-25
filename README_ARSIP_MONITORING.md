# Arsip Monitoring - Dokumentasi Implementasi

## Ringkasan Perubahan
Sistem "Laporan Akhir" telah diubah menjadi "Arsip Monitoring" dengan fitur baru untuk mengelola dokumen monitoring dari rencana awal hingga triwulan 4.

## Fitur Utama
1. **Upload File**: Kemampuan untuk mengunggah file PDF, DOC, DOCX, XLS, XLSX (maksimal 10MB)
2. **Detail/View**: Melihat file yang telah diunggah (PDF dapat dibuka di browser)
3. **Download**: Mengunduh file arsip
4. **Delete**: Menghapus file arsip
5. **Replace**: Mengganti file yang sudah ada

## Struktur Database Baru

### Tabel `arsip_monitoring`
- `id`: Primary key
- `skpd_tugas_id`: Foreign key ke tabel skpd_tugas
- `periode`: Enum (rencana_awal, triwulan_1, triwulan_2, triwulan_3, triwulan_4)
- `tahun`: Tahun arsip
- `nama_file`: Nama file asli
- `path_file`: Path file di storage
- `ukuran_file`: Ukuran file dalam bytes
- `tipe_file`: Ekstensi file
- `tanggal_upload`: Waktu upload
- `uploaded_by`: Foreign key ke users
- `keterangan`: Keterangan opsional

## File yang Diubah/Ditambah

### Backend (Laravel)
1. **Migration**: `2025_01_15_000000_create_arsip_monitoring_table.php`
2. **Model**: `app/Models/ArsipMonitoring.php`
3. **Controller**: `app/Http/Controllers/LaporanAkhirController.php` (diubah untuk arsip monitoring)
4. **Routes**: `routes/web.php` (menambah prefix arsip-monitoring)
5. **Seeder**: `database/seeders/SumberAnggaranSeeder.php`

### Frontend (Vue.js)
1. **Index**: `resources/js/pages/LaporanAkhir/Index.vue` (judul dan route diubah)
2. **Show**: `resources/js/pages/LaporanAkhir/Show.vue` (judul dan route diubah)
3. **Detail**: `resources/js/pages/LaporanAkhir/Detail.vue` (sepenuhnya dibuat ulang)
4. **Sidebar**: `resources/js/components/AppSidebar.vue` (route menu diubah)

## Route Baru
- `GET /arsip-monitoring` - Index halaman arsip monitoring
- `GET /arsip-monitoring/{id}` - Show detail SKPD
- `GET /arsip-monitoring/detail/{tugasId}` - Detail arsip per tugas
- `POST /arsip-monitoring/upload` - Upload file arsip
- `GET /arsip-monitoring/download/{id}` - Download file
- `GET /arsip-monitoring/view/{id}` - View file di browser
- `DELETE /arsip-monitoring/delete/{id}` - Hapus file

## Struktur Periode
1. **Rencana Awal** - Dokumen perencanaan awal
2. **Triwulan 1** - Dokumen monitoring triwulan 1
3. **Triwulan 2** - Dokumen monitoring triwulan 2
4. **Triwulan 3** - Dokumen monitoring triwulan 3
5. **Triwulan 4** - Dokumen monitoring triwulan 4

## Hak Akses
- **Upload**: Semua user yang dapat mengakses SKPD terkait
- **View/Download**: Semua user yang dapat mengakses SKPD terkait
- **Delete**: Semua user yang dapat mengakses SKPD terkait

## Validasi File
- **Tipe File**: PDF, DOC, DOCX, XLS, XLSX
- **Ukuran Maksimal**: 10MB
- **Storage**: File disimpan di `storage/app/public/arsip_monitoring/{tahun}/{skpd_tugas_id}/`

## Cara Penggunaan
1. Akses menu "Arsip Monitoring" di sidebar
2. Pilih SKPD yang diinginkan
3. Pilih tugas yang akan dikelola arsipnya
4. Untuk setiap periode, klik tombol "Unggah" untuk upload file baru
5. Setelah file terupload, tombol "Detail" akan muncul untuk melihat file
6. Gunakan tombol "Download" untuk mengunduh file
7. Gunakan tombol "Hapus" untuk menghapus file jika diperlukan

## Technical Notes
- File disimpan menggunakan Laravel Storage di disk 'public'
- Symbolic link dari storage ke public sudah dibuat
- Migration sudah dijalankan dan tabel sudah tersedia
- Route cache telah dibersihkan
- Seeder sumber anggaran telah dijalankan

## Status
✅ **SELESAI DIIMPLEMENTASI** - Sistem arsip monitoring sudah siap digunakan. 