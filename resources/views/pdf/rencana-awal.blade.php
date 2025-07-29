<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Rencana Awal - {{ $skpd->nama_skpd ?? 'SKPD tidak ditemukan' }}</title>
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
            font-family: 'Times New Roman', Times, serif;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
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
            font-size: 6px;
        }

        .table .amount {
            text-align: center;
            width: 70px;
            min-width: 70px;
            font-size: 6px;
        }

        .table .percent {
            text-align: center;
            width: 35px;
            min-width: 35px;
            font-size: 6px;
        }

        .table .description {
            width: auto;

            @if ($orientation === 'landscape')
                max-width: 200px;
            @else
                max-width: 120px;
            @endif
            word-wrap: break-word;
            font-size: 6px;
        }

        @if ($orientation === 'landscape')
            /* Landscape specific styles */
            .table {
                font-size: 8px !important;
            }

            .table .amount {
                width: 90px;
                min-width: 90px;
                font-size: 7px;
            }

            .table .percent {
                width: 45px;
                min-width: 45px;
                font-size: 7px;
            }

            .table .code {
                width: 80px;
                min-width: 80px;
                font-size: 7px;
            }

            .table .description {
                max-width: 300px;
                font-size: 7px;
            }

            .table th {
                font-size: 7px;
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
            text-align: right;
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
    </style>
</head>

<body>
    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama SKPD</div>
            <div class="info-value">: {{ $skpd->nama_skpd ?? 'SKPD tidak ditemukan' }}</div>
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
                <th colspan="2">Triwulan 1</th>
                <th colspan="2">Triwulan 2</th>
                <th colspan="2">Triwulan 3</th>
                <th colspan="2">Triwulan 4</th>
            </tr>
            <tr>
                <th class="amount">Pokok<br>(RP)</th>
                <th class="amount">Parsial<br>(RP)</th>
                <th class="amount">Perubahan<br>(RP)</th>
                <th>Kinerja<br>Fisik (%)</th>
                <th>Keuangan<br>(RP)</th>
                <th>Kinerja<br>Fisik (%)</th>
                <th>Keuangan<br>(RP)</th>
                <th>Kinerja<br>Fisik (%)</th>
                <th>Keuangan<br>(RP)</th>
                <th>Kinerja<br>Fisik (%)</th>
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
                <th>9</th>
                <th>10</th>
                <th>11</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $totalPagu = 0;
            @endphp

            @forelse($bidangurusanTugas as $bidangUrusan)
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
                    $urusanTarget1 = 0;
                    $urusanTarget2 = 0;
                    $urusanTarget3 = 0;
                    $urusanTarget4 = 0;

                    // For calculating average kinerja fisik
                    $urusanKinerjaFisik1 = 0;
                    $urusanKinerjaFisik2 = 0;
                    $urusanKinerjaFisik3 = 0;
                    $urusanKinerjaFisik4 = 0;
                    $countSubKegiatan = 0;

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
                                $targetForTask = collect($monitoringTargets)->where('task_id', $subkegiatan->id);
                                $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');

                                foreach ($targetsBySumber as $targetsForSumber) {
                                    $firstTarget = $targetsForSumber->first();
                                    $urusanPaguPokok += $firstTarget['pagu_pokok'] ?? 0;
                                    $urusanPaguParsial += $firstTarget['pagu_parsial'] ?? 0;
                                    $urusanPaguPerubahan += $firstTarget['pagu_perubahan'] ?? 0;

                                    $targetsByPeriode = $targetsForSumber->keyBy('periode_id');
                                    $urusanTarget1 += $targetsByPeriode->get(2)['keuangan'] ?? 0;
                                    $urusanTarget2 += $targetsByPeriode->get(3)['keuangan'] ?? 0;
                                    $urusanTarget3 += $targetsByPeriode->get(4)['keuangan'] ?? 0;
                                    $urusanTarget4 += $targetsByPeriode->get(5)['keuangan'] ?? 0;

                                    // Calculate kinerja fisik for averaging
                                    $urusanKinerjaFisik1 += $targetsByPeriode->get(2)['kinerja_fisik'] ?? 0;
                                    $urusanKinerjaFisik2 += $targetsByPeriode->get(3)['kinerja_fisik'] ?? 0;
                                    $urusanKinerjaFisik3 += $targetsByPeriode->get(4)['kinerja_fisik'] ?? 0;
                                    $urusanKinerjaFisik4 += $targetsByPeriode->get(5)['kinerja_fisik'] ?? 0;
                                    $countSubKegiatan++;
                                }
                            }
                        }
                    }

                    // Calculate average kinerja fisik for urusan
                    $avgKinerjaFisik1 = $countSubKegiatan > 0 ? $urusanKinerjaFisik1 / $countSubKegiatan : 0;
                    $avgKinerjaFisik2 = $countSubKegiatan > 0 ? $urusanKinerjaFisik2 / $countSubKegiatan : 0;
                    $avgKinerjaFisik3 = $countSubKegiatan > 0 ? $urusanKinerjaFisik3 / $countSubKegiatan : 0;
                    $avgKinerjaFisik4 = $countSubKegiatan > 0 ? $urusanKinerjaFisik4 / $countSubKegiatan : 0;
                @endphp

                <!-- Urusan Row dengan akumulasi -->
                <tr style="background-color: #e8f4fd; font-weight: bold;">
                    <td>{{ $no++ }}</td>
                    <td>{{ $bidangUrusan->kodeNomenklatur->nomor_kode ?? '-' }}</td>
                    <td class="description"><strong>URUSAN:
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
                    <td class="center"><strong>{{ number_format($avgKinerjaFisik1, 1) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $urusanTarget1 > 0 ? 'Rp ' . number_format($urusanTarget1, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center"><strong>{{ number_format($avgKinerjaFisik2, 1) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $urusanTarget2 > 0 ? 'Rp ' . number_format($urusanTarget2, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center"><strong>{{ number_format($avgKinerjaFisik3, 1) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $urusanTarget3 > 0 ? 'Rp ' . number_format($urusanTarget3, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td class="center"><strong>{{ number_format($avgKinerjaFisik4, 1) }}%</strong></td>
                    <td class="center">
                        <strong>{{ $urusanTarget4 > 0 ? 'Rp ' . number_format($urusanTarget4, 0, ',', '.') : '-' }}</strong>
                    </td>
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

                        // Calculate total for program (akumulasi dari sub kegiatan)
                        $programPaguPokok = 0;
                        $programPaguParsial = 0;
                        $programPaguPerubahan = 0;
                        $programTarget1 = 0;
                        $programTarget2 = 0;
                        $programTarget3 = 0;
                        $programTarget4 = 0;

                        // For calculating average kinerja fisik
                        $programKinerjaFisik1 = 0;
                        $programKinerjaFisik2 = 0;
                        $programKinerjaFisik3 = 0;
                        $programKinerjaFisik4 = 0;
                        $countProgramSubKegiatan = 0;

                        foreach ($kegiatanInProgram as $kegiatan) {
                            $subKegiatanInKegiatan = $subkegiatanTugas->filter(function ($subkegiatan) use ($kegiatan) {
                                return str_starts_with(
                                    $subkegiatan->kodeNomenklatur->nomor_kode ?? '',
                                    $kegiatan->kodeNomenklatur->nomor_kode ?? '',
                                );
                            });

                            foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                $targetForTask = collect($monitoringTargets)->where('task_id', $subkegiatan->id);
                                $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');

                                foreach ($targetsBySumber as $targetsForSumber) {
                                    $firstTarget = $targetsForSumber->first();
                                    $programPaguPokok += $firstTarget['pagu_pokok'] ?? 0;
                                    $programPaguParsial += $firstTarget['pagu_parsial'] ?? 0;
                                    $programPaguPerubahan += $firstTarget['pagu_perubahan'] ?? 0;

                                    $targetsByPeriode = $targetsForSumber->keyBy('periode_id');
                                    $programTarget1 += $targetsByPeriode->get(2)['keuangan'] ?? 0;
                                    $programTarget2 += $targetsByPeriode->get(3)['keuangan'] ?? 0;
                                    $programTarget3 += $targetsByPeriode->get(4)['keuangan'] ?? 0;
                                    $programTarget4 += $targetsByPeriode->get(5)['keuangan'] ?? 0;

                                    // Calculate kinerja fisik for averaging
                                    $programKinerjaFisik1 += $targetsByPeriode->get(2)['kinerja_fisik'] ?? 0;
                                    $programKinerjaFisik2 += $targetsByPeriode->get(3)['kinerja_fisik'] ?? 0;
                                    $programKinerjaFisik3 += $targetsByPeriode->get(4)['kinerja_fisik'] ?? 0;
                                    $programKinerjaFisik4 += $targetsByPeriode->get(5)['kinerja_fisik'] ?? 0;
                                    $countProgramSubKegiatan++;
                                }
                            }
                        }

                        // Calculate average kinerja fisik for program
                        $programAvgKinerjaFisik1 =
                            $countProgramSubKegiatan > 0 ? $programKinerjaFisik1 / $countProgramSubKegiatan : 0;
                        $programAvgKinerjaFisik2 =
                            $countProgramSubKegiatan > 0 ? $programKinerjaFisik2 / $countProgramSubKegiatan : 0;
                        $programAvgKinerjaFisik3 =
                            $countProgramSubKegiatan > 0 ? $programKinerjaFisik3 / $countProgramSubKegiatan : 0;
                        $programAvgKinerjaFisik4 =
                            $countProgramSubKegiatan > 0 ? $programKinerjaFisik4 / $countProgramSubKegiatan : 0;
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
                        <td class="center"><strong>{{ number_format($programAvgKinerjaFisik1, 1) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $programTarget1 > 0 ? 'Rp ' . number_format($programTarget1, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center"><strong>{{ number_format($programAvgKinerjaFisik2, 1) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $programTarget2 > 0 ? 'Rp ' . number_format($programTarget2, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center"><strong>{{ number_format($programAvgKinerjaFisik3, 1) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $programTarget3 > 0 ? 'Rp ' . number_format($programTarget3, 0, ',', '.') : '-' }}</strong>
                        </td>
                        <td class="center"><strong>{{ number_format($programAvgKinerjaFisik4, 1) }}%</strong></td>
                        <td class="center">
                            <strong>{{ $programTarget4 > 0 ? 'Rp ' . number_format($programTarget4, 0, ',', '.') : '-' }}</strong>
                        </td>
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

                            // Calculate total for kegiatan (akumulasi dari sub kegiatan)
                            $kegiatanPaguPokok = 0;
                            $kegiatanPaguParsial = 0;
                            $kegiatanPaguPerubahan = 0;
                            $kegiatanTarget1 = 0;
                            $kegiatanTarget2 = 0;
                            $kegiatanTarget3 = 0;
                            $kegiatanTarget4 = 0;

                            // For calculating average kinerja fisik
                            $kegiatanKinerjaFisik1 = 0;
                            $kegiatanKinerjaFisik2 = 0;
                            $kegiatanKinerjaFisik3 = 0;
                            $kegiatanKinerjaFisik4 = 0;
                            $countKegiatanSubKegiatan = 0;

                            foreach ($subKegiatanInKegiatan as $subkegiatan) {
                                $targetForTask = collect($monitoringTargets)->where('task_id', $subkegiatan->id);
                                $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');

                                foreach ($targetsBySumber as $targetsForSumber) {
                                    $firstTarget = $targetsForSumber->first();
                                    $kegiatanPaguPokok += $firstTarget['pagu_pokok'] ?? 0;
                                    $kegiatanPaguParsial += $firstTarget['pagu_parsial'] ?? 0;
                                    $kegiatanPaguPerubahan += $firstTarget['pagu_perubahan'] ?? 0;

                                    $targetsByPeriode = $targetsForSumber->keyBy('periode_id');
                                    $kegiatanTarget1 += $targetsByPeriode->get(2)['keuangan'] ?? 0;
                                    $kegiatanTarget2 += $targetsByPeriode->get(3)['keuangan'] ?? 0;
                                    $kegiatanTarget3 += $targetsByPeriode->get(4)['keuangan'] ?? 0;
                                    $kegiatanTarget4 += $targetsByPeriode->get(5)['keuangan'] ?? 0;

                                    // Calculate kinerja fisik for averaging
                                    $kegiatanKinerjaFisik1 += $targetsByPeriode->get(2)['kinerja_fisik'] ?? 0;
                                    $kegiatanKinerjaFisik2 += $targetsByPeriode->get(3)['kinerja_fisik'] ?? 0;
                                    $kegiatanKinerjaFisik3 += $targetsByPeriode->get(4)['kinerja_fisik'] ?? 0;
                                    $kegiatanKinerjaFisik4 += $targetsByPeriode->get(5)['kinerja_fisik'] ?? 0;
                                    $countKegiatanSubKegiatan++;
                                }
                            }

                            // Calculate average kinerja fisik for kegiatan
                            $kegiatanAvgKinerjaFisik1 =
                                $countKegiatanSubKegiatan > 0 ? $kegiatanKinerjaFisik1 / $countKegiatanSubKegiatan : 0;
                            $kegiatanAvgKinerjaFisik2 =
                                $countKegiatanSubKegiatan > 0 ? $kegiatanKinerjaFisik2 / $countKegiatanSubKegiatan : 0;
                            $kegiatanAvgKinerjaFisik3 =
                                $countKegiatanSubKegiatan > 0 ? $kegiatanKinerjaFisik3 / $countKegiatanSubKegiatan : 0;
                            $kegiatanAvgKinerjaFisik4 =
                                $countKegiatanSubKegiatan > 0 ? $kegiatanKinerjaFisik4 / $countKegiatanSubKegiatan : 0;
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
                            <td class="center"><strong>{{ number_format($kegiatanAvgKinerjaFisik1, 1) }}%</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanTarget1 > 0 ? 'Rp ' . number_format($kegiatanTarget1, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td class="center"><strong>{{ number_format($kegiatanAvgKinerjaFisik2, 1) }}%</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanTarget2 > 0 ? 'Rp ' . number_format($kegiatanTarget2, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td class="center"><strong>{{ number_format($kegiatanAvgKinerjaFisik3, 1) }}%</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanTarget3 > 0 ? 'Rp ' . number_format($kegiatanTarget3, 0, ',', '.') : '-' }}</strong>
                            </td>
                            <td class="center"><strong>{{ number_format($kegiatanAvgKinerjaFisik4, 1) }}%</strong></td>
                            <td class="center">
                                <strong>{{ $kegiatanTarget4 > 0 ? 'Rp ' . number_format($kegiatanTarget4, 0, ',', '.') : '-' }}</strong>
                            </td>
                        </tr>

                        @foreach ($subKegiatanInKegiatan as $subkegiatan)
                            @php
                                // Get targets for this subkegiatan for all periods
                                $targetForTask = collect($monitoringTargets)->where('task_id', $subkegiatan->id);

                                // Group by sumber anggaran
                                $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');

                                // Get all unique sumber anggaran for this task
                                $allSumberIds = $targetsBySumber->keys()->filter();

                                // If no sumber anggaran found, still show the subkegiatan row
                                if ($allSumberIds->isEmpty()) {
                                    $allSumberIds = collect([null]); // Show at least one empty row
                                }
                            @endphp

                            @foreach ($allSumberIds as $sumberAnggaranId)
                                @php
                                    $targetsForSumber = $targetsBySumber->get($sumberAnggaranId, collect());

                                    // Group targets by periode_id
                                    $targetsByPeriode = $targetsForSumber->keyBy('periode_id');

                                    // Get targets for each triwulan (periode_id 2,3,4,5 = TW1,2,3,4)
                                    $target1 = $targetsByPeriode->get(2); // Triwulan 1
                                    $target2 = $targetsByPeriode->get(3); // Triwulan 2
                                    $target3 = $targetsByPeriode->get(4); // Triwulan 3
                                    $target4 = $targetsByPeriode->get(5); // Triwulan 4

                                    // Get pagu data (use any target as they all have same pagu values)
                                    $firstTarget = $targetsForSumber->first();
                                    $paguPokok = $firstTarget['pagu_pokok'] ?? 0;
                                    $paguParsial = $firstTarget['pagu_parsial'] ?? 0;
                                    $paguPerubahan = $firstTarget['pagu_perubahan'] ?? 0;

                                    $totalPagu += $paguPokok + $paguParsial + $paguPerubahan;

                                    $sumberDana = $firstTarget['sumber_anggaran_nama'] ?? '-';
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
                                    <td class="center">
                                        {{ $target1 ? number_format($target1['kinerja_fisik'], 1) : '0.0' }}%</td>
                                    <td class="center">
                                        {{ ($target1['keuangan'] ?? 0) > 0 ? 'Rp ' . number_format($target1['keuangan'] ?? 0, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="center">
                                        {{ $target2 ? number_format($target2['kinerja_fisik'], 1) : '0.0' }}%</td>
                                    <td class="center">
                                        {{ ($target2['keuangan'] ?? 0) > 0 ? 'Rp ' . number_format($target2['keuangan'] ?? 0, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="center">
                                        {{ $target3 ? number_format($target3['kinerja_fisik'], 1) : '0.0' }}%</td>
                                    <td class="center">
                                        {{ ($target3['keuangan'] ?? 0) > 0 ? 'Rp ' . number_format($target3['keuangan'] ?? 0, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="center">
                                        {{ $target4 ? number_format($target4['kinerja_fisik'], 1) : '0.0' }}%</td>
                                    <td class="center">
                                        {{ ($target4['keuangan'] ?? 0) > 0 ? 'Rp ' . number_format($target4['keuangan'] ?? 0, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            @empty
                <tr>
                    <td colspan="15" style="text-align: center; font-style: italic; padding: 20px;">
                        <strong>Tidak ada data rencana awal yang ditemukan</strong><br>
                        <small style="color: #666;">
                            SKPD ID: {{ $tugas->skpd_id ?? 'N/A' }}<br>
                            Tahun: {{ $tahun }}<br>
                            Total Monitoring: {{ count($monitoring ?? []) }}<br>
                            Total Targets: {{ count($monitoringTargets ?? []) }}<br>
                            Pastikan data sudah disimpan di halaman Rencana Awal
                        </small>
                    </td>
                </tr>
            @endforelse

            @if ($totalPagu > 0)
                @php
                    $totalPaguPokok = 0;
                    $totalPaguParsial = 0;
                    $totalPaguPerubahan = 0;
                    $totalTarget1 = 0;
                    $totalTarget2 = 0;
                    $totalTarget3 = 0;
                    $totalTarget4 = 0;

                    // Recalculate totals by category from all subkegiatan
                    foreach ($subkegiatanTugas as $subkegiatan) {
                        $targetForTask = collect($monitoringTargets)->where('task_id', $subkegiatan->id);
                        $targetsBySumber = $targetForTask->groupBy('sumber_anggaran_id');

                        foreach ($targetsBySumber as $targetsForSumber) {
                            $firstTarget = $targetsForSumber->first();
                            $totalPaguPokok += $firstTarget['pagu_pokok'] ?? 0;
                            $totalPaguParsial += $firstTarget['pagu_parsial'] ?? 0;
                            $totalPaguPerubahan += $firstTarget['pagu_perubahan'] ?? 0;

                            // Sum targets by periode
                            $targetsByPeriode = $targetsForSumber->keyBy('periode_id');
                            $totalTarget1 += $targetsByPeriode->get(2)['keuangan'] ?? 0;
                            $totalTarget2 += $targetsByPeriode->get(3)['keuangan'] ?? 0;
                            $totalTarget3 += $targetsByPeriode->get(4)['keuangan'] ?? 0;
                            $totalTarget4 += $targetsByPeriode->get(5)['keuangan'] ?? 0;
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
                    <td>&nbsp;</td>
                    <td class="center">
                        <strong>{{ $totalTarget1 > 0 ? 'Rp ' . number_format($totalTarget1, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td class="center">
                        <strong>{{ $totalTarget2 > 0 ? 'Rp ' . number_format($totalTarget2, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td class="center">
                        <strong>{{ $totalTarget3 > 0 ? 'Rp ' . number_format($totalTarget3, 0, ',', '.') : '-' }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td class="center">
                        <strong>{{ $totalTarget4 > 0 ? 'Rp ' . number_format($totalTarget4, 0, ',', '.') : '-' }}</strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

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
                <p class="signature-name">.........................</p>
                <p>NIP: .........................</p>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }} |
            {{ $jenis_laporan ?? 'Laporan Rencana Awal' }} -
            {{ $skpd->nama_skpd ?? 'SKPD tidak ditemukan' }}</p>
    </div>
</body>

</html>
