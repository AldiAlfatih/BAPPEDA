<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $jenis_laporan }} - {{ $skpd->nama_skpd }}</title>
    <style>
        @page {
            margin: {{ $margin_top ?? 20 }}mm {{ $margin_right ?? 20 }}mm {{ $margin_bottom ?? 20 }}mm {{ $margin_left ?? 20 }}mm;
            size: {{ $paper_size ?? 'A4' }} {{ $orientation ?? 'portrait' }};
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .header {
            display: none;
        }

        .info-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: center;
        }

        .info-label {
            width: 140px;
            font-weight: 600;
            color: #495057;
            font-size: 11px;
        }

        .info-value {
            flex: 1;
            color: #212529;
            font-size: 11px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9px;
            table-layout: fixed;
            border: 2px solid #000;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            hyphens: auto;
            font-family: 'Times New Roman', Times, serif;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
            line-height: 1.2;
            vertical-align: middle;
        }

        .table td.description {
            text-align: left;
        }

        .table td.amount {
            text-align: right;
        }

        .table td.center {
            text-align: center;
        }

        .table .number {
            text-align: center;
            width: 20px;
            min-width: 20px;
        }

        .table .code {
            width: 60px;
            min-width: 60px;
            font-size: 7px;
        }

        .table .amount {
            text-align: center;
            width: 80px;
            min-width: 80px;
            font-size: 7px;
        }

        .table .percent {
            text-align: center;
            width: 40px;
            min-width: 40px;
            font-size: 7px;
        }

        .table .description {
            width: auto;
            max-width: 120px;
            word-wrap: break-word;
            font-size: 7px;
        }

        .table .notes {
            width: auto;

            @if ($orientation === 'landscape')
                max-width: 120px;
            @else
                max-width: 80px;
            @endif
            word-wrap: break-word;
            font-size: 7px;
        }

        @if ($orientation === 'landscape')
            /* Landscape specific styles */
            .table {
                font-size: 9px !important;
            }

            .table .description {
                max-width: 200px;
                font-size: 8px;
            }

            .table .amount {
                width: 90px;
                min-width: 90px;
                font-size: 8px;
            }

            .table .percent {
                width: 50px;
                min-width: 50px;
                font-size: 8px;
            }

            .table .code {
                width: 80px;
                min-width: 80px;
                font-size: 8px;
            }

            .table .notes {
                font-size: 8px;
            }
        @endif

        .signature-section {
            margin-top: 40px;
            width: 100%;
            position: relative;
            height: 120px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
            position: absolute;
            top: 0;
            right: 0;
        }

        .signature-center {
            text-align: center;
        }

        .signature-box p {
            margin: 5px 0;
            font-size: 11px;
        }

        .signature-space {
            height: 80px;
            margin: 20px 0;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .sub-row {
            background-color: #f9f9f9;
            font-style: italic;
        }

        .realisasi-section {
            background-color: #e8f4f8;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            position: fixed;
            bottom: 1cm;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
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
                <th class="percent">Kinerja<br>Fisik (%)</th>
                <th class="amount">Keuangan<br>(RP)</th>
                <th class="percent">Kinerja<br>Fisik (%)</th>
                <th class="percent">Keuangan<br>(%)</th>
                <th class="amount">Keuangan<br>(RP)</th>
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
                    // Get programs under this bidang urusan
                    $programsInBidang = $programTugas->filter(function ($program) use ($bidangUrusan) {
                        return str_starts_with(
                            $program->kodeNomenklatur->nomor_kode ?? '',
                            $bidangUrusan->kodeNomenklatur->nomor_kode ?? '',
                        );
                    });

                    // Calculate total for urusan (akumulasi dari semua sub kegiatan)
                    $urusanPaguPokok = 0;
                    $urusanPaguParsial = 0;
                    $urusanPaguPerubahan = 0;
                    $urusanTargetFisik = 0;
                    $urusanTargetKeuangan = 0;
                    $urusanRealisasiFisik = 0;
                    $urusanRealisasiKeuangan = 0;
                    $urusanItemCount = 0;

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

                                foreach ($allSumberIds->isEmpty() ? [null] : $allSumberIds as $sumberAnggaranId) {
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

                    $avgUrusanTargetFisik = $urusanItemCount > 0 ? $urusanTargetFisik / $urusanItemCount : 0;
                    $avgUrusanRealisasiFisik = $urusanItemCount > 0 ? $urusanRealisasiFisik / $urusanItemCount : 0;
                    // Persentase keuangan realisasi urusan terhadap total pagu
                    $urusanTotalPaguAll = ($urusanPaguPokok + $urusanPaguParsial + $urusanPaguPerubahan);
                    $urusanPersentaseKeuangan = $urusanTotalPaguAll > 0 ? ($urusanRealisasiKeuangan / $urusanTotalPaguAll) * 100 : 0;
                @endphp

                <!-- Bidang Urusan Row dengan akumulasi -->
                <tr style="background-color: #e8f4fd; font-weight: bold;">
                    <td>{{ $no++ }}</td>
                    <td>{{ $bidangUrusan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                    <td class="description"><strong>BIDANG URUSAN:
                            {{ $bidangUrusan->kodeNomenklatur->nomenklatur ?? '-' }}</strong></td>
                    <td class="center">
                        <strong>{{ $urusanPaguPokok > 0 ? 'Rp ' . number_format($urusanPaguPokok, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center">
                        <strong>{{ $urusanPaguParsial > 0 ? 'Rp ' . number_format($urusanPaguParsial, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center">
                        <strong>{{ $urusanPaguPerubahan > 0 ? 'Rp ' . number_format($urusanPaguPerubahan, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td class="center"><strong>{{ number_format($avgUrusanTargetFisik, 1) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $urusanTargetKeuangan > 0 ? 'Rp ' . number_format($urusanTargetKeuangan, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center"><strong>{{ number_format($avgUrusanRealisasiFisik, 1) }}%</strong></td>
                    <!-- Realisasi Keuangan (%) -->
                    <td class="center"><strong>{{ number_format($urusanPersentaseKeuangan, 2) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $urusanRealisasiKeuangan > 0 ? 'Rp ' . number_format($urusanRealisasiKeuangan, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>

                @foreach ($programsInBidang as $program)
                    @php
                        // Get kegiatan under this program
                        $kegiatanInProgram = $kegiatanTugas->filter(function ($kegiatan) use ($program) {
                            return str_starts_with(
                                $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                $program->kodeNomenklatur->nomor_kode ?? '',
                            );
                        });

                        // Calculate total for program
                        $programPaguPokok = 0;
                        $programPaguParsial = 0;
                        $programPaguPerubahan = 0;
                        $programTargetFisik = 0;
                        $programTargetKeuangan = 0;
                        $programRealisasiFisik = 0;
                        $programRealisasiKeuangan = 0;
                        $programItemCount = 0;

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

                                foreach ($allSumberIds->isEmpty() ? [null] : $allSumberIds as $sumberAnggaranId) {
                                    $targetItem = $targetsBySumber->get($sumberAnggaranId)?->first();
                                    $realisasiItem = $realisasiBySumber->get($sumberAnggaranId)?->first();

                                    $programPaguPokok += $targetItem['pagu_pokok'] ?? 0;
                                    $programPaguParsial += $targetItem['pagu_parsial'] ?? 0;
                                    $programPaguPerubahan += $targetItem['pagu_perubahan'] ?? 0;
                                    $programTargetFisik += $targetItem['kinerja_fisik'] ?? 0;
                                    $programTargetKeuangan += $targetItem['keuangan'] ?? 0;
                                    $programRealisasiFisik += $realisasiItem['kinerja_fisik'] ?? 0;
                                    $programRealisasiKeuangan += $realisasiItem['keuangan'] ?? 0;
                                    $programItemCount++;
                                }
                            }
                        }

                        $avgProgramTargetFisik = $programItemCount > 0 ? $programTargetFisik / $programItemCount : 0;
                        $avgProgramRealisasiFisik =
                            $programItemCount > 0 ? $programRealisasiFisik / $programItemCount : 0;
                        // Persentase keuangan realisasi program terhadap total pagu
                        $programTotalPaguAll = ($programPaguPokok + $programPaguParsial + $programPaguPerubahan);
                        $programPersentaseKeuangan = $programTotalPaguAll > 0 ? ($programRealisasiKeuangan / $programTotalPaguAll) * 100 : 0;
                    @endphp

                    <!-- Program Row dengan akumulasi -->
                    <tr style="background-color: #f0f8ff; font-weight: bold;">
                        <td>{{ $no++ }}</td>
                        <td>{{ $program->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                        <td class="description"><strong>PROGRAM:
                                {{ $program->kodeNomenklatur->nomenklatur ?? '-' }}</strong></td>
                        <td class="center">
                            <strong>{{ $programPaguPokok > 0 ? 'Rp ' . number_format($programPaguPokok, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center">
                            <strong>{{ $programPaguParsial > 0 ? 'Rp ' . number_format($programPaguParsial, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center">
                            <strong>{{ $programPaguPerubahan > 0 ? 'Rp ' . number_format($programPaguPerubahan, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td>&nbsp;</td>
                        <td class="center"><strong>{{ number_format($avgProgramTargetFisik, 1) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $programTargetKeuangan > 0 ? 'Rp ' . number_format($programTargetKeuangan, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center"><strong>{{ number_format($avgProgramRealisasiFisik, 1) }}%</strong></td>
                        <!-- Realisasi Keuangan (%) -->
                        <td class="center"><strong>{{ number_format($programPersentaseKeuangan, 2) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $programRealisasiKeuangan > 0 ? 'Rp ' . number_format($programRealisasiKeuangan, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    @foreach ($kegiatanInProgram as $kegiatan)
                        @php
                            // Get sub kegiatan under this kegiatan
                            $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                return str_starts_with(
                                    $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                );
                            });

                            // Calculate total for kegiatan
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
                                $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');
                                $realisasiBySumber = $realisasiForTask->groupBy('sumber_anggaran_id');
                                $allSumberIds = collect($targetsBySumber->keys())
                                    ->merge($realisasiBySumber->keys())
                                    ->unique();

                                foreach ($allSumberIds->isEmpty() ? [null] : $allSumberIds as $sumberAnggaranId) {
                                    $targetItem = $targetsBySumber->get($sumberAnggaranId)?->first();
                                    $realisasiItem = $realisasiBySumber->get($sumberAnggaranId)?->first();

                                    $kegiatanPaguPokok += $targetItem['pagu_pokok'] ?? 0;
                                    $kegiatanPaguParsial += $targetItem['pagu_parsial'] ?? 0;
                                    $kegiatanPaguPerubahan += $targetItem['pagu_perubahan'] ?? 0;
                                    $kegiatanTargetFisik += $targetItem['kinerja_fisik'] ?? 0;
                                    $kegiatanTargetKeuangan += $targetItem['keuangan'] ?? 0;
                                    $kegiatanRealisasiFisik += $realisasiItem['kinerja_fisik'] ?? 0;
                                    $kegiatanRealisasiKeuangan += $realisasiItem['keuangan'] ?? 0;
                                    $kegiatanItemCount++;
                                }
                            }

                            $avgKegiatanTargetFisik =
                                $kegiatanItemCount > 0 ? $kegiatanTargetFisik / $kegiatanItemCount : 0;
                            $avgKegiatanRealisasiFisik =
                                $kegiatanItemCount > 0 ? $kegiatanRealisasiFisik / $kegiatanItemCount : 0;
                            // Persentase keuangan realisasi kegiatan terhadap total pagu
                            $kegiatanTotalPaguAll = ($kegiatanPaguPokok + $kegiatanPaguParsial + $kegiatanPaguPerubahan);
                            $kegiatanPersentaseKeuangan = $kegiatanTotalPaguAll > 0 ? ($kegiatanRealisasiKeuangan / $kegiatanTotalPaguAll) * 100 : 0;
                        @endphp

                        <!-- Kegiatan Row dengan akumulasi -->
                        <tr style="background-color: #f8fcff; font-weight: bold;">
                            <td>{{ $no++ }}</td>
                            <td>{{ $kegiatan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                            <td class="description"><strong>KEGIATAN:
                                    {{ $kegiatan->kodeNomenklatur->nomenklatur ?? '-' }}</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanPaguPokok > 0 ? 'Rp ' . number_format($kegiatanPaguPokok, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td class="center">
                                <strong>{{ $kegiatanPaguParsial > 0 ? 'Rp ' . number_format($kegiatanPaguParsial, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td class="center">
                                <strong>{{ $kegiatanPaguPerubahan > 0 ? 'Rp ' . number_format($kegiatanPaguPerubahan, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td>&nbsp;</td>
                            <td class="center"><strong>{{ number_format($avgKegiatanTargetFisik, 1) }}%</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanTargetKeuangan > 0 ? 'Rp ' . number_format($kegiatanTargetKeuangan, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td class="center"><strong>{{ number_format($avgKegiatanRealisasiFisik, 1) }}%</strong></td>
                            <!-- Realisasi Keuangan (%) -->
                            <td class="center"><strong>{{ number_format($kegiatanPersentaseKeuangan, 2) }}%</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanRealisasiKeuangan > 0 ? 'Rp ' . number_format($kegiatanRealisasiKeuangan, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                        @foreach ($subKegiatanInKegiatan as $subkegiatan)
                            @php
                                // Get targets and realisasi for this subkegiatan
                                $targetForTask = collect($monitoringTargets)
                                    ->where('task_id', $subkegiatan->id)
                                    ->where('periode_id', $periode->id);
                                $realisasiForTask = collect($monitoringRealisasi)
                                    ->where('task_id', $subkegiatan->id)
                                    ->where('periode_id', $periode->id);

                                // Group by sumber anggaran
                                $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');
                                $realisasiBySumber = $realisasiForTask->groupBy('sumber_anggaran_id');

                                // Get all unique sumber anggaran for this task
                                $allSumberIds = collect($targetsBySumber->keys())
                                    ->merge($realisasiBySumber->keys())
                                    ->unique();

                                // If no sumber anggaran found, still show the subkegiatan row
                                if ($allSumberIds->isEmpty()) {
                                    $allSumberIds = collect([null]); // Show at least one empty row
                                }
                            @endphp

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
                                    $pptk = $targetItem['nama_pptk'] ?? ($realisasiItem['nama_pptk'] ?? '-');
                                    $keterangan = $targetItem['deskripsi'] ?? ($realisasiItem['deskripsi'] ?? '-');
                                    $sumberDana =
                                        $targetItem['sumber_anggaran_nama'] ??
                                        ($realisasiItem['sumber_anggaran_nama'] ?? '-');
                                @endphp

                                <!-- Sub Kegiatan Row -->
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $subkegiatan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                                    <td class="description">{{ $subkegiatan->kodeNomenklatur->nomenklatur ?? '-' }}
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
                                    <td class="description">{{ $keterangan }}</td>
                                    <td class="description">{{ $pptk }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @empty
                <tr>
                    <td colspan="14" style="text-align: center; font-style: italic; padding: 20px;">
                        <strong>Tidak ada data subkegiatan {{ $jenis_laporan }} yang ditemukan</strong><br>
                        <small style="color: #666;">
                            SKPD ID: {{ $tugas->skpd_id ?? 'N/A' }}<br>
                            Periode ID: {{ $periode->id ?? 'N/A' }}<br>
                            Tahun: {{ $tahun }}<br>
                            Total Monitoring: {{ count($monitoring ?? []) }}<br>
                            Total Targets: {{ count($monitoringTargets ?? []) }}<br>
                            Total Realisasi: {{ count($monitoringRealisasi ?? []) }}<br>
                            Pastikan data sudah disimpan di halaman {{ $jenis_laporan }}
                        </small>
                    </td>
                </tr>
            @endforelse

            @if ($totalPagu > 0)
                @php
                    $avgTargetFisik = $itemCount > 0 ? $totalTargetFisik / $itemCount : 0;
                    $avgRealisasiFisik = $itemCount > 0 ? $totalRealisasiFisik / $itemCount : 0;
                @endphp
                @php
                    $totalPaguPokok = 0;
                    $totalPaguParsial = 0;
                    $totalPaguPerubahan = 0;

                    // Recalculate totals by category (using filtered urusan data)
                    $filteredBidangUrusan = $currentUrusan ? collect([$currentUrusan]) : $bidangurusanTugas;

                    foreach ($filteredBidangUrusan as $bidangUrusan) {
                        $programsInBidang = $programTugas->filter(function ($program) use ($bidangUrusan) {
                            return str_starts_with(
                                $program->kodeNomenklatur->nomor_kode ?? '',
                                $bidangUrusan->kodeNomenklatur->nomor_kode ?? '',
                            );
                        });

                        foreach ($programsInBidang as $program) {
                            $kegiatanInProgram = $kegiatanTugas->filter(function ($kegiatan) use ($program) {
                                return str_starts_with(
                                    $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    $program->kodeNomenklatur->nomor_kode ?? '',
                                );
                            });

                            foreach ($kegiatanInProgram as $kegiatan) {
                                $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use (
                                    $kegiatan,
                                ) {
                                    return str_starts_with(
                                        $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                        $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    );
                                });

                                foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                    $targetForTask = collect($monitoringTargets)
                                        ->where('task_id', $subkegiatan->id)
                                        ->where('periode_id', $periode->id);
                                    $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');

                                    foreach ($targetsBySumber as $targets) {
                                        $targetItem = $targets->first();
                                        $totalPaguPokok += $targetItem['pagu_pokok'] ?? 0;
                                        $totalPaguParsial += $targetItem['pagu_parsial'] ?? 0;
                                        $totalPaguPerubahan += $targetItem['pagu_perubahan'] ?? 0;
                                    }
                                }
                            }
                        }
                    }
                @endphp

                <tr class="total-row">
                    <td colspan="3" style="text-align: center;"><strong>TOTAL</strong></td>
                    <td class="center">
                        <strong>{{ $totalPaguPokok > 0 ? 'Rp ' . number_format($totalPaguPokok, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center">
                        <strong>{{ $totalPaguParsial > 0 ? 'Rp ' . number_format($totalPaguParsial, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center">
                        <strong>{{ $totalPaguPerubahan > 0 ? 'Rp ' . number_format($totalPaguPerubahan, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td class="center"><strong>{{ number_format($avgTargetFisik, 1) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $totalTargetKeuangan > 0 ? 'Rp ' . number_format($totalTargetKeuangan, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center"><strong>{{ number_format($avgRealisasiFisik, 1) }}%</strong></td>
                    @php
                        $grandTotalPaguAll = ($totalPaguPokok + $totalPaguParsial + $totalPaguPerubahan);
                        $grandPersentaseRealisasiKeuangan = $grandTotalPaguAll > 0 ? ($totalRealisasiKeuangan / $grandTotalPaguAll) * 100 : 0;
                    @endphp
                    <!-- Realisasi Keuangan (%) Total -->
                    <td class="center"><strong>{{ number_format($grandPersentaseRealisasiKeuangan, 2) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $totalRealisasiKeuangan > 0 ? 'Rp ' . number_format($totalRealisasiKeuangan, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Box removed as requested -->

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            @if (isset($penandatangan_1) && is_array($penandatangan_1))
                <p>{{ $penandatangan_1['tempat'] ?? '' }},
                    {{ isset($penandatangan_1['tanggal']) ? \Carbon\Carbon::parse($penandatangan_1['tanggal'])->format('d F Y') : date('d F Y') }}
                </p>
                <p>{{ $penandatangan_1['jabatan'] ?? '' }}</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $penandatangan_1['nama'] ?? '' }}</p>
                <p>NIP: {{ $penandatangan_1['nip'] ?? '-' }}</p>
            @else
                <p>{{ date('d F Y') }}</p>
                <p>Pejabat yang berwenang</p>
                <div class="signature-space"></div>
                <p class="signature-name">-</p>
                <p>NIP: -</p>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }} | {{ $jenis_laporan }} -
            {{ $skpd->nama_skpd }}</p>
    </div>
</body>

</html>
