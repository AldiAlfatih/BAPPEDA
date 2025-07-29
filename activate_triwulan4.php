<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ACTIVATING TRIWULAN 4 ===" . PHP_EOL;

// Update Triwulan 4 to be active and extend the end date
$triwulan4 = App\Models\Periode::find(5);
if ($triwulan4) {
    $triwulan4->status = 1;
    $triwulan4->tanggal_selesai = '2025-12-31';
    $triwulan4->save();
    echo '✅ Triwulan 4 activated and extended until 2025-12-31' . PHP_EOL;
    echo "ID: {$triwulan4->id}" . PHP_EOL;
    echo "Status: {$triwulan4->status}" . PHP_EOL;
    echo "Tanggal selesai: {$triwulan4->tanggal_selesai}" . PHP_EOL;
} else {
    echo '❌ Triwulan 4 not found' . PHP_EOL;
}
