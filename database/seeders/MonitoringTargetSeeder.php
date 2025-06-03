<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\MonitoringTarget;
use App\Models\SumberAnggaran;
use App\Models\SkpdTugas;
use App\Models\Periode;
use App\Models\PeriodeTahun;
use App\Models\PeriodeTahap;

// ... namespace dan use tetap

class MonitoringTargetSeeder extends Seeder
{
    public function run(): void
    {
        $sumberAnggaranIds = SumberAnggaran::pluck('id')->toArray();
        if (empty($sumberAnggaranIds)) {
            $this->command->warn('Seeder gagal: Tidak ada data sumber anggaran.');
            return;
        }

        $periodeTahap = PeriodeTahap::whereIn('tahap', ['Rencana','Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4', 'Evaluasi Akhir'])->get()->keyBy('tahap');
        if ($periodeTahap->isEmpty()) {
            $this->command->warn('Seeder gagal: Tidak ada data periode tahap.');
            return;
        }

        $periodeTahunList = collect([2025])->mapWithKeys(function ($tahun) {
            $periodeTahun = PeriodeTahun::firstOrCreate(
                ['tahun' => $tahun],
                ['status' => 1]
            );
            return [$tahun => $periodeTahun->id];
        });

        $periodePerTahun = [];
        foreach ($periodeTahunList as $tahun => $tahunId) {
            $periodePerTahun[$tahun] = collect();
            foreach ($periodeTahap as $nama => $tahap) {
               $tanggalMulai = match ($nama) {
                    'Rencana' => "$tahun-01-01",
                    'Triwulan 1' => "$tahun-02-01",
                    'Triwulan 2' => "$tahun-04-01",
                    'Triwulan 3' => "$tahun-06-01",
                    'Triwulan 4' => "$tahun-08-01",
                    'Evaluasi Akhir' => "$tahun-10-01", 
                };

                $tanggalSelesai = match ($nama) {
                    'Rencana' => "$tahun-01-01",
                    'Triwulan 1' => "$tahun-03-31",
                    'Triwulan 2' => "$tahun-06-30",
                    'Triwulan 3' => "$tahun-09-30",
                    'Triwulan 4' => "$tahun-09-30",        
                    'Evaluasi Akhir' => "$tahun-12-31",    
                };


                $periode = Periode::firstOrCreate([
                    'tahap_id' => $tahap->id,
                    'tahun_id' => $tahunId,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                ], [
                    'status' => 2 // ✅ Status "selesai" (data historis)
                ]);

                $periodePerTahun[$tahun]->push($periode);
            }
        }

        $skpdTugasList = SkpdTugas::with('skpd')->where('is_aktif', true)->get();
        if ($skpdTugasList->isEmpty()) {
            $this->command->warn('Seeder gagal: Tidak ada SKPD Tugas aktif.');
            return;
        }

        foreach ($skpdTugasList as $skpdTugas) {
            foreach ($periodeTahunList as $tahun => $tahunId) {
                $defaultPeriode = $periodePerTahun[$tahun]->first();

                $monitoring = Monitoring::create([
                    'skpd_tugas_id' => $skpdTugas->id,
                    'periode_id' => $defaultPeriode->id,
                    'tahun' => $tahun,
                    'deskripsi' => "Monitoring {$skpdTugas->skpd->nama_dinas} Tahun $tahun",
                    'nama_pptk' => "PPTK " . $skpdTugas->skpd->nama_skpd,
                ]);

                $selectedAnggaranIds = collect($sumberAnggaranIds)->random(min(3, count($sumberAnggaranIds)));

                foreach ($selectedAnggaranIds as $sumberAnggaranId) {
                    $monitoringAnggaran = MonitoringAnggaran::create([
                        'sumber_anggaran_id' => $sumberAnggaranId,
                        'monitoring_id' => $monitoring->id,
                    ]);

                    foreach ($periodePerTahun[$tahun] as $periode) {
                        MonitoringPagu::create([
                            'monitoring_anggaran_id' => $monitoringAnggaran->id,
                            'periode_id' => $periode->id,
                            'kategori' => rand(1, 3),
                            'dana' => 500_000_000, // nilai tetap contoh
                        ]);

                        $tahapNama = $periode->tahap->tahap;

                        $kinerjaFisik = match ($tahapNama) {
                            'Triwulan 1' => 25,           // 🟡 Triwulan 1
                            'Triwulan 2' => 50,           // 🟠 Triwulan 2
                            'Triwulan 3' => 75,           // 🔵 Triwulan 3
                            'Triwulan 4' => 100,          // 🟢 Triwulan 4
                            default => 0,                 // Rencana
                        };

                        $keuangan = match ($tahapNama) {
                            'Triwulan 1' => 125_000,  // 🟡 Triwulan 1
                            'Triwulan 2' => 250_000,  // 🟠 Triwulan 2
                            'Triwulan 3' => 375_000,  // 🔵 Triwulan 3
                            'Triwulan 4' => 500_000,  // 🟢 Triwulan 4
                            default => 0,                 // Rencana
                        };

                        MonitoringTarget::create([
                            'monitoring_anggaran_id' => $monitoringAnggaran->id,
                            'periode_id' => $periode->id,
                            'kinerja_fisik' => $kinerjaFisik,
                            'keuangan' => $keuangan,
                        ]);
                    }
                }
            }
        }
    }
}