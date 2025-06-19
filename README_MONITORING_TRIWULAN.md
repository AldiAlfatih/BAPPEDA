# Fitur Monitoring Triwulan dan Akumulasi Kinerja Tahunan

## Deskripsi
Sistem ini memungkinkan penyimpanan data monitoring triwulan dan secara otomatis menghitung akumulasi persentase kinerja tahunan berdasarkan data dari semua triwulan yang telah tersimpan.

## Fitur Utama

### 1. Simpan Data Monitoring Triwulan 1
- **Endpoint**: `POST /triwulan/{tid}/save-realisasi`
- **Parameter**: 
  - `tid`: ID triwulan (1, 2, 3, atau 4)
  - Form data: realisasi_fisik, realisasi_keuangan, keterangan, nama_pptk

#### Proses Penyimpanan:
1. Validasi periode triwulan (harus dalam status aktif)
2. Validasi data input (fisik dan keuangan wajib diisi)
3. Mencari/membuat record monitoring untuk realisasi triwulan
4. Menyalin data pagu dari "Rencana Awal" jika tersedia
5. Menyimpan data realisasi ke tabel `monitoring_realisasi`
6. **Otomatis menghitung akumulasi kinerja tahunan**

### 2. Akumulasi Kinerja Tahunan
Sistem secara otomatis menghitung:
- **Total Kinerja Fisik**: Akumulasi dari semua triwulan yang tersimpan
- **Total Keuangan**: Akumulasi dari semua triwulan yang tersimpan
- **Detail per Triwulan**: Breakdown data setiap triwulan

#### Algoritma Perhitungan:
```php
// Loop untuk semua triwulan (1-4)
$totalFisik = 0;
$totalKeuangan = 0;

for ($tid = 1; $tid <= 4; $tid++) {
    $realisasi = getRealisasiTriwulan($skpdTugasId, $tid, $tahun);
    if ($realisasi) {
        $totalFisik += $realisasi->kinerja_fisik;
        $totalKeuangan += $realisasi->keuangan;
    }
}

return [
    'akumulasi_fisik' => $totalFisik,
    'akumulasi_keuangan' => $totalKeuangan,
    'detail_triwulan' => $detailPerTriwulan
];
```

### 3. API Akumulasi Kinerja
- **Endpoint**: `GET /triwulan/akumulasi-kinerja/{skpdTugasId}/{tahun?}`
- **Response**:
```json
{
    "success": true,
    "data": {
        "akumulasi_fisik": 75.50,
        "akumulasi_keuangan": 1500000000,
        "jumlah_triwulan_tersimpan": 2,
        "detail_triwulan": {
            "1": {
                "triwulan": 1,
                "nama_triwulan": "Triwulan 1",
                "kinerja_fisik": 25.00,
                "keuangan": 500000000,
                "periode_id": 2,
                "realisasi_id": 123
            },
            "2": {
                "triwulan": 2,
                "nama_triwulan": "Triwulan 2", 
                "kinerja_fisik": 50.50,
                "keuangan": 1000000000,
                "periode_id": 3,
                "realisasi_id": 124
            }
        }
    }
}
```

## Cara Penggunaan Frontend

### 1. Mengisi Data Monitoring
1. Buka halaman Detail Triwulan 1
2. Isi kolom Realisasi Fisik (%), Realisasi Keuangan (Rp)
3. Tambahkan keterangan dan nama PPTK (opsional)
4. Klik tombol "Simpan"

### 2. Melihat Akumulasi Tahunan
1. Klik tombol "📊 Akumulasi Tahunan" di header tabel
2. Sistem akan menampilkan popup dengan:
   - Total akumulasi fisik dan keuangan
   - Jumlah triwulan yang sudah tersimpan
   - Detail breakdown per triwulan

### 3. Response Simpan Data
Setelah menyimpan, sistem akan menampilkan pesan sukses dengan informasi:
```
Data Triwulan 1 berhasil disimpan!

Akumulasi Kinerja Tahunan:
- Fisik: 25.00%
- Keuangan: 500,000,000
- Triwulan tersimpan: 1
```

## Struktur Database

### Tabel `monitoring_realisasi`
- `id`: Primary key
- `monitoring_anggaran_id`: Foreign key ke monitoring_anggaran
- `periode_id`: Foreign key ke periode triwulan
- `kinerja_fisik`: Persentase kinerja fisik
- `keuangan`: Nilai keuangan realisasi
- `deskripsi`: Keterangan tambahan
- `nama_pptk`: Nama PPTK

### Relasi Data
```
SkpdTugas -> Monitoring -> MonitoringAnggaran -> MonitoringRealisasi
                      \-> MonitoringPagu
```

## Logging dan Debug
Sistem mencatat log untuk:
- Proses penyimpanan data realisasi
- Perhitungan akumulasi kinerja
- Error handling
- Detail data per triwulan

Log dapat dilihat di `storage/logs/laravel.log` dengan prefix `TriwulanController`.

## Error Handling
- Validasi periode triwulan harus aktif
- Validasi data input wajib
- Handling jika tidak ada data Rencana Awal
- Fallback untuk data yang tidak lengkap

## Benefit
1. **Otomatis**: Akumulasi dihitung otomatis setiap kali simpan data
2. **Real-time**: Data akumulasi selalu up-to-date
3. **Transparan**: User dapat melihat breakdown per triwulan
4. **Audit**: Log lengkap untuk tracking perubahan data
5. **Konsisten**: Menggunakan data pagu dari Rencana Awal sebagai baseline 