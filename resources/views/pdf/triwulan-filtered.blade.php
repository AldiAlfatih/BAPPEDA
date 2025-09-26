<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $jenis_laporan }} - {{ $skpd->nama_skpd }}</title>
    <style>
        @page {
            margin: {{ $margin_top ?? 20 }}mm {{ $margin_right ?? 20 }}mm {{ $margin_bottom ?? 20 }}mm {{ $margin_left ?? 20 }}mm;
            size: {{ $paper_size ?? 'A4' }} {{ $orientation ?? 'landscape' }};
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
        }

        .filter-header {
            background-color: #f0f8ff;
            border: 2px solid #4a90e2;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .filter-info {
            font-size: 9px;
            color: #2c5aa0;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            align-items: center;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
            font-size: 10px;
        }

        .info-value {
            flex: 1;
            font-size: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8px;
            table-layout: fixed;
            border: 2px solid #000;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            hyphens: auto;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
            line-height: 1.2;
        }

        .table td.description {
            text-align: left;
            padding-left: 6px;
        }

        .table td.amount {
            text-align: right;
        }

        .table td.center {
            text-align: center;
        }

        .table .number {
            width: 30px;
        }

        .table .code {
            width: 80px;
        }

        .bidang-urusan {
            background-color: #e8f4fd;
            font-weight: bold;
        }

        .program {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .kegiatan {
            background-color: #fff3cd;
            font-weight: bold;
        }

        .subkegiatan {
            background-color: #d4edda;
        }

        .subkegiatan-sumber {
            background-color: #ffffff;
            padding-left: 20px !important;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: right;
            min-width: 180px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 60px;
        }

        .signature-nip {
            font-size: 8px;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <!-- Removed filter header as requested -->

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama SKPD</div>
            <div class="info-value">: {{ $skpd->nama_skpd }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kode Organisasi</div>
            <div class="info-value">: {{ $skpd->kode_organisasi ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak</div>
            <div class="info-value">: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tahun</div>
            <div class="info-value">: {{ $tahun ?? date('Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Periode</div>
            <div class="info-value">: {{ $periode->nama ?? $triwulanName ?? '-' }}</div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table">
        <thead>
            <tr>
                <th rowspan="3" class="number">No</th>
                <th rowspan="3" class="code">Kode</th>
                <th rowspan="3">Bidang Urusan &<br>Program/<br>Kegiatan/Sub<br>Kegiatan</th>
                <th colspan="3">Pagu Anggaran APBD</th>
                <th rowspan="3">Sumber<br>Dana</th>
                <th colspan="2">Target</th>
                <th colspan="3">Realisasi</th>
                <th rowspan="3">Keterangan</th>
                <th rowspan="3">PPTK</th>
            </tr>
            <tr>
                <th class="amount">Pokok<br>(RP)</th>
                <th class="amount">Parsial<br>(RP)</th>
                <th class="amount">Perubahan<br>(RP)</th>
                <th>Kinerja<br>Fisik (%)</th>
                <th>Keuangan<br>(RP)</th>
                <th>Kinerja<br>Fisik (%)</th>
                <th>Keuangan<br>(%)</th>
                <th>Keuangan<br>(RP)</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalPagu = 0;
                $totalTargetKeuangan = 0;
                $totalRealisasiKeuangan = 0;
                $totalTargetFisik = 0;
                $totalRealisasiFisik = 0;
                $itemCount = 0;
            @endphp

            @forelse(($currentUrusan ? collect([$currentUrusan]) : $bidangurusanTugas) as $bidangUrusan)
                @php
                    // Get programs under this bidang urusan that have filtered data
                    $programsInBidang = $programTugas->filter(function ($program) use ($bidangUrusan) {
                        return str_starts_with(
                            $program->kodeNomenklatur->nomor_kode ?? '',
                            $bidangUrusan->kodeNomenklatur->nomor_kode ?? '',
                        );
                    });

                    // Check if bidang urusan has any filtered data before displaying
                    $bidangHasFilteredData = false;
                    $urusanPaguPokok = 0;
                    $urusanPaguParsial = 0;
                    $urusanPaguPerubahan = 0;
                    $urusanTargetFisik = 0;
                    $urusanTargetKeuangan = 0;
                    $urusanRealisasiFisik = 0;
                    $urusanRealisasiKeuangan = 0;
                    $urusanItemCount = 0;

                    // Pre-check if this bidang urusan has any filtered data
                    foreach ($programsInBidang as $program) {
                        $kegiatanInProgram = $kegiatanTugas->filter(function ($kegiatan) use ($program) {
                            return str_starts_with(
                                $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                $program->kodeNomenklatur->nomor_kode ?? '',
                            );
                        });

                        foreach ($kegiatanInProgram as $kegiatan) {
                            $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                return str_starts_with(
                                    $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                );
                            });

                            foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                $targetForTask = collect($monitoringTargets)
                                    ->where('task_id', $subkegiatan->id)
                                    ->where('periode_id', $periode->id);
                                    
                                if ($targetForTask->isNotEmpty()) {
                                    $bidangHasFilteredData = true;
                                    break 3; // Break out of all nested loops
                                }
                            }
                        }
                    }

                    // Calculate totals only if has filtered data
                    if ($bidangHasFilteredData) {
                        foreach ($programsInBidang as $program) {
                            $kegiatanInProgram = $kegiatanTugas->filter(function ($kegiatan) use ($program) {
                                return str_starts_with(
                                    $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    $program->kodeNomenklatur->nomor_kode ?? '',
                                );
                            });

                            foreach ($kegiatanInProgram as $kegiatan) {
                                $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                    return str_starts_with(
                                        $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                        $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    );
                                });

                                foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                    $targetForTask = collect($monitoringTargets)
                                        ->where('task_id', $subkegiatan->id)
                                        ->where('periode_id', $periode->id);
                                    $realisasiForTask = collect($monitoringRealisasi)
                                        ->where('task_id', $subkegiatan->id)
                                        ->where('periode_id', $periode->id);
                                    $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');
                                    $realisasiBySumber = $realisasiForTask->groupBy('sumber_anggaran_id');
                                    $allSumberIds = collect($targetsBySumber->keys())
                                        ->merge($realisasiBySumber->keys())
                                        ->unique();

                                    foreach ($allSumberIds as $sumberAnggaranId) {
                                        $targetItem = $targetsBySumber->get($sumberAnggaranId)?->first();
                                        $realisasiItem = $realisasiBySumber->get($sumberAnggaranId)?->first();

                                        $urusanPaguPokok += $targetItem['pagu_pokok'] ?? 0;
                                        $urusanPaguParsial += $targetItem['pagu_parsial'] ?? 0;
                                        $urusanPaguPerubahan += $targetItem['pagu_perubahan'] ?? 0;
                                        $urusanTargetFisik += $targetItem['kinerja_fisik'] ?? 0;
                                        $urusanTargetKeuangan += $targetItem['keuangan'] ?? 0;
                                        $urusanRealisasiFisik += $realisasiItem['kinerja_fisik'] ?? 0;
                                        $urusanRealisasiKeuangan += $realisasiItem['keuangan'] ?? 0;
                                        $urusanItemCount++;
                                    }
                                }
                            }
                        }
                    }

                    $avgUrusanTargetFisik = $urusanItemCount > 0 ? $urusanTargetFisik / $urusanItemCount : 0;
                    $avgUrusanRealisasiFisik = $urusanItemCount > 0 ? $urusanRealisasiFisik / $urusanItemCount : 0;
                    
                    // Calculate percentage for urusan financial realization
                    $totalUrusanPagu = $urusanPaguPokok + $urusanPaguParsial + $urusanPaguPerubahan;
                    $persentaseUrusanKeuangan = $totalUrusanPagu > 0 ? ($urusanRealisasiKeuangan / $totalUrusanPagu) * 100 : 0;
                @endphp

                @if($bidangHasFilteredData)
                    <!-- Bidang Urusan Row -->
                    <tr class="bidang-urusan">
                        <td>{{ $no++ }}</td>
                        <td>{{ $bidangUrusan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                        <td class="description"><strong>BIDANG URUSAN: {{ $bidangUrusan->kodeNomenklatur->nomenklatur ?? '-' }}</strong></td>
                        <td class="center">
                            <strong>{{ $urusanPaguPokok > 0 ? 'Rp ' . number_format($urusanPaguPokok, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center">
                            <strong>{{ $urusanPaguParsial > 0 ? 'Rp ' . number_format($urusanPaguParsial, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center">
                            <strong>{{ $urusanPaguPerubahan > 0 ? 'Rp ' . number_format($urusanPaguPerubahan, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center">-</td>
                        <td class="center"><strong>{{ number_format($avgUrusanTargetFisik, 1) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $urusanTargetKeuangan > 0 ? 'Rp ' . number_format($urusanTargetKeuangan, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center"><strong>{{ number_format($avgUrusanRealisasiFisik, 1) }}%</strong></td>
                        <td class="center"><strong>{{ number_format($persentaseUrusanKeuangan, 2) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $urusanRealisasiKeuangan > 0 ? 'Rp ' . number_format($urusanRealisasiKeuangan, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center">-</td>
                        <td class="center">-</td>
                    </tr>

                    @foreach ($programsInBidang as $program)
                        @php
                            // Check if program has filtered data
                            $programHasFilteredData = false;
                            $kegiatanInProgram = $kegiatanTugas->filter(function ($kegiatan) use ($program) {
                                return str_starts_with(
                                    $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    $program->kodeNomenklatur->nomor_kode ?? '',
                                );
                            });

                            foreach ($kegiatanInProgram as $kegiatan) {
                                $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                    return str_starts_with(
                                        $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                        $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    );
                                });

                                foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                    $targetForTask = collect($monitoringTargets)
                                        ->where('task_id', $subkegiatan->id)
                                        ->where('periode_id', $periode->id);
                                        
                                    if ($targetForTask->isNotEmpty()) {
                                        $programHasFilteredData = true;
                                        break 2;
                                    }
                                }
                            }
                        @endphp

                        @if($programHasFilteredData)
                            @php
                                // Calculate program totals from filtered data
                                $programPaguPokok = 0;
                                $programPaguParsial = 0;
                                $programPaguPerubahan = 0;
                                $programTargetFisik = 0;
                                $programTargetKeuangan = 0;
                                $programRealisasiFisik = 0;
                                $programRealisasiKeuangan = 0;
                                $programItemCount = 0;

                                $kegiatanInProgram = $kegiatanTugas->filter(function ($kegiatan) use ($program) {
                                    return str_starts_with(
                                        $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                        $program->kodeNomenklatur->nomor_kode ?? '',
                                    );
                                });

                                foreach ($kegiatanInProgram as $kegiatan) {
                                    $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                        return str_starts_with(
                                            $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                            $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                        );
                                    });

                                    foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                        $targetForTask = collect($monitoringTargets)
                                            ->where('task_id', $subkegiatan->id)
                                            ->where('periode_id', $periode->id);
                                        $realisasiForTask = collect($monitoringRealisasi)
                                            ->where('task_id', $subkegiatan->id)
                                            ->where('periode_id', $periode->id);

                                        foreach ($targetForTask as $target) {
                                            $programPaguPokok += $target['pagu_pokok'] ?? 0;
                                            $programPaguParsial += $target['pagu_parsial'] ?? 0;
                                            $programPaguPerubahan += $target['pagu_perubahan'] ?? 0;
                                            $programTargetFisik += $target['kinerja_fisik'] ?? 0;
                                            $programTargetKeuangan += $target['keuangan'] ?? 0;
                                            $programItemCount++;
                                        }

                                        foreach ($realisasiForTask as $realisasi) {
                                            $programRealisasiFisik += $realisasi['kinerja_fisik'] ?? 0;
                                            $programRealisasiKeuangan += $realisasi['keuangan'] ?? 0;
                                        }
                                    }
                                }

                                $avgProgramTargetFisik = $programItemCount > 0 ? $programTargetFisik / $programItemCount : 0;
                                $avgProgramRealisasiFisik = $programItemCount > 0 ? $programRealisasiFisik / $programItemCount : 0;
                                
                                // Calculate percentage for program financial realization
                                $totalProgramPagu = $programPaguPokok + $programPaguParsial + $programPaguPerubahan;
                                $persentaseProgramKeuangan = $totalProgramPagu > 0 ? ($programRealisasiKeuangan / $totalProgramPagu) * 100 : 0;
                            @endphp

                            <!-- Program Row -->
                            <tr class="program">
                                <td>{{ $no++ }}</td>
                                <td>{{ $program->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                                <td class="description">PROGRAM: {{ $program->kodeNomenklatur->nomenklatur ?? '-' }}</td>
                                <td class="center">{{ $programPaguPokok > 0 ? 'Rp ' . number_format($programPaguPokok, 0, ',', '.') : '-' }}</td>
                                <td class="center">{{ $programPaguParsial > 0 ? 'Rp ' . number_format($programPaguParsial, 0, ',', '.') : '-' }}</td>
                                <td class="center">{{ $programPaguPerubahan > 0 ? 'Rp ' . number_format($programPaguPerubahan, 0, ',', '.') : '-' }}</td>
                                <td class="center">-</td>
                                <td class="center">{{ number_format($avgProgramTargetFisik, 1) }}%</td>
                                <td class="center">{{ $programTargetKeuangan > 0 ? 'Rp ' . number_format($programTargetKeuangan, 0, ',', '.') : '-' }}</td>
                                <td class="center">{{ number_format($avgProgramRealisasiFisik, 1) }}%</td>
                                <td class="center">{{ number_format($persentaseProgramKeuangan, 2) }}%</td>
                                <td class="center">{{ $programRealisasiKeuangan > 0 ? 'Rp ' . number_format($programRealisasiKeuangan, 0, ',', '.') : '-' }}</td>
                                <td class="center">-</td>
                                <td class="center">-</td>
                            </tr>

                            @foreach ($kegiatanInProgram as $kegiatan)
                                @php
                                    // Check if kegiatan has filtered data
                                    $kegiatanHasFilteredData = false;
                                    $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                        return str_starts_with(
                                            $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                            $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                        );
                                    });

                                    foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                        $targetForTask = collect($monitoringTargets)
                                            ->where('task_id', $subkegiatan->id)
                                            ->where('periode_id', $periode->id);
                                            
                                        if ($targetForTask->isNotEmpty()) {
                                            $kegiatanHasFilteredData = true;
                                            break;
                                        }
                                    }
                                @endphp

                                @if($kegiatanHasFilteredData)
                                    @php
                                        // Calculate kegiatan totals from filtered data
                                        $kegiatanPaguPokok = 0;
                                        $kegiatanPaguParsial = 0;
                                        $kegiatanPaguPerubahan = 0;
                                        $kegiatanTargetFisik = 0;
                                        $kegiatanTargetKeuangan = 0;
                                        $kegiatanRealisasiFisik = 0;
                                        $kegiatanRealisasiKeuangan = 0;
                                        $kegiatanItemCount = 0;

                                        foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                            $targetForTask = collect($monitoringTargets)
                                                ->where('task_id', $subkegiatan->id)
                                                ->where('periode_id', $periode->id);
                                            $realisasiForTask = collect($monitoringRealisasi)
                                                ->where('task_id', $subkegiatan->id)
                                                ->where('periode_id', $periode->id);

                                            foreach ($targetForTask as $target) {
                                                $kegiatanPaguPokok += $target['pagu_pokok'] ?? 0;
                                                $kegiatanPaguParsial += $target['pagu_parsial'] ?? 0;
                                                $kegiatanPaguPerubahan += $target['pagu_perubahan'] ?? 0;
                                                $kegiatanTargetFisik += $target['kinerja_fisik'] ?? 0;
                                                $kegiatanTargetKeuangan += $target['keuangan'] ?? 0;
                                                $kegiatanItemCount++;
                                            }

                                            foreach ($realisasiForTask as $realisasi) {
                                                $kegiatanRealisasiFisik += $realisasi['kinerja_fisik'] ?? 0;
                                                $kegiatanRealisasiKeuangan += $realisasi['keuangan'] ?? 0;
                                            }
                                        }

                                        $avgKegiatanTargetFisik = $kegiatanItemCount > 0 ? $kegiatanTargetFisik / $kegiatanItemCount : 0;
                                        $avgKegiatanRealisasiFisik = $kegiatanItemCount > 0 ? $kegiatanRealisasiFisik / $kegiatanItemCount : 0;
                                        
                                        // Calculate percentage for kegiatan financial realization
                                        $totalKegiatanPagu = $kegiatanPaguPokok + $kegiatanPaguParsial + $kegiatanPaguPerubahan;
                                        $persentaseKegiatanKeuangan = $totalKegiatanPagu > 0 ? ($kegiatanRealisasiKeuangan / $totalKegiatanPagu) * 100 : 0;
                                    @endphp

                                    <!-- Kegiatan Row -->
                                    <tr class="kegiatan">
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $kegiatan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                                        <td class="description">KEGIATAN: {{ $kegiatan->kodeNomenklatur->nomenklatur ?? '-' }}</td>
                                        <td class="center">{{ $kegiatanPaguPokok > 0 ? 'Rp ' . number_format($kegiatanPaguPokok, 0, ',', '.') : '-' }}</td>
                                        <td class="center">{{ $kegiatanPaguParsial > 0 ? 'Rp ' . number_format($kegiatanPaguParsial, 0, ',', '.') : '-' }}</td>
                                        <td class="center">{{ $kegiatanPaguPerubahan > 0 ? 'Rp ' . number_format($kegiatanPaguPerubahan, 0, ',', '.') : '-' }}</td>
                                        <td class="center">-</td>
                                        <td class="center">{{ number_format($avgKegiatanTargetFisik, 1) }}%</td>
                                        <td class="center">{{ $kegiatanTargetKeuangan > 0 ? 'Rp ' . number_format($kegiatanTargetKeuangan, 0, ',', '.') : '-' }}</td>
                                        <td class="center">{{ number_format($avgKegiatanRealisasiFisik, 1) }}%</td>
                                        <td class="center">{{ number_format($persentaseKegiatanKeuangan, 2) }}%</td>
                                        <td class="center">{{ $kegiatanRealisasiKeuangan > 0 ? 'Rp ' . number_format($kegiatanRealisasiKeuangan, 0, ',', '.') : '-' }}</td>
                                        <td class="center">-</td>
                                        <td class="center">-</td>
                                    </tr>

                                    @foreach ($subKegiatanInKegiatan as $subkegiatan)
                                        @php
                                            // Get targets and realisasi for this subkegiatan (filtered data only)
                                            $targetForTask = collect($monitoringTargets)
                                                ->where('task_id', $subkegiatan->id)
                                                ->where('periode_id', $periode->id);
                                            $realisasiForTask = collect($monitoringRealisasi)
                                                ->where('task_id', $subkegiatan->id)
                                                ->where('periode_id', $periode->id);

                                            // Group by sumber anggaran
                                            $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');
                                            $realisasiBySumber = $realisasiForTask->groupBy('sumber_anggaran_id');

                                            // Get all unique sumber anggaran for this task (only from filtered data)
                                            $allSumberIds = collect($targetsBySumber->keys())
                                                ->merge($realisasiBySumber->keys())
                                                ->unique();
                                        @endphp

                                        @if($allSumberIds->isNotEmpty())
                                            @foreach ($allSumberIds as $sumberAnggaranId)
                                                @php
                                                    $targetItem = $targetsBySumber->get($sumberAnggaranId)?->first();
                                                    $realisasiItem = $realisasiBySumber->get($sumberAnggaranId)?->first();

                                                    $paguPokok = $targetItem['pagu_pokok'] ?? 0;
                                                    $paguParsial = $targetItem['pagu_parsial'] ?? 0;
                                                    $paguPerubahan = $targetItem['pagu_perubahan'] ?? 0;
                                                    $totalPaguItem = $paguPokok + $paguParsial + $paguPerubahan;

                                                    $targetFisik = $targetItem['kinerja_fisik'] ?? 0;
                                                    $targetKeuangan = $targetItem['keuangan'] ?? 0;
                                                    $realisasiFisik = $realisasiItem['kinerja_fisik'] ?? 0;
                                                    $realisasiKeuangan = $realisasiItem['keuangan'] ?? 0;

                                                    // Accumulate totals
                                                    $totalPagu += $totalPaguItem;
                                                    $totalTargetKeuangan += $targetKeuangan;
                                                    $totalRealisasiKeuangan += $realisasiKeuangan;
                                                    $totalTargetFisik += $targetFisik;
                                                    $totalRealisasiFisik += $realisasiFisik;
                                                    $itemCount++;

                                                    // Calculate percentage for financial realization
                                                    $persentaseKeuangan = 0;
                                                    if ($totalPaguItem > 0) {
                                                        $persentaseKeuangan = ($realisasiKeuangan / $totalPaguItem) * 100;
                                                    }

                                                    // Get PPTK and Keterangan
                                                    $pptk = $realisasiItem['nama_pptk'] ?? '-';
                                                    $keterangan = $realisasiItem['deskripsi'] ?? '-';
                                                    $sumberDana = $targetItem['sumber_anggaran_nama'] ?? 
                                                        ($realisasiItem['sumber_anggaran_nama'] ?? '-');
                                                @endphp

                                                <!-- Sub Kegiatan Row -->
                                                <tr class="subkegiatan-sumber">
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $subkegiatan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                                                    <td class="description">
                                                        SUB KEGIATAN: {{ $subkegiatan->kodeNomenklatur->nomenklatur ?? '-' }}
                                                        <br><span style="margin-left: 20px; font-style: italic; color: #666;">└─ {{ $sumberDana }}</span>
                                                    </td>
                                                    <td class="center">
                                                        {{ $paguPokok > 0 ? 'Rp ' . number_format($paguPokok, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="center">
                                                        {{ $paguParsial > 0 ? 'Rp ' . number_format($paguParsial, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="center">
                                                        {{ $paguPerubahan > 0 ? 'Rp ' . number_format($paguPerubahan, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="description">{{ $sumberDana }}</td>
                                                    <td class="center">{{ number_format($targetFisik, 1) }}%</td>
                                                    <td class="center">
                                                        {{ $targetKeuangan > 0 ? 'Rp ' . number_format($targetKeuangan, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="center">{{ number_format($realisasiFisik, 1) }}%</td>
                                                    <td class="center">{{ number_format($persentaseKeuangan, 2) }}%</td>
                                                    <td class="center">
                                                        {{ $realisasiKeuangan > 0 ? 'Rp ' . number_format($realisasiKeuangan, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="description" style="font-size: 7px;">{{ $keterangan }}</td>
                                                    <td class="description" style="font-size: 7px;">{{ $pptk }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="14" class="center" style="padding: 20px;">
                        <strong>Tidak ada data untuk sumber dana yang dipilih</strong>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    <!-- <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
        <h4 style="margin: 0 0 10px 0; font-size: 10px;">📊 Ringkasan Laporan Selektif</h4>
        <div style="font-size: 8px;">
            <div style="margin-bottom: 3px;"><strong>Sumber Dana Terpilih:</strong> {{ implode(', ', $selectedSumberDanaNames) }}</div>
            <div style="margin-bottom: 3px;"><strong>Total Item:</strong> {{ $itemCount }} entri</div>
            <div style="margin-bottom: 3px;"><strong>Total Pagu:</strong> Rp {{ number_format($totalPagu, 0, ',', '.') }}</div>
            <div style="margin-bottom: 3px;"><strong>Total Target:</strong> Rp {{ number_format($totalTargetKeuangan, 0, ',', '.') }}</div>
            <div><strong>Total Realisasi:</strong> Rp {{ number_format($totalRealisasiKeuangan, 0, ',', '.') }}</div>
        </div>
    </div> -->

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div>{{ $penandatangan_1['tempat'] }}, {{ \Carbon\Carbon::parse($penandatangan_1['tanggal'])->format('d F Y') }}</div>
            <div>{{ $penandatangan_1['jabatan'] }}</div>
            <div class="signature-name">{{ $penandatangan_1['nama'] }}</div>
            <div class="signature-nip">NIP: {{ $penandatangan_1['nip'] }}</div>
        </div>
    </div>
</body>

</html> 