<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SCRIPT AKTIVASI PERIODE PARSIAL ===" . PHP_EOL;
echo "Script ini akan mengaktifkan periode Triwulan 2 dan 3 untuk mode parsial" . PHP_EOL;
echo "=========================================" . PHP_EOL;

// Get tahun aktif
$tahunAktif = App\Models\PeriodeTahun::getTahunAktif();
if (!$tahunAktif) {
    echo "❌ Tidak ada tahun aktif!" . PHP_EOL;
    exit(1);
}

echo "✅ Tahun aktif: " . $tahunAktif->tahun . PHP_EOL;

// Get periode Triwulan 2 dan 3
$triwulan2 = App\Models\Periode::with(['tahap', 'tahun'])
    ->whereHas('tahap', function($query) {
        $query->where('tahap', 'Triwulan 2');
    })
    ->where('tahun_id', $tahunAktif->id)
    ->first();

$triwulan3 = App\Models\Periode::with(['tahap', 'tahun'])
    ->whereHas('tahap', function($query) {
        $query->where('tahap', 'Triwulan 3');
    })
    ->where('tahun_id', $tahunAktif->id)
    ->first();

echo PHP_EOL . "=== STATUS PERIODE SAAT INI ===" . PHP_EOL;

if ($triwulan2) {
    echo "Triwulan 2: ID {$triwulan2->id}, Status {$triwulan2->status}, Selesai: {$triwulan2->tanggal_selesai}" . PHP_EOL;
} else {
    echo "❌ Triwulan 2 tidak ditemukan!" . PHP_EOL;
}

if ($triwulan3) {
    echo "Triwulan 3: ID {$triwulan3->id}, Status {$triwulan3->status}, Selesai: {$triwulan3->tanggal_selesai}" . PHP_EOL;
} else {
    echo "❌ Triwulan 3 tidak ditemukan!" . PHP_EOL;
}

echo PHP_EOL . "=== PILIHAN AKTIVASI ===" . PHP_EOL;
echo "1. Aktifkan Triwulan 2 (dan nonaktifkan yang lain)" . PHP_EOL;
echo "2. Aktifkan Triwulan 3 (dan nonaktifkan yang lain)" . PHP_EOL;
echo "3. Biarkan Triwulan 1 tetap aktif" . PHP_EOL;
echo "4. Keluar tanpa perubahan" . PHP_EOL;

echo PHP_EOL . "Pilih opsi (1-4): ";
$handle = fopen("php://stdin", "r");
$choice = trim(fgets($handle));
fclose($handle);

switch ($choice) {
    case '1':
        if ($triwulan2) {
            // Nonaktifkan semua periode
            App\Models\Periode::where('tahun_id', $tahunAktif->id)->update(['status' => 0]);
            
            // Aktifkan Triwulan 2 dan extend tanggal selesai
            $triwulan2->update([
                'status' => 1,
                'tanggal_selesai' => $tahunAktif->tahun . '-12-31'
            ]);
            
            echo "✅ Triwulan 2 berhasil diaktifkan!" . PHP_EOL;
            echo "   Tanggal selesai diperpanjang hingga 31 Desember " . $tahunAktif->tahun . PHP_EOL;
        } else {
            echo "❌ Triwulan 2 tidak ditemukan!" . PHP_EOL;
        }
        break;
        
    case '2':
        if ($triwulan3) {
            // Nonaktifkan semua periode
            App\Models\Periode::where('tahun_id', $tahunAktif->id)->update(['status' => 0]);
            
            // Aktifkan Triwulan 3 dan extend tanggal selesai
            $triwulan3->update([
                'status' => 1,
                'tanggal_selesai' => $tahunAktif->tahun . '-12-31'
            ]);
            
            echo "✅ Triwulan 3 berhasil diaktifkan!" . PHP_EOL;
            echo "   Tanggal selesai diperpanjang hingga 31 Desember " . $tahunAktif->tahun . PHP_EOL;
        } else {
            echo "❌ Triwulan 3 tidak ditemukan!" . PHP_EOL;
        }
        break;
        
    case '3':
        echo "✅ Triwulan 1 tetap aktif." . PHP_EOL;
        break;
        
    case '4':
        echo "✅ Keluar tanpa perubahan." . PHP_EOL;
        exit(0);
        
    default:
        echo "❌ Pilihan tidak valid!" . PHP_EOL;
        exit(1);
}

echo PHP_EOL . "=== STATUS AKHIR ===" . PHP_EOL;

// Show final status
$finalPeriods = App\Models\Periode::with(['tahap', 'tahun'])
    ->where('tahun_id', $tahunAktif->id)
    ->whereHas('tahap', function($query) {
        $query->whereIn('tahap', ['Triwulan 1', 'Triwulan 2', 'Triwulan 3']);
    })
    ->get();

foreach ($finalPeriods as $periode) {
    $status = $periode->status == 1 ? '🟢 AKTIF' : '🔴 TIDAK AKTIF';
    echo "{$periode->tahap->tahap}: {$status} (Selesai: {$periode->tanggal_selesai})" . PHP_EOL;
}

echo PHP_EOL . "✅ Script selesai. Tombol parsial sekarang dapat digunakan untuk mengakses periode yang aktif." . PHP_EOL;
