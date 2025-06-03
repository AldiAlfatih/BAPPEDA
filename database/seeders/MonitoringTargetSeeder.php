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

class MonitoringTargetSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID sumber anggaran
        $sumberAnggaranIds = SumberAnggaran::pluck('id')->toArray();
        if (empty($sumberAnggaranIds)) {
            $this->command->warn('Seeder gagal: Tidak ada data sumber anggaran.');
            return;
        }

        // Ambil ID tahap triwulan
        $periodeTahap = PeriodeTahap::whereIn('tahap', ['Rencana','Triwulan 1', 'Triwulan 2', 'Triwulan 3', 'Triwulan 4'])->get()->keyBy('tahap');
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

        // Buat periode triwulan untuk tiap tahun
        $periodePerTahun = [];
        foreach ($periodeTahunList as $tahun => $tahunId) {
            $periodePerTahun[$tahun] = collect();
            foreach ($periodeTahap as $nama => $tahap) {
                $tanggalMulai = match ($nama) {
                    'Rencana' => "$tahun-01-01",
                    'Triwulan 1' => "$tahun-01-01",
                    'Triwulan 2' => "$tahun-04-01",
                    'Triwulan 3' => "$tahun-07-01",
                    'Triwulan 4' => "$tahun-10-01",
                };
                $tanggalSelesai = match ($nama) {
                    'Rencana' => "$tahun-01-01",
                    'Triwulan 1' => "$tahun-03-31",
                    'Triwulan 2' => "$tahun-06-30",
                    'Triwulan 3' => "$tahun-09-30",
                    'Triwulan 4' => "$tahun-12-31",
                };

                $periode = Periode::firstOrCreate([
                    'tahap_id' => $tahap->id,
                    'tahun_id' => $tahunId,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                ], ['status' => 1]);

                $periodePerTahun[$tahun]->push($periode);
            }
        }

        // Ambil SKPD Tugas aktif
        $skpdTugasList = SkpdTugas::with('skpd')->where('is_aktif', true)->get();
        if ($skpdTugasList->isEmpty()) {
            $this->command->warn('Seeder gagal: Tidak ada SKPD Tugas aktif.');
            return;
        }

        // Mulai membuat data monitoring & target
        foreach ($skpdTugasList as $skpdTugas) {
            foreach ($periodeTahunList as $tahun => $tahunId) {
                $defaultPeriode = $periodePerTahun[$tahun]->first();

                // Create Monitoring
                $monitoring = Monitoring::create([
                    'skpd_tugas_id' => $skpdTugas->id,
                    'periode_id' => $defaultPeriode->id,
                    'tahun' => $tahun,
                    'deskripsi' => "Monitoring {$skpdTugas->skpd->nama_dinas} Tahun $tahun",
                    'nama_pptk' => "PPTK " . $skpdTugas->skpd->nama_skpd,
                ]);

                // Pilih 2-3 sumber anggaran acak
                $selectedAnggaranIds = collect($sumberAnggaranIds)->random(min(3, count($sumberAnggaranIds)));

                foreach ($selectedAnggaranIds as $sumberAnggaranId) {
                    $monitoringAnggaran = MonitoringAnggaran::create([
                        'sumber_anggaran_id' => $sumberAnggaranId,
                        'monitoring_id' => $monitoring->id,
                    ]);

                    // Buat monitoring pagu dan target untuk tiap triwulan
                    foreach ($periodePerTahun[$tahun] as $periode) {
                        MonitoringPagu::create([
                            'monitoring_anggaran_id' => $monitoringAnggaran->id,
                            'periode_id' => $periode->id,
                            'kategori' => rand(1, 3),
                            'dana' => rand(100_000_000, 1_000_000_000),
                        ]);

                        MonitoringTarget::create([
                            'monitoring_anggaran_id' => $monitoringAnggaran->id,
                            'periode_id' => $periode->id,
                            'kinerja_fisik' => rand(10, 100),
                            'keuangan' => rand(100_000_000, 500_000_000),
                        ]);
                    }
                }
            }
}
}
}
