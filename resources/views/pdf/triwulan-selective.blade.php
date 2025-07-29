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
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .header {
            display: none;
        }

        .selective-header {
            background-color: #f0f8ff;
            border: 2px solid #4a90e2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .source-filter-info {
            font-size: 11px;
            color: #2c5aa0;
            font-weight: bold;
            margin-bottom: 5px;
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

        .compare-mode-table {
            border: 2px solid #28a745;
        }

        .compare-mode-table th {
            background-color: #e8f5e8;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            text-align: center;
            min-width: 200px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 80px;
        }

        .signature-nip {
            font-size: 10px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Selective Report Header -->
    <div class="selective-header">
        <div class="source-filter-info">
            📊 LAPORAN SELECTIVE - MODE: {{ strtoupper($selectionMode) }}
        </div>
        <div class="source-filter-info">
            🎯 Sumber Dana: {{ implode(', ', $selectedSumberDanaNames) }}
        </div>
        <div class="source-filter-info">
            📈 Total Item: {{ $filteredItemsCount }} anggaran
        </div>
        <div class="source-filter-info">
            💰 Total Pagu: Rp {{ number_format($filteredTotals['totalPagu'], 0, ',', '.') }}
        </div>
    </div>

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
            <div class="info-value">: {{ $periode->nama ?? '-' }}</div>
        </div>
    </div>

    <!-- Data Table -->
    @if($selectionMode === 'compare')
        <!-- Comparative Table -->
        <table class="table compare-mode-table">
            <thead>
                <tr>
                    <th rowspan="2" class="number">No</th>
                    <th rowspan="2">Sumber Dana</th>
                    <th colspan="3">Pagu Anggaran (Rp)</th>
                    <th colspan="2">Target</th>
                    <th colspan="2">Realisasi</th>
                </tr>
                <tr>
                    <th>Pokok</th>
                    <th>Parsial</th>
                    <th>Perubahan</th>
                    <th>Fisik (%)</th>
                    <th>Keuangan (Rp)</th>
                    <th>Fisik (%)</th>
                    <th>Keuangan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($filteredAnggaran as $anggaran)
                    <tr>
                        <td class="number">{{ $no++ }}</td>
                        <td class="description">{{ $anggaran->sumberAnggaran->nama }}</td>
                        <td class="amount">{{ $anggaran->pagu->where('kategori', 1)->sum('dana') > 0 ? 'Rp ' . number_format($anggaran->pagu->where('kategori', 1)->sum('dana'), 0, ',', '.') : '-' }}</td>
                        <td class="amount">{{ $anggaran->pagu->where('kategori', 2)->sum('dana') > 0 ? 'Rp ' . number_format($anggaran->pagu->where('kategori', 2)->sum('dana'), 0, ',', '.') : '-' }}</td>
                        <td class="amount">{{ $anggaran->pagu->where('kategori', 3)->sum('dana') > 0 ? 'Rp ' . number_format($anggaran->pagu->where('kategori', 3)->sum('dana'), 0, ',', '.') : '-' }}</td>
                        <td class="center">{{ $anggaran->realisasi->avg('kinerja_fisik') ?? '-' }}%</td>
                        <td class="amount">{{ $anggaran->realisasi->sum('keuangan') > 0 ? 'Rp ' . number_format($anggaran->realisasi->sum('keuangan'), 0, ',', '.') : '-' }}</td>
                        <td class="center">{{ $anggaran->realisasi->avg('kinerja_fisik') ?? '-' }}%</td>
                        <td class="amount">{{ $anggaran->realisasi->sum('keuangan') > 0 ? 'Rp ' . number_format($anggaran->realisasi->sum('keuangan'), 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <!-- Standard Table -->
        <table class="table">
            <thead>
                <tr>
                    <th rowspan="3" class="number">No</th>
                    <th rowspan="3">Sumber Dana</th>
                    <th colspan="3">Pagu Anggaran APBD</th>
                    <th colspan="2">Target</th>
                    <th colspan="2">Realisasi</th>
                </tr>
                <tr>
                    <th class="amount">Pokok<br>(RP)</th>
                    <th class="amount">Parsial<br>(RP)</th>
                    <th class="amount">Perubahan<br>(RP)</th>
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
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($filteredAnggaran as $anggaran)
                    <tr>
                        <td class="number">{{ $no++ }}</td>
                        <td class="description">{{ $anggaran->sumberAnggaran->nama }}</td>
                        <td class="amount">{{ $anggaran->pagu->where('kategori', 1)->sum('dana') > 0 ? 'Rp ' . number_format($anggaran->pagu->where('kategori', 1)->sum('dana'), 0, ',', '.') : '-' }}</td>
                        <td class="amount">{{ $anggaran->pagu->where('kategori', 2)->sum('dana') > 0 ? 'Rp ' . number_format($anggaran->pagu->where('kategori', 2)->sum('dana'), 0, ',', '.') : '-' }}</td>
                        <td class="amount">{{ $anggaran->pagu->where('kategori', 3)->sum('dana') > 0 ? 'Rp ' . number_format($anggaran->pagu->where('kategori', 3)->sum('dana'), 0, ',', '.') : '-' }}</td>
                        <td class="center">{{ $anggaran->realisasi->avg('kinerja_fisik') ?? '-' }}%</td>
                        <td class="amount">{{ $anggaran->realisasi->sum('keuangan') > 0 ? 'Rp ' . number_format($anggaran->realisasi->sum('keuangan'), 0, ',', '.') : '-' }}</td>
                        <td class="center">{{ $anggaran->realisasi->avg('kinerja_fisik') ?? '-' }}%</td>
                        <td class="amount">{{ $anggaran->realisasi->sum('keuangan') > 0 ? 'Rp ' . number_format($anggaran->realisasi->sum('keuangan'), 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Summary Section -->
    <div class="summary-box">
        <h4>📊 Summary Laporan Selective</h4>
        <div class="summary-content">
            <div class="summary-item">
                <span>Total Sumber Dana:</span>
                <span>{{ count($selectedSumberDanaNames) }} sumber dana</span>
            </div>
            <div class="summary-item">
                <span>Total Pagu Filtered:</span>
                <span>Rp {{ number_format($filteredTotals['totalPagu'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Total Realisasi:</span>
                <span>Rp {{ number_format($filteredTotals['totalRealisasi'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Persentase Capaian:</span>
                <span>{{ $filteredTotals['persentaseCapaian'] }}%</span>
            </div>
            <div class="summary-item">
                <span>Mode Selection:</span>
                <span>{{ ucfirst($selectionMode) }}</span>
            </div>
        </div>
    </div>

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