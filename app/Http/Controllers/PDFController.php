<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringTarget;
use App\Models\MonitoringRealisasi;
use App\Models\SkpdTugas;
use App\Models\SumberAnggaran;
use App\Models\KodeNomenklatur;
use App\Models\User;
use App\Models\Periode;
use App\Models\PeriodeTahun;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    /**
     * Set memory and time limits for PDF generation
     */
    private function setPdfLimits()
    {
        // Increase memory limit for PDF generation
        $memoryLimit = config('dompdf.memory_limit', '512M');
        $timeLimit = config('dompdf.time_limit', 300);
        
        ini_set('memory_limit', $memoryLimit);
        set_time_limit($timeLimit);
        
        \Log::info('PDF generation limits set', [
            'memory_limit' => $memoryLimit,
            'time_limit' => $timeLimit
        ]);
    }

    /**
     * Show PDF configuration form for Rencana Awal
     */
    public function showRencanaAwalPdfForm($tugasId)
    {
        $skpdTugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.kepalaAktif.user.userDetail',
            'skpd.timKerja.operator.userDetail'
        ])->findOrFail($tugasId);

        // Ambil tahun dari monitoring
        $monitoringTahun = Monitoring::where('skpd_tugas_id', $tugasId)
            ->select('tahun')
            ->distinct()
            ->get()
            ->pluck('tahun');

        // Ambil semua periode tahun yang ada di sistem
        $periodeTahun = \App\Models\PeriodeTahun::orderBy('tahun', 'desc')
            ->get()
            ->pluck('tahun');

        // Gabungkan tahun dari monitoring dan periode tahun yang aktif
        $availableTahun = $monitoringTahun->concat($periodeTahun)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        \Log::info('Available Tahun Debug', [
            'monitoring_tahun' => $monitoringTahun,
            'periode_tahun' => $periodeTahun,
            'combined_tahun' => $availableTahun
        ]);

        // Debug: Check if kodeNomenklatur is loaded
        if (!$skpdTugas->kodeNomenklatur) {
            \Log::warning("KodeNomenklatur not found for SkpdTugas ID: {$tugasId}. kode_nomenklatur_id: {$skpdTugas->kode_nomenklatur_id}");
        }

        // Manually add tim_kerja_aktif like in TriwulanController
        if ($skpdTugas->skpd && $skpdTugas->skpd->timKerja) {
            $skpdTugas->skpd->tim_kerja_aktif = $skpdTugas->skpd->timKerja->first();
        }

        return Inertia::render('PDF/RencanaAwalPdfForm', [
            'tugas' => $skpdTugas,
            'skpd' => $skpdTugas->skpd,
            'availableTahun' => $availableTahun, // Kirim daftar tahun yang tersedia
        ]);
    }

    /**
     * Show PDF configuration form for Triwulan
     */
    public function showTriwulanPdfForm($tid, $tugasId)
    {
        $skpdTugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.kepalaAktif.user.userDetail',
            'skpd.timKerja.operator.userDetail'
        ])->findOrFail($tugasId);

        // Debug: Check if kodeNomenklatur is loaded
        if (!$skpdTugas->kodeNomenklatur) {
            \Log::warning("KodeNomenklatur not found for SkpdTugas ID: {$tugasId}. kode_nomenklatur_id: {$skpdTugas->kode_nomenklatur_id}");
        }

        // Manually add tim_kerja_aktif like in TriwulanController
        if ($skpdTugas->skpd && $skpdTugas->skpd->timKerja) {
            $skpdTugas->skpd->tim_kerja_aktif = $skpdTugas->skpd->timKerja->first();
        }

        $triwulanName = $this->getTriwulanName($tid);

        // Get active year from PeriodeTahun
        $tahunAktif = \App\Models\PeriodeTahun::getTahunAktif();
        $currentYear = $tahunAktif ? $tahunAktif->tahun : date('Y');

        // Ambil tahun dari monitoring triwulan
        $monitoringTahun = Monitoring::where('skpd_tugas_id', $tugasId)
            ->whereHas('periode.tahap', function($query) use ($triwulanName) {
                $query->where('tahap', $triwulanName);
            })
            ->select('tahun')
            ->distinct()
            ->get()
            ->pluck('tahun');

        // Ambil semua periode tahun yang ada di sistem
        $periodeTahun = \App\Models\PeriodeTahun::orderBy('tahun', 'desc')
            ->get()
            ->pluck('tahun');

        // Gabungkan tahun dari monitoring dan periode tahun yang aktif
        $availableTahun = collect([$currentYear]) // Start with active year
            ->concat($monitoringTahun)
            ->concat($periodeTahun)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        \Log::info('Triwulan Available Tahun Debug', [
            'monitoring_tahun' => $monitoringTahun,
            'periode_tahun' => $periodeTahun,
            'combined_tahun' => $availableTahun
        ]);

        // Get monitoring targets for sumber dana filter
        $allMonitoring = Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                $query->where('skpd_id', $skpdTugas->skpd_id);
            })
            ->where('tahun', $currentYear)
            ->with(['anggaran.target.periode', 'anggaran.realisasi.periode', 'anggaran.sumberAnggaran', 'anggaran.pagu'])
            ->get();

        // Extract unique sumber dana for filter
        $sumberDanaOptions = [];
        foreach ($allMonitoring as $monitoringItem) {
            foreach ($monitoringItem->anggaran as $anggaran) {
                if ($anggaran->sumberAnggaran) {
                    $sumberDanaOptions[$anggaran->sumber_anggaran_id] = [
                        'id' => $anggaran->sumber_anggaran_id,
                        'nama' => $anggaran->sumberAnggaran->nama,
                        'count' => ($sumberDanaOptions[$anggaran->sumber_anggaran_id]['count'] ?? 0) + 1
                    ];
                }
            }
        }

        $sumberDanaOptions = array_values($sumberDanaOptions);

        return Inertia::render('PDF/TriwulanPdfForm', [
            'tugas' => $skpdTugas,
            'skpd' => $skpdTugas->skpd,
            'tid' => $tid,
            'triwulanName' => $triwulanName,
            'availableTahun' => $availableTahun,
            'sumberDanaOptions' => $sumberDanaOptions, // Add sumber dana options for filter
        ]);
    }

    /**
     * Generate PDF for Rencana Awal
     */
    public function generateRencanaAwalPdf(Request $request, $tugasId)
    {
        try {
            // Set memory and time limits for PDF generation
            $this->setPdfLimits();
            
            // Handle both JSON and form data
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $validated = validator($data, [
                'tahun' => 'required|integer|min:2020|max:2030',
                'penandatangan_1_tempat' => 'required|string|max:100',
                'penandatangan_1_tanggal' => 'required|date',
                'penandatangan_1_nama' => 'required|string|max:255',
                'penandatangan_1_jabatan' => 'required|string|max:255',
                'penandatangan_1_nip' => 'required|string|max:50',
                'paper_size' => 'required|in:A4,A3,Letter',
                'orientation' => 'required|in:portrait,landscape',
                'margin_top' => 'required|numeric|min:0|max:50',
                'margin_right' => 'required|numeric|min:0|max:50',
                'margin_bottom' => 'required|numeric|min:0|max:50',
                'margin_left' => 'required|numeric|min:0|max:50',
            ])->validate();

            // Check if SKPD Tugas exists
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'skpd.kepalaAktif.user.userDetail',
                'skpd.timKerja.operator.userDetail'
            ])->find($tugasId);

            if (!$skpdTugas) {
                \Log::error('PDF Generation Error: SKPD Tugas not found', ['tugasId' => $tugasId]);
                return response()->json(['error' => 'Data SKPD tidak ditemukan'], 404);
            }

            if (!$skpdTugas->skpd) {
                \Log::error('PDF Generation Error: SKPD not found', ['tugasId' => $tugasId]);
                return response()->json(['error' => 'Data SKPD tidak ditemukan'], 404);
            }

            // Validate kodeNomenklatur exists
            if (!$skpdTugas->kodeNomenklatur) {
                \Log::error('PDF Generation Error: KodeNomenklatur not found', [
                    'tugasId' => $tugasId,
                    'kode_nomenklatur_id' => $skpdTugas->kode_nomenklatur_id
                ]);
                return response()->json(['error' => 'Data kode nomenklatur tidak ditemukan'], 404);
            }

            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'skpd.kepalaAktif.user.userDetail',
                'skpd.timKerja.operator.userDetail'
            ])->findOrFail($tugasId);

            // Get monitoring data for Rencana Awal with hierarchical structure
            $tahun = $validated['tahun'];

            // SIMPLIFIED APPROACH: Get all data first, filter later if needed
            $allSkpdTugas = SkpdTugas::where('skpd_id', $skpdTugas->skpd_id)
                ->with(['kodeNomenklatur.details'])
                ->get();

            // Group by hierarchical structure
            $bidangurusanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 2;
            })->values();

            $programTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 3;
            })->values();

            $kegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 4;
            })->values();

            // Try different levels for sub kegiatan
            $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 5;
            })->values();

            // If no level 5, try level 4
            if ($subkegiatanTugas->isEmpty()) {
                $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 4;
                })->values();
            }

            // Get ALL monitoring data for this SKPD first
            $allMonitoring = Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                    $query->where('skpd_id', $skpdTugas->skpd_id);
                })
                ->where('tahun', $tahun)
                ->where(function($query) {
                    $query->where('deskripsi', 'Rencana Awal')
                          ->orWhere('deskripsi', '');  // Also include empty description
                })
                ->with(['anggaran.target.periode', 'anggaran.realisasi.periode', 'anggaran.sumberAnggaran', 'anggaran.pagu'])
                ->get();

            // ALWAYS determine and filter by urusan - regardless of what level is opened
            $currentUrusan = null;
            $urusanCode = null;

            // Determine urusan from any level of task
            $currentCode = $skpdTugas->kodeNomenklatur->nomor_kode ?? '';

            if ($skpdTugas->kodeNomenklatur->jenis_nomenklatur == 2) {
                // Current task is urusan itself
                $currentUrusan = $skpdTugas;
                $urusanCode = $currentCode;
            } else {
                // SPECIAL CASE: If current task is very high level (jenis_nomenklatur = 0 or 1)
                // and has short code like "1", try to find urusan by prefix matching
                if ($skpdTugas->kodeNomenklatur->jenis_nomenklatur <= 1 && strlen($currentCode) <= 2) {
                    \Log::info("RENCANA AWAL URUSAN DETERMINATION - High level task detected, trying prefix matching");

                    // Try to find urusan that starts with current code
                    $currentUrusan = $bidangurusanTugas->first(function($item) use ($currentCode) {
                        $itemCode = $item->kodeNomenklatur->nomor_kode ?? '';
                        $isMatch = str_starts_with($itemCode, $currentCode . '.');

                        \Log::info("RENCANA AWAL URUSAN DETERMINATION - Prefix matching", [
                            'item_code' => $itemCode,
                            'current_code' => $currentCode,
                            'prefix_check' => $currentCode . '.',
                            'is_match' => $isMatch
                        ]);

                        return $isMatch;
                    });

                    if ($currentUrusan) {
                        $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                        \Log::info("RENCANA AWAL URUSAN DETERMINATION - Found urusan by prefix!", [
                            'urusan_code' => $urusanCode,
                            'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A'
                        ]);
                    }
                }

                // If no urusan found by prefix, try normal pattern matching
                if (!$currentUrusan) {
                    // Find parent urusan from current task's code
                    // Try different urusan code patterns
                    $possibleUrusanCodes = [];

                    if (strlen($currentCode) >= 4) {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 4); // X.XX format
                    }
                    if (strlen($currentCode) >= 3) {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 3); // X.X format
                    }
                    if (strlen($currentCode) >= 5 && substr($currentCode, 4, 1) === '.') {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 5); // X.XX. format
                    }

                    // Search for urusan with any of these codes
                    foreach ($possibleUrusanCodes as $testCode) {
                        $currentUrusan = $allSkpdTugas->first(function($item) use ($testCode, $currentCode) {
                            return $item->kodeNomenklatur->jenis_nomenklatur == 2 &&
                                   ($item->kodeNomenklatur->nomor_kode === $testCode ||
                                    str_starts_with($currentCode, $item->kodeNomenklatur->nomor_kode ?? ''));
                        });

                        if ($currentUrusan) {
                            $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                            break;
                        }
                    }
                }

                // FALLBACK: If we still can't find specific urusan, default to first available urusan
                if (!$currentUrusan && $bidangurusanTugas->isNotEmpty()) {
                    $currentUrusan = $bidangurusanTugas->first();
                    $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                    \Log::info("RENCANA AWAL URUSAN DETERMINATION - Using first available urusan as fallback", [
                        'urusan_code' => $urusanCode,
                        'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ]);
                }
            }

            // Apply urusan filtering if urusan is found
            if ($currentUrusan && $urusanCode) {
                \Log::info("Filtering by urusan", [
                    'urusan_code' => $urusanCode,
                    'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A',
                    'original_code' => $currentCode
                ]);

                // Filter data by urusan
                $bidangurusanTugas = collect([$currentUrusan]);

                $programTugas = $allSkpdTugas->filter(function($item) use ($urusanCode) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 3 &&
                           str_starts_with($item->kodeNomenklatur->nomor_kode ?? '', $urusanCode);
                })->values();

                $kegiatanTugas = $allSkpdTugas->filter(function($item) use ($urusanCode) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 4 &&
                           str_starts_with($item->kodeNomenklatur->nomor_kode ?? '', $urusanCode);
                })->values();

                $subkegiatanTugas = $allSkpdTugas->filter(function($item) use ($urusanCode) {
                    return ($item->kodeNomenklatur->jenis_nomenklatur == 5 || $item->kodeNomenklatur->jenis_nomenklatur == 4) &&
                           str_starts_with($item->kodeNomenklatur->nomor_kode ?? '', $urusanCode);
                })->values();

                // Filter monitoring by urusan
                $allMonitoring = $allMonitoring->filter(function($monitoring) use ($urusanCode) {
                    $monitoringCode = $monitoring->tugas->kodeNomenklatur->nomor_kode ?? '';
                    return str_starts_with($monitoringCode, $urusanCode);
                });
            } else {
                \Log::warning("Could not determine urusan for filtering", [
                    'task_code' => $currentCode,
                    'task_jenis' => $skpdTugas->kodeNomenklatur->jenis_nomenklatur ?? 'N/A'
                ]);
            }

            // Collect monitoring targets and realisasi with UNIQUE filtering to avoid duplicates
            $monitoringTargetsRaw = [];
            $monitoringRealisasiRaw = [];

            foreach ($allMonitoring as $monitoringItem) {
                foreach ($monitoringItem->anggaran as $anggaran) {
                    // Get pagu data for all categories (pokok, parsial, perubahan) with proper aggregation
                    $paguData = [
                        'pokok' => 0,
                        'parsial' => 0,
                        'perubahan' => 0
                    ];

                    // Aggregate pagu data from all records
                    foreach ($anggaran->pagu as $pagu) {
                        switch ($pagu->kategori) {
                            case 1:
                                $paguData['pokok'] += $pagu->dana;
                                break;
                            case 2:
                                $paguData['parsial'] += $pagu->dana;
                                break;
                            case 3:
                                $paguData['perubahan'] += $pagu->dana;
                                break;
                        }
                    }

                    // Collect monitoring targets with unique key to prevent duplicates
                    foreach ($anggaran->target as $target) {
                        $uniqueKey = $monitoringItem->skpd_tugas_id . '_' . $anggaran->sumber_anggaran_id . '_' . $target->periode_id;

                        $monitoringTargetsRaw[$uniqueKey] = [
                            'id' => $target->id,
                            'task_id' => $monitoringItem->skpd_tugas_id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan,
                            'pagu_pokok' => $paguData['pokok'],
                            'pagu_parsial' => $paguData['parsial'],
                            'pagu_perubahan' => $paguData['perubahan'],
                            'periode' => $target->periode ? $target->periode->nama : 'Unknown',
                            'periode_id' => $target->periode_id,
                            'monitoring_id' => $monitoringItem->id,
                            'monitoring_anggaran_id' => $anggaran->id,
                            'deskripsi' => $monitoringItem->deskripsi,
                            'nama_pptk' => $monitoringItem->nama_pptk,
                            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                            'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                        ];
                    }

                    // Collect monitoring realisasi with unique key to prevent duplicates
                    foreach ($anggaran->realisasi as $realisasi) {
                        $uniqueKey = $monitoringItem->skpd_tugas_id . '_' . $anggaran->sumber_anggaran_id . '_' . $realisasi->periode_id;

                        $monitoringRealisasiRaw[$uniqueKey] = [
                            'id' => $realisasi->id,
                            'task_id' => $monitoringItem->skpd_tugas_id,
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'pagu_pokok' => $paguData['pokok'],
                            'pagu_parsial' => $paguData['parsial'],
                            'pagu_perubahan' => $paguData['perubahan'],
                            'periode' => $realisasi->periode ? $realisasi->periode->nama : 'Unknown',
                            'periode_id' => $realisasi->periode_id,
                            'monitoring_id' => $monitoringItem->id,
                            'monitoring_anggaran_id' => $anggaran->id,
                            'deskripsi' => $monitoringItem->deskripsi,
                            'nama_pptk' => $monitoringItem->nama_pptk,
                            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                            'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                        ];
                    }
                }
            }

            // Convert to array (removing duplicates)
            $monitoringTargets = array_values($monitoringTargetsRaw);
            $monitoringRealisasi = array_values($monitoringRealisasiRaw);

            // Debug logging for rencana awal data analysis
            $debugInfo = [
                'skpd_tugas_id' => $tugasId,
                'current_urusan_id' => $currentUrusan ? $currentUrusan->id : 'ALL',
                'current_urusan_code' => $urusanCode ?? 'ALL',
                'current_urusan_name' => $currentUrusan ? ($currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A') : 'ALL URUSAN',
                'tahun' => $tahun,
                'all_monitoring_count' => $allMonitoring->count(),
                'targets_count' => count($monitoringTargets),
                'realisasi_count' => count($monitoringRealisasi),
                'bidang_urusan_count' => $bidangurusanTugas->count(),
                'program_count' => $programTugas->count(),
                'subkegiatan_count' => $subkegiatanTugas->count(),
                'all_skpd_tugas_count' => $allSkpdTugas->count()
            ];

            \Log::info("Rencana Awal PDF data query result - FIXED FILTERING", $debugInfo);

            // Log hierarchical structure
            \Log::info("Rencana Awal PDF hierarchical data", [
                'bidang_urusan' => $bidangurusanTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ];
                })->toArray(),
                'program' => $programTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ];
                })->toArray(),
                'subkegiatan' => $subkegiatanTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ];
                })->toArray()
            ]);

            // Prepare data for PDF
            $pdfData = [
                'skpd' => $skpdTugas->skpd,
                'tugas' => $skpdTugas,
                'monitoring' => $allMonitoring,
                'bidangurusanTugas' => $bidangurusanTugas,
                'programTugas' => $programTugas,
                'kegiatanTugas' => $kegiatanTugas,
                'subkegiatanTugas' => $subkegiatanTugas,
                'monitoringTargets' => $monitoringTargets,
                'monitoringRealisasi' => $monitoringRealisasi,
                'currentUrusan' => $currentUrusan, // Add current urusan for header
                'penandatangan_1' => [
                    'tempat' => $validated['penandatangan_1_tempat'],
                    'tanggal' => $validated['penandatangan_1_tanggal'],
                    'nama' => $validated['penandatangan_1_nama'],
                    'jabatan' => $validated['penandatangan_1_jabatan'],
                    'nip' => $validated['penandatangan_1_nip'],
                ],
                'jenis_laporan' => 'Rencana Awal',
                'tahun' => $tahun,
                // Add PDF settings for dynamic CSS
                'paper_size' => $validated['paper_size'],
                'orientation' => $validated['orientation'],
                'margin_top' => $validated['margin_top'],
                'margin_right' => $validated['margin_right'],
                'margin_bottom' => $validated['margin_bottom'],
                'margin_left' => $validated['margin_left'],
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdf.rencana-awal', $pdfData);

            // Note: CSS @page in template will override these settings, but kept for fallback
            $pdf->setPaper($validated['paper_size'], $validated['orientation']);

            // Basic options (margins handled by CSS @page)
            $pdf->setOptions([
                'enable_php' => false,
                'enable_javascript' => false,
                'enable_remote' => false,
            ]);

            $filename = 'Rencana_Awal_' . str_replace(' ', '_', $skpdTugas->skpd->nama_skpd) . '_' . date('Y-m-d') . '.pdf';

            // Log aktivitas download PDF
            UserActivityService::logPdfDownload('Rencana Awal', [
                'tugas_id' => $tugasId,
                'skpd_id' => $skpdTugas->skpd_id,
                'skpd_nama' => $skpdTugas->skpd->nama_skpd,
                'filename' => $filename,
                'tahun' => $validated['tahun'],
                'paper_size' => $validated['paper_size'],
                'orientation' => $validated['orientation']
            ]);

            // Return PDF as response with proper headers for download
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdf->output()),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating Rencana Awal PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat membuat PDF'], 500);
        }
    }

    /**
     * Generate PDF for Triwulan
     */
    public function generateTriwulanPdf(Request $request, $tid, $tugasId)
    {
        try {
            // Set memory and time limits for PDF generation
            $this->setPdfLimits();
            
            // Handle both JSON and form data
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $validated = validator($data, [
                'tahun' => 'required|integer|min:2020|max:2030',
                'mode' => 'nullable|string|in:all,filtered',
                'sumber_dana_ids' => 'nullable|array',
                'sumber_dana_ids.*' => 'integer',
                'penandatangan_1_tempat' => 'required|string|max:100',
                'penandatangan_1_tanggal' => 'required|date',
                'penandatangan_1_nama' => 'required|string|max:255',
                'penandatangan_1_jabatan' => 'required|string|max:255',
                'penandatangan_1_nip' => 'required|string|max:50',
                'paper_size' => 'required|in:A4,A3,Letter',
                'orientation' => 'required|in:portrait,landscape',
                'margin_top' => 'required|numeric|min:0|max:50',
                'margin_right' => 'required|numeric|min:0|max:50',
                'margin_bottom' => 'required|numeric|min:0|max:50',
                'margin_left' => 'required|numeric|min:0|max:50',
            ])->validate();

            // Check if SKPD Tugas exists
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'skpd.kepalaAktif.user.userDetail',
                'skpd.timKerja.operator.userDetail'
            ])->find($tugasId);

            if (!$skpdTugas) {
                \Log::error('Triwulan PDF Generation Error: SKPD Tugas not found', ['tugasId' => $tugasId, 'tid' => $tid]);
                return response()->json(['error' => 'Data SKPD tidak ditemukan'], 404);
            }

            if (!$skpdTugas->skpd) {
                \Log::error('Triwulan PDF Generation Error: SKPD not found', ['tugasId' => $tugasId, 'tid' => $tid]);
                return response()->json(['error' => 'Data SKPD tidak ditemukan'], 404);
            }

            // Validate kodeNomenklatur exists
            if (!$skpdTugas->kodeNomenklatur) {
                \Log::error('Triwulan PDF Generation Error: KodeNomenklatur not found', [
                    'tugasId' => $tugasId,
                    'tid' => $tid,
                    'kode_nomenklatur_id' => $skpdTugas->kode_nomenklatur_id
                ]);
                return response()->json(['error' => 'Data kode nomenklatur tidak ditemukan'], 404);
            }

            // Get periode by triwulan
            $tahun = $validated['tahun'];
            $periode = $this->getPeriodeByTriwulan($tid, $tahun);

            if (!$periode) {
                \Log::error('Triwulan PDF Generation Error: Periode not found', [
                    'tugasId' => $tugasId,
                    'tid' => $tid,
                    'tahun' => $tahun
                ]);
                return response()->json(['error' => 'Periode tidak ditemukan untuk tahun ' . $tahun], 404);
            }

            // SIMPLIFIED APPROACH: Get all data first, filter later if needed
            $allSkpdTugas = SkpdTugas::where('skpd_id', $skpdTugas->skpd_id)
                ->with(['kodeNomenklatur.details'])
                ->get();

            // Group by hierarchical structure
            $bidangurusanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 2;
            })->values();

            $programTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 3;
            })->values();

            $kegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 4;
            })->values();

            // Try different levels for sub kegiatan
            $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 5;
            })->values();

            // If no level 5, try level 4
            if ($subkegiatanTugas->isEmpty()) {
                $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 4;
                })->values();
            }

            // Get ALL monitoring data for this SKPD first
            $allMonitoring = Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                    $query->where('skpd_id', $skpdTugas->skpd_id);
                })
                ->where('tahun', $tahun)
                ->with(['anggaran.target.periode', 'anggaran.realisasi.periode', 'anggaran.sumberAnggaran', 'anggaran.pagu'])
                ->get();

            // ALWAYS determine and filter by urusan - regardless of what level is opened
            $currentUrusan = null;
            $urusanCode = null;

            // Determine urusan from any level of task
            $currentCode = $skpdTugas->kodeNomenklatur->nomor_kode ?? '';

            // DEBUG: Log current task details for TRIWULAN
            \Log::info("TRIWULAN URUSAN DETERMINATION - Current Task Details", [
                'task_id' => $tugasId,
                'current_code' => $currentCode,
                'jenis_nomenklatur' => $skpdTugas->kodeNomenklatur->jenis_nomenklatur ?? 'NULL',
                'nomenklatur' => $skpdTugas->kodeNomenklatur->nomenklatur ?? 'NULL'
            ]);

            // DEBUG: Log all available urusan for TRIWULAN
            \Log::info("TRIWULAN URUSAN DETERMINATION - Available Urusan in SKPD", [
                'all_urusan' => $bidangurusanTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A',
                        'jenis_nomenklatur' => $item->kodeNomenklatur->jenis_nomenklatur ?? 'N/A'
                    ];
                })->toArray()
            ]);

            if ($skpdTugas->kodeNomenklatur->jenis_nomenklatur == 2) {
                // Current task is urusan itself
                $currentUrusan = $skpdTugas;
                $urusanCode = $currentCode;
                \Log::info("TRIWULAN URUSAN DETERMINATION - Current task IS urusan", [
                    'urusan_code' => $urusanCode,
                    'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A'
                ]);
            } else {
                // SPECIAL CASE: If current task is very high level (jenis_nomenklatur = 0 or 1)
                // and has short code like "1", try to find urusan by prefix matching
                if ($skpdTugas->kodeNomenklatur->jenis_nomenklatur <= 1 && strlen($currentCode) <= 2) {
                    \Log::info("TRIWULAN URUSAN DETERMINATION - High level task detected, trying prefix matching");

                    // Try to find urusan that starts with current code
                    $currentUrusan = $bidangurusanTugas->first(function($item) use ($currentCode) {
                        $itemCode = $item->kodeNomenklatur->nomor_kode ?? '';
                        $isMatch = str_starts_with($itemCode, $currentCode . '.');

                        \Log::info("TRIWULAN URUSAN DETERMINATION - Prefix matching", [
                            'item_code' => $itemCode,
                            'current_code' => $currentCode,
                            'prefix_check' => $currentCode . '.',
                            'is_match' => $isMatch
                        ]);

                        return $isMatch;
                    });

                    if ($currentUrusan) {
                        $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                        \Log::info("TRIWULAN URUSAN DETERMINATION - Found urusan by prefix!", [
                            'urusan_code' => $urusanCode,
                            'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A'
                        ]);
                    }
                }

                // If no urusan found by prefix, try normal pattern matching
                if (!$currentUrusan) {
                    // Find parent urusan from current task's code
                    // Try different urusan code patterns
                    $possibleUrusanCodes = [];

                    if (strlen($currentCode) >= 4) {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 4); // X.XX format
                    }
                    if (strlen($currentCode) >= 3) {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 3); // X.X format
                    }
                    if (strlen($currentCode) >= 5 && substr($currentCode, 4, 1) === '.') {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 5); // X.XX. format
                    }

                    \Log::info("TRIWULAN URUSAN DETERMINATION - Trying possible urusan codes", [
                        'current_code' => $currentCode,
                        'possible_codes' => $possibleUrusanCodes
                    ]);

                    // Search for urusan with any of these codes
                    foreach ($possibleUrusanCodes as $testCode) {
                        \Log::info("TRIWULAN URUSAN DETERMINATION - Testing code: " . $testCode);

                        $currentUrusan = $allSkpdTugas->first(function($item) use ($testCode, $currentCode) {
                            $itemCode = $item->kodeNomenklatur->nomor_kode ?? '';
                            $isUrusan = $item->kodeNomenklatur->jenis_nomenklatur == 2;
                            $codeMatch = ($itemCode === $testCode || str_starts_with($currentCode, $itemCode));

                            \Log::info("TRIWULAN URUSAN DETERMINATION - Checking item", [
                                'item_id' => $item->id,
                                'item_code' => $itemCode,
                                'is_urusan' => $isUrusan,
                                'test_code' => $testCode,
                                'code_match' => $codeMatch,
                                'current_code' => $currentCode,
                                'starts_with_check' => str_starts_with($currentCode, $itemCode),
                                'equals_check' => $itemCode === $testCode,
                                'result' => $isUrusan && $codeMatch
                            ]);

                            return $isUrusan && $codeMatch;
                        });

                        if ($currentUrusan) {
                            $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                            \Log::info("TRIWULAN URUSAN DETERMINATION - Found urusan!", [
                                'urusan_code' => $urusanCode,
                                'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A',
                                'matched_test_code' => $testCode
                            ]);
                            break;
                        }
                    }
                }

                if (!$currentUrusan) {
                    \Log::error("TRIWULAN URUSAN DETERMINATION - No urusan found for current task! Will default to first urusan.");

                    // FALLBACK: If we still can't find specific urusan, default to first available urusan
                    // This happens when user is at very high level but we want to show filtered data
                    if ($bidangurusanTugas->isNotEmpty()) {
                        $currentUrusan = $bidangurusanTugas->first();
                        $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                        \Log::info("TRIWULAN URUSAN DETERMINATION - Using first available urusan as fallback", [
                            'urusan_code' => $urusanCode,
                            'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A'
                        ]);
                    }
                }
            }

            // Apply urusan filtering if urusan is found
            if ($currentUrusan && $urusanCode) {
                \Log::info("Filtering by urusan", [
                    'urusan_code' => $urusanCode,
                    'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A',
                    'original_code' => $currentCode
                ]);

                // Filter data by urusan
                $bidangurusanTugas = collect([$currentUrusan]);

                $programTugas = $allSkpdTugas->filter(function($item) use ($urusanCode) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 3 &&
                           str_starts_with($item->kodeNomenklatur->nomor_kode ?? '', $urusanCode);
                })->values();

                $kegiatanTugas = $allSkpdTugas->filter(function($item) use ($urusanCode) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 4 &&
                           str_starts_with($item->kodeNomenklatur->nomor_kode ?? '', $urusanCode);
                })->values();

                $subkegiatanTugas = $allSkpdTugas->filter(function($item) use ($urusanCode) {
                    return ($item->kodeNomenklatur->jenis_nomenklatur == 5 || $item->kodeNomenklatur->jenis_nomenklatur == 4) &&
                           str_starts_with($item->kodeNomenklatur->nomor_kode ?? '', $urusanCode);
                })->values();

                // Filter monitoring by urusan
                $allMonitoring = $allMonitoring->filter(function($monitoring) use ($urusanCode) {
                    $monitoringCode = $monitoring->tugas->kodeNomenklatur->nomor_kode ?? '';
                    return str_starts_with($monitoringCode, $urusanCode);
                });
            } else {
                \Log::warning("Could not determine urusan for filtering", [
                    'task_code' => $currentCode,
                    'task_jenis' => $skpdTugas->kodeNomenklatur->jenis_nomenklatur ?? 'N/A'
                ]);
            }

            // Collect monitoring targets and realisasi with UNIQUE filtering to avoid duplicates
            $monitoringTargetsRaw = [];
            $monitoringRealisasiRaw = [];

            foreach ($allMonitoring as $monitoringItem) {
                foreach ($monitoringItem->anggaran as $anggaran) {
                    // Get pagu data for all categories (pokok, parsial, perubahan) with proper aggregation
                    $paguData = [
                        'pokok' => 0,
                        'parsial' => 0,
                        'perubahan' => 0
                    ];

                    // Aggregate pagu data from all records
                    foreach ($anggaran->pagu as $pagu) {
                        switch ($pagu->kategori) {
                            case 1:
                                $paguData['pokok'] += $pagu->dana;
                                break;
                            case 2:
                                $paguData['parsial'] += $pagu->dana;
                                break;
                            case 3:
                                $paguData['perubahan'] += $pagu->dana;
                                break;
                        }
                    }

                    // Collect monitoring targets with unique key to prevent duplicates
                    foreach ($anggaran->target as $target) {
                        $uniqueKey = $monitoringItem->skpd_tugas_id . '_' . $anggaran->sumber_anggaran_id . '_' . $target->periode_id;

                        $monitoringTargetsRaw[$uniqueKey] = [
                            'id' => $target->id,
                            'task_id' => $monitoringItem->skpd_tugas_id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan,
                            'pagu_pokok' => $paguData['pokok'],
                            'pagu_parsial' => $paguData['parsial'],
                            'pagu_perubahan' => $paguData['perubahan'],
                            'periode' => $target->periode ? $target->periode->nama : 'Unknown',
                            'periode_id' => $target->periode_id,
                            'monitoring_id' => $monitoringItem->id,
                            'monitoring_anggaran_id' => $anggaran->id,
                            'deskripsi' => $monitoringItem->deskripsi,
                            'nama_pptk' => $monitoringItem->nama_pptk,
                            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                            'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                        ];
                    }

                    // Collect monitoring realisasi with unique key to prevent duplicates
                    foreach ($anggaran->realisasi as $realisasi) {
                        $uniqueKey = $monitoringItem->skpd_tugas_id . '_' . $anggaran->sumber_anggaran_id . '_' . $realisasi->periode_id;

                        $monitoringRealisasiRaw[$uniqueKey] = [
                            'id' => $realisasi->id,
                            'task_id' => $monitoringItem->skpd_tugas_id,
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'pagu_pokok' => $paguData['pokok'],
                            'pagu_parsial' => $paguData['parsial'],
                            'pagu_perubahan' => $paguData['perubahan'],
                            'periode' => $realisasi->periode ? $realisasi->periode->nama : 'Unknown',
                            'periode_id' => $realisasi->periode_id,
                            'monitoring_id' => $monitoringItem->id,
                            'monitoring_anggaran_id' => $anggaran->id,
                            'deskripsi' => $monitoringItem->deskripsi,
                            'nama_pptk' => $monitoringItem->nama_pptk,
                            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                            'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                        ];
                    }
                }
            }

            // Convert to array (removing duplicates)
            $monitoringTargets = array_values($monitoringTargetsRaw);
            $monitoringRealisasi = array_values($monitoringRealisasiRaw);

            // Apply sumber dana filtering if specified
            $selectedSumberDanaIds = $validated['sumber_dana_ids'] ?? [];
            $isFiltered = !empty($selectedSumberDanaIds);
            
            if ($isFiltered) {
                \Log::info('Applying Sumber Dana Filter', [
                    'selected_sumber_dana_ids' => $selectedSumberDanaIds,
                    'before_filter_targets' => count($monitoringTargets),
                    'before_filter_realisasi' => count($monitoringRealisasi)
                ]);

                // Filter targets by selected sumber dana
                $monitoringTargets = array_filter($monitoringTargets, function($target) use ($selectedSumberDanaIds) {
                    return in_array($target['sumber_anggaran_id'], $selectedSumberDanaIds);
                });

                // Filter realisasi by selected sumber dana
                $monitoringRealisasi = array_filter($monitoringRealisasi, function($realisasi) use ($selectedSumberDanaIds) {
                    return in_array($realisasi['sumber_anggaran_id'], $selectedSumberDanaIds);
                });

                // Re-index arrays after filtering
                $monitoringTargets = array_values($monitoringTargets);
                $monitoringRealisasi = array_values($monitoringRealisasi);

                \Log::info('Applied Sumber Dana Filter', [
                    'after_filter_targets' => count($monitoringTargets),
                    'after_filter_realisasi' => count($monitoringRealisasi)
                ]);

                // Get filtered sumber dana names for display
                $filteredSumberDanaNames = [];
                foreach ($monitoringTargets as $target) {
                    if (in_array($target['sumber_anggaran_id'], $selectedSumberDanaIds)) {
                        $filteredSumberDanaNames[$target['sumber_anggaran_id']] = $target['sumber_anggaran_nama'];
                    }
                }
                $filteredSumberDanaNames = array_unique(array_values($filteredSumberDanaNames));
            }

            // Debug logging for triwulan data analysis
            $debugInfo = [
                'skpd_tugas_id' => $tugasId,
                'current_urusan_id' => $currentUrusan ? $currentUrusan->id : 'ALL',
                'current_urusan_code' => $urusanCode ?? 'ALL',
                'current_urusan_name' => $currentUrusan ? ($currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A') : 'ALL URUSAN',
                'period_id' => $periode->id,
                'tahun' => $tahun,
                'all_monitoring_count' => $allMonitoring->count(),
                'targets_count' => count($monitoringTargets),
                'realisasi_count' => count($monitoringRealisasi),
                'bidang_urusan_count' => $bidangurusanTugas->count(),
                'program_count' => $programTugas->count(),
                'subkegiatan_count' => $subkegiatanTugas->count(),
                'all_skpd_tugas_count' => $allSkpdTugas->count(),
                'filtered_monitoring_count' => $allMonitoring->count(),
                'filtering_applied' => $currentUrusan ? 'YES' : 'NO'
            ];

            \Log::info("Triwulan PDF data query result - DEBUGGING FILTER", $debugInfo);

            // Log hierarchical structure with DETAILED ANALYSIS
            \Log::info("Triwulan PDF hierarchical data - FINAL RESULT", [
                'bidang_urusan' => $bidangurusanTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ];
                })->toArray(),
                'program' => $programTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ];
                })->toArray(),
                'subkegiatan' => $subkegiatanTugas->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                        'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ];
                })->toArray(),
                'expected_urusan_code' => $urusanCode ?? 'NONE',
                'filtering_status' => $currentUrusan ? 'APPLIED' : 'NOT_APPLIED'
            ]);

            // Extra validation: Check if there are urusan that shouldn't be there
            if ($currentUrusan && $urusanCode) {
                $unexpectedUrusan = $bidangurusanTugas->filter(function($item) use ($urusanCode) {
                    return $item->kodeNomenklatur->nomor_kode !== $urusanCode;
                });

                if ($unexpectedUrusan->isNotEmpty()) {
                    \Log::error("UNEXPECTED URUSAN FOUND IN FILTERED DATA!", [
                        'expected_urusan' => $urusanCode,
                        'unexpected_urusan' => $unexpectedUrusan->map(function($item) {
                            return [
                                'id' => $item->id,
                                'nomor_kode' => $item->kodeNomenklatur->nomor_kode ?? 'N/A',
                                'nomenklatur' => $item->kodeNomenklatur->nomenklatur ?? 'N/A'
                            ];
                        })->toArray()
                    ]);
                }
            }

            // Prepare data for PDF
            $pdfData = [
                'skpd' => $skpdTugas->skpd,
                'tugas' => $skpdTugas,
                'monitoring' => $allMonitoring,
                'bidangurusanTugas' => $bidangurusanTugas,
                'programTugas' => $programTugas,
                'kegiatanTugas' => $kegiatanTugas,
                'subkegiatanTugas' => $subkegiatanTugas,
                'monitoringTargets' => $monitoringTargets,
                'monitoringRealisasi' => $monitoringRealisasi,
                'periode' => $periode,
                'currentUrusan' => $currentUrusan, // Add current urusan for header
                'penandatangan_1' => [
                    'tempat' => $validated['penandatangan_1_tempat'],
                    'tanggal' => $validated['penandatangan_1_tanggal'],
                    'nama' => $validated['penandatangan_1_nama'],
                    'jabatan' => $validated['penandatangan_1_jabatan'],
                    'nip' => $validated['penandatangan_1_nip'],
                ],
                'jenis_laporan' => $this->getTriwulanName($tid) . ($isFiltered ? ' (Selektif)' : ''),
                'tahun' => $tahun,
                'tid' => $tid,
                'triwulanName' => $this->getTriwulanName($tid),
                'isFiltered' => $isFiltered,
                // Add PDF settings for dynamic CSS
                'paper_size' => $validated['paper_size'],
                'orientation' => $validated['orientation'],
                'margin_top' => $validated['margin_top'],
                'margin_right' => $validated['margin_right'],
                'margin_bottom' => $validated['margin_bottom'],
                'margin_left' => $validated['margin_left'],
            ];

            // Add filtering info if filtered
            if ($isFiltered) {
                $pdfData['selectedSumberDanaNames'] = $filteredSumberDanaNames ?? [];
                $pdfData['filteredItemsCount'] = count($monitoringTargets);
                
                // Calculate filtered totals
                $pdfData['filteredTotals'] = [
                    'totalTarget' => array_sum(array_column($monitoringTargets, 'keuangan')),
                    'totalRealisasi' => array_sum(array_column($monitoringRealisasi, 'keuangan')),
                    'totalPagu' => 0
                ];
                
                // Calculate total pagu from filtered targets
                foreach ($monitoringTargets as $target) {
                    $paguPokok = $target['pagu_pokok'] ?? 0;
                    $paguParsial = $target['pagu_parsial'] ?? 0;
                    $paguPerubahan = $target['pagu_perubahan'] ?? 0;
                    
                    // Use the latest available pagu
                    if ($paguPerubahan > 0) {
                        $pdfData['filteredTotals']['totalPagu'] += $paguPerubahan;
                    } elseif ($paguParsial > 0) {
                        $pdfData['filteredTotals']['totalPagu'] += $paguParsial;
                    } else {
                        $pdfData['filteredTotals']['totalPagu'] += $paguPokok;
                    }
                }
                
                $pdfData['filteredTotals']['persentaseCapaian'] = $pdfData['filteredTotals']['totalTarget'] > 0 
                    ? round(($pdfData['filteredTotals']['totalRealisasi'] / $pdfData['filteredTotals']['totalTarget']) * 100, 2)
                    : 0;
            }

            // Generate PDF using appropriate template
            $template = $isFiltered ? 'pdf.triwulan-filtered' : 'pdf.triwulan';
            $pdf = Pdf::loadView($template, $pdfData);

            // Note: CSS @page in template will override these settings, but kept for fallback
            $pdf->setPaper($validated['paper_size'], $validated['orientation']);

            // Basic options (margins handled by CSS @page)
            $pdf->setOptions([
                'enable_php' => false,
                'enable_javascript' => false,
                'enable_remote' => false,
            ]);

            // Generate filename based on filtering status
            $modeText = $isFiltered ? '_Selektif' : '_Lengkap';
            $cleanSkpdName = str_replace(' ', '_', $skpdTugas->skpd->nama_skpd);
            $filename = $this->getTriwulanName($tid) . $modeText . '_' . $cleanSkpdName . '_' . date('Y-m-d') . '.pdf';

            // Log aktivitas download PDF
            $logData = [
                'tugas_id' => $tugasId,
                'skpd_id' => $skpdTugas->skpd_id,
                'skpd_nama' => $skpdTugas->skpd->nama_skpd,
                'filename' => $filename,
                'tid' => $tid,
                'triwulan' => $this->getTriwulanName($tid),
                'tahun' => $validated['tahun'],
                'paper_size' => $validated['paper_size'],
                'orientation' => $validated['orientation'],
                'mode' => $isFiltered ? 'filtered' : 'full',
                'is_filtered' => $isFiltered
            ];

            if ($isFiltered) {
                $logData['selected_sumber_dana_ids'] = $selectedSumberDanaIds;
                $logData['selected_sumber_dana_names'] = $filteredSumberDanaNames ?? [];
                $logData['filtered_targets_count'] = count($monitoringTargets);
                $logData['filtered_realisasi_count'] = count($monitoringRealisasi);
            }

            UserActivityService::logPdfDownload($this->getTriwulanName($tid) . ($isFiltered ? ' Selektif' : ''), $logData);

            // Return PDF as response with proper headers for download
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdf->output()),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating Triwulan PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat membuat PDF'], 500);
        }
    }

    /**
     * Generate Filtered PDF for Triwulan based on selected sumber dana (Simplified Version)
     */
    public function generateFilteredTriwulanPdf(Request $request, $tid, $tugasId)
    {
        try {
            // Set memory and time limits for PDF generation
            $this->setPdfLimits();
            
            $validated = $request->validate([
                'tahun' => 'required|integer|min:2020|max:2030',
                'sumber_dana_ids' => 'required|array|min:1',
                'sumber_dana_ids.*' => 'integer',
                'mode' => 'nullable|string',
                'paper_size' => 'nullable|string|in:A4,A3,Letter',
                'orientation' => 'nullable|string|in:portrait,landscape',
                'margin_top' => 'nullable|numeric|min:10|max:50',
                'margin_right' => 'nullable|numeric|min:10|max:50',
                'margin_bottom' => 'nullable|numeric|min:10|max:50',
                'margin_left' => 'nullable|numeric|min:10|max:50',
                'penandatangan_1_tempat' => 'required|string|max:255',
                'penandatangan_1_tanggal' => 'required|date',
                'penandatangan_1_nama' => 'required|string|max:255',
                'penandatangan_1_jabatan' => 'required|string|max:255',
                'penandatangan_1_nip' => 'required|string|max:50',
            ]);

            // Use tahun from user input instead of system active year
            $tahun = $validated['tahun'];
            
            // Check if SKPD Tugas exists
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'skpd.kepalaAktif.user.userDetail',
                'skpd.timKerja.operator.userDetail'
            ])->find($tugasId);

            if (!$skpdTugas) {
                \Log::error('Filtered Triwulan PDF Generation Error: SKPD Tugas not found', ['tugasId' => $tugasId, 'tid' => $tid]);
                return response()->json(['error' => 'Data SKPD tidak ditemukan'], 404);
            }

            if (!$skpdTugas->skpd) {
                \Log::error('Filtered Triwulan PDF Generation Error: SKPD not found', ['tugasId' => $tugasId, 'tid' => $tid]);
                return response()->json(['error' => 'Data SKPD tidak ditemukan'], 404);
            }

            // Validate kodeNomenklatur exists
            if (!$skpdTugas->kodeNomenklatur) {
                \Log::error('Filtered Triwulan PDF Generation Error: KodeNomenklatur not found', [
                    'tugasId' => $tugasId,
                    'tid' => $tid,
                    'kode_nomenklatur_id' => $skpdTugas->kode_nomenklatur_id
                ]);
                return response()->json(['error' => 'Data kode nomenklatur tidak ditemukan'], 404);
            }
            
            // Get periode by triwulan and current year
            $periode = $this->getPeriodeByTriwulan($tid, $tahun);
            
            if (!$periode) {
                \Log::error('Filtered Triwulan PDF Generation Error: Periode not found', [
                    'tugasId' => $tugasId,
                    'tid' => $tid,
                    'tahun' => $tahun
                ]);
                return response()->json(['error' => 'Periode tidak ditemukan untuk tahun ' . $tahun], 404);
            }

            // FOLLOW SAME PATTERN AS TriwulanController: Get all monitoring data for the year first
            // Then filter targets/realisasi by periode_id
            $allMonitoring = Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                    $query->where('skpd_id', $skpdTugas->skpd_id);
                })
                ->where('tahun', $tahun)
                ->with(['anggaran.target.periode', 'anggaran.realisasi.periode', 'anggaran.sumberAnggaran', 'anggaran.pagu'])
                ->get();

            if ($allMonitoring->isEmpty()) {
                \Log::error('Filtered Triwulan PDF Generation Error: No monitoring data found for year', [
                    'tugasId' => $tugasId,
                    'tid' => $tid,
                    'tahun' => $tahun,
                    'skpd_id' => $skpdTugas->skpd_id
                ]);
                return response()->json(['error' => 'Data monitoring tidak ditemukan untuk tahun ' . $tahun], 404);
            }

            // Extract monitoring targets and realisasi that match the specific periode and selected sumber dana
            $selectedSumberDanaIds = $validated['sumber_dana_ids'];

            \Log::info('Filtered Triwulan PDF Generation: Found monitoring data', [
                'tugasId' => $tugasId,
                'tid' => $tid,
                'tahun' => $tahun,
                'periode_id' => $periode->id,
                'triwulan_name' => $this->getTriwulanName($tid),
                'monitoring_count' => $allMonitoring->count(),
                'selected_sumber_dana' => $selectedSumberDanaIds
            ]);
            $filteredTargets = [];
            $filteredRealisasi = [];
            $filteredAnggaran = collect();

            foreach ($allMonitoring as $monitoringItem) {
                foreach ($monitoringItem->anggaran as $anggaran) {
                    // Only include anggaran with selected sumber dana
                    if (!in_array($anggaran->sumber_anggaran_id, $selectedSumberDanaIds)) {
                        continue;
                    }

                    // Get pagu data for all categories (same as TriwulanController)
                    $paguData = [
                        'pokok' => 0,
                        'parsial' => 0,
                        'perubahan' => 0
                    ];

                    // Aggregate pagu data from all records
                    foreach ($anggaran->pagu as $pagu) {
                        switch ($pagu->kategori) {
                            case 1:
                                $paguData['pokok'] += $pagu->dana;
                                break;
                            case 2:
                                $paguData['parsial'] += $pagu->dana;
                                break;
                            case 3:
                                $paguData['perubahan'] += $pagu->dana;
                                break;
                        }
                    }

                    // Check if this anggaran has targets or realisasi for the specific periode
                    $hasDataForPeriode = false;
                    
                    // Check targets for this periode
                    $targetsForPeriode = $anggaran->target->where('periode_id', $periode->id);
                    if ($targetsForPeriode->isNotEmpty()) {
                        $hasDataForPeriode = true;
                        foreach ($targetsForPeriode as $target) {
                            $filteredTargets[] = [
                                'id' => $target->id,
                                'task_id' => $monitoringItem->skpd_tugas_id,
                                'kinerja_fisik' => $target->kinerja_fisik,
                                'keuangan' => $target->keuangan,
                                'pagu_pokok' => $paguData['pokok'],
                                'pagu_parsial' => $paguData['parsial'],
                                'pagu_perubahan' => $paguData['perubahan'],
                                'periode' => $target->periode ? $target->periode->nama : 'Unknown',
                                'periode_id' => $target->periode_id,
                                'monitoring_id' => $monitoringItem->id,
                                'monitoring_anggaran_id' => $anggaran->id,
                                'deskripsi' => $monitoringItem->deskripsi,
                                'nama_pptk' => $monitoringItem->nama_pptk,
                                'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                                'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                            ];
                        }
                    }

                    // Check realisasi for this periode
                    $realisasiForPeriode = $anggaran->realisasi->where('periode_id', $periode->id);
                    if ($realisasiForPeriode->isNotEmpty()) {
                        $hasDataForPeriode = true;
                        foreach ($realisasiForPeriode as $realisasi) {
                            $filteredRealisasi[] = [
                                'id' => $realisasi->id,
                                'task_id' => $monitoringItem->skpd_tugas_id,
                                'kinerja_fisik' => $realisasi->kinerja_fisik,
                                'keuangan' => $realisasi->keuangan,
                                'pagu_pokok' => $paguData['pokok'],
                                'pagu_parsial' => $paguData['parsial'],
                                'pagu_perubahan' => $paguData['perubahan'],
                                'periode' => $realisasi->periode ? $realisasi->periode->nama : 'Unknown',
                                'periode_id' => $realisasi->periode_id,
                                'monitoring_id' => $monitoringItem->id,
                                'monitoring_anggaran_id' => $anggaran->id,
                                'deskripsi' => $realisasi->deskripsi ?? $monitoringItem->deskripsi,
                                'nama_pptk' => $realisasi->nama_pptk ?? $monitoringItem->nama_pptk,
                                'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                                'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                            ];
                        }
                    }

                    // If this anggaran has data for the periode, include it
                    if ($hasDataForPeriode) {
                        $filteredAnggaran->push($anggaran);
                    }
                }
            }

            if (empty($filteredTargets) && empty($filteredRealisasi)) {
                \Log::warning('Filtered Triwulan PDF Generation: No data after filtering', [
                    'tugasId' => $tugasId,
                    'tid' => $tid,
                    'periode_id' => $periode->id,
                    'selected_sumber_dana' => $selectedSumberDanaIds,
                    'total_monitoring' => $allMonitoring->count()
                ]);
                return response()->json(['error' => 'Tidak ada data target atau realisasi untuk ' . $this->getTriwulanName($tid) . ' pada sumber dana yang dipilih'], 404);
            }

            \Log::info('Filtered Triwulan PDF Generation: Data filtered successfully', [
                'tugasId' => $tugasId,
                'tid' => $tid,
                'periode_id' => $periode->id,
                'filtered_targets_count' => count($filteredTargets),
                'filtered_realisasi_count' => count($filteredRealisasi),
                'filtered_anggaran_count' => $filteredAnggaran->count(),
                'selected_sumber_dana' => $selectedSumberDanaIds
            ]);

            // Get selected sumber dana names for display
            $selectedSumberDanaNames = SumberAnggaran::whereIn('id', $selectedSumberDanaIds)
                ->pluck('nama')
                ->toArray();

            // Calculate totals from filtered data (same as TriwulanController logic)
            $totals = [
                'totalPagu' => 0,
                'totalTarget' => 0,
                'totalRealisasi' => 0,
                'totalSubKegiatan' => count($filteredTargets), // Count actual filtered items
            ];

            // Calculate pagu from filtered targets (better accuracy)
            foreach ($filteredTargets as $target) {
                $paguPokok = $target['pagu_pokok'] ?? 0;
                $paguParsial = $target['pagu_parsial'] ?? 0;
                $paguPerubahan = $target['pagu_perubahan'] ?? 0;
                
                // Use the latest available pagu (priority: perubahan > parsial > pokok)
                if ($paguPerubahan > 0) {
                    $totals['totalPagu'] += $paguPerubahan;
                } elseif ($paguParsial > 0) {
                    $totals['totalPagu'] += $paguParsial;
                } else {
                    $totals['totalPagu'] += $paguPokok;
                }
            }

            $totals['totalTarget'] = collect($filteredTargets)->sum('keuangan');
            $totals['totalRealisasi'] = collect($filteredRealisasi)->sum('keuangan');

            $totals['persentaseCapaian'] = $totals['totalTarget'] > 0 
                ? round(($totals['totalRealisasi'] / $totals['totalTarget']) * 100, 2)
                : 0;

            // Get hierarchical data for PDF template (similar to regular PDF generation)
            $allSkpdTugas = SkpdTugas::where('skpd_id', $skpdTugas->skpd_id)
                ->with(['kodeNomenklatur.details'])
                ->get();

            // Group by hierarchical structure
            $bidangurusanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 2;
            })->values();

            $programTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 3;
            })->values();

            $kegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 4;
            })->values();

            $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 5;
            })->values();

            // If no level 5, use level 4 as subkegiatan (same as TriwulanController logic)
            if ($subkegiatanTugas->isEmpty()) {
                $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                    return $item->kodeNomenklatur->jenis_nomenklatur == 4;
                })->values();
            }

            // Determine current urusan (similar to regular PDF generation)
            $currentUrusan = null;
            $urusanCode = null;

            // Determine urusan from any level of task
            $currentCode = $skpdTugas->kodeNomenklatur->nomor_kode ?? '';

            if ($skpdTugas->kodeNomenklatur->jenis_nomenklatur == 2) {
                // Current task is urusan itself
                $currentUrusan = $skpdTugas;
                $urusanCode = $currentCode;
            } else {
                // SPECIAL CASE: If current task is very high level (jenis_nomenklatur = 0 or 1)
                // and has short code like "1", try to find urusan by prefix matching
                if ($skpdTugas->kodeNomenklatur->jenis_nomenklatur <= 1 && strlen($currentCode) <= 2) {
                    \Log::info("FILTERED TRIWULAN URUSAN DETERMINATION - High level task detected, trying prefix matching");

                    // Try to find urusan that starts with current code
                    $currentUrusan = $bidangurusanTugas->first(function($item) use ($currentCode) {
                        $itemCode = $item->kodeNomenklatur->nomor_kode ?? '';
                        $isMatch = str_starts_with($itemCode, $currentCode . '.');

                        return $isMatch;
                    });

                    if ($currentUrusan) {
                        $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                    }
                }

                // If no urusan found by prefix, try normal pattern matching
                if (!$currentUrusan) {
                    // Find parent urusan from current task's code
                    // Try different urusan code patterns
                    $possibleUrusanCodes = [];

                    if (strlen($currentCode) >= 4) {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 4); // X.XX format
                    }
                    if (strlen($currentCode) >= 3) {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 3); // X.X format
                    }
                    if (strlen($currentCode) >= 5 && substr($currentCode, 4, 1) === '.') {
                        $possibleUrusanCodes[] = substr($currentCode, 0, 5); // X.XX. format
                    }

                    // Search for urusan with any of these codes
                    foreach ($possibleUrusanCodes as $testCode) {
                        $currentUrusan = $allSkpdTugas->first(function($item) use ($testCode, $currentCode) {
                            return $item->kodeNomenklatur->jenis_nomenklatur == 2 &&
                                   ($item->kodeNomenklatur->nomor_kode === $testCode ||
                                    str_starts_with($currentCode, $item->kodeNomenklatur->nomor_kode ?? ''));
                        });

                        if ($currentUrusan) {
                            $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                            break;
                        }
                    }
                }

                // FALLBACK: If we still can't find specific urusan, default to first available urusan
                if (!$currentUrusan && $bidangurusanTugas->isNotEmpty()) {
                    $currentUrusan = $bidangurusanTugas->first();
                    $urusanCode = $currentUrusan->kodeNomenklatur->nomor_kode ?? '';
                    \Log::info("FILTERED TRIWULAN URUSAN DETERMINATION - Using first available urusan as fallback", [
                        'urusan_code' => $urusanCode,
                        'urusan_name' => $currentUrusan->kodeNomenklatur->nomenklatur ?? 'N/A'
                    ]);
                }
            }

            // Prepare PDF data
            $pdfData = [
                'skpd' => $skpdTugas->skpd,
                'tugas' => $skpdTugas,
                'monitoring' => $allMonitoring->first(), // Use first monitoring for basic info
                'bidangurusanTugas' => $bidangurusanTugas,
                'programTugas' => $programTugas,
                'kegiatanTugas' => $kegiatanTugas,
                'subkegiatanTugas' => $subkegiatanTugas,
                'monitoringTargets' => $filteredTargets, // Use filtered targets
                'monitoringRealisasi' => $filteredRealisasi, // Use filtered realisasi
                'currentUrusan' => $currentUrusan, // Add current urusan for header
                'filteredAnggaran' => $filteredAnggaran,
                'filteredTargets' => $filteredTargets,
                'filteredRealisasi' => $filteredRealisasi,
                'selectedSumberDanaNames' => $selectedSumberDanaNames,
                'totals' => $totals,
                'periode' => $periode,
                'jenis_laporan' => $this->getTriwulanName($tid) . ' (Filtered)',
                'tahun' => $tahun,
                'tid' => $tid,
                'triwulanName' => $this->getTriwulanName($tid),
                'isFiltered' => true,
                'paper_size' => $validated['paper_size'] ?? 'A4',
                'orientation' => $validated['orientation'] ?? 'landscape',
                'margin_top' => $validated['margin_top'] ?? 20,
                'margin_right' => $validated['margin_right'] ?? 20,
                'margin_bottom' => $validated['margin_bottom'] ?? 20,
                'margin_left' => $validated['margin_left'] ?? 20,
                'penandatangan_1' => [
                    'tempat' => $validated['penandatangan_1_tempat'],
                    'tanggal' => $validated['penandatangan_1_tanggal'],
                    'nama' => $validated['penandatangan_1_nama'],
                    'jabatan' => $validated['penandatangan_1_jabatan'],
                    'nip' => $validated['penandatangan_1_nip'],
                ],
            ];

            // Generate PDF using filtered triwulan template
            $pdf = Pdf::loadView('pdf.triwulan-filtered', $pdfData);
            $pdf->setPaper($validated['paper_size'] ?? 'A4', $validated['orientation'] ?? 'landscape');

            // Basic options
            $pdf->setOptions([
                'enable_php' => false,
                'enable_javascript' => false,
                'enable_remote' => false,
            ]);

            // Debug log for PDF data
            \Log::info('Filtered Triwulan PDF Data Summary', [
                'tugasId' => $tugasId,
                'tid' => $tid,
                'bidangurusanTugas_count' => $bidangurusanTugas->count(),
                'programTugas_count' => $programTugas->count(),
                'kegiatanTugas_count' => $kegiatanTugas->count(),
                'subkegiatanTugas_count' => $subkegiatanTugas->count(),
                'filteredTargets_count' => count($filteredTargets),
                'filteredRealisasi_count' => count($filteredRealisasi),
                'totals' => $totals,
                'selected_sumber_dana' => $selectedSumberDanaNames,
                'periode_name' => $periode->nama ?? 'NULL'
            ]);

            // Generate filename with filter info
            $sumberDanaText = implode('_', array_map(function($name) {
                return str_replace([' ', '/', '-'], '_', $name);
            }, $selectedSumberDanaNames));
            
            $filename = $this->getTriwulanName($tid) . '_Filtered_' . 
                        mb_substr($sumberDanaText, 0, 50) . '_' . // Limit filename length
                        str_replace(' ', '_', $skpdTugas->skpd->nama_skpd) . '_' . 
                        date('Y-m-d') . '.pdf';

            // Log filtered PDF download
            UserActivityService::logPdfDownload($this->getTriwulanName($tid) . ' Filtered', [
                'tugas_id' => $tugasId,
                'skpd_id' => $skpdTugas->skpd_id,
                'skpd_nama' => $skpdTugas->skpd->nama_skpd,
                'selected_sources' => $selectedSumberDanaIds,
                'selected_source_names' => $selectedSumberDanaNames,
                'mode' => 'filtered',
                'filename' => $filename,
                'tid' => $tid,
                'triwulan' => $this->getTriwulanName($tid),
                'tahun' => $tahun,
                'periode_id' => $periode->id,
                'total_filtered_targets' => count($filteredTargets),
                'total_filtered_realisasi' => count($filteredRealisasi),
                'paper_size' => $validated['paper_size'] ?? 'A4',
                'orientation' => $validated['orientation'] ?? 'landscape',
            ]);

            // Return PDF as response with proper headers for download
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdf->output()),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating filtered Triwulan PDF: ' . $e->getMessage(), [
                'tid' => $tid,
                'tugasId' => $tugasId,
                'selected_sumber_dana' => $validated['sumber_dana_ids'] ?? [],
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get periode by triwulan ID
     */
    private function getPeriodeByTriwulan(int $tid, int $tahun)
    {
        $triwulanNames = [
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4'
        ];

        if (!isset($triwulanNames[$tid])) {
            return null;
        }

        // First get the tahun record
        $periodeTahun = PeriodeTahun::where('tahun', $tahun)->first();
        if (!$periodeTahun) {
            return null;
        }

        // Then get the periode with the correct tahap and tahun
        return Periode::where('tahun_id', $periodeTahun->id)
            ->whereHas('tahap', function($query) use ($triwulanNames, $tid) {
                $query->where('tahap', 'like', '%' . $triwulanNames[$tid] . '%');
            })
            ->first();
    }

    /**
     * Get triwulan name by ID
     */
    private function getTriwulanName(int $tid)
    {
        $names = [
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4'
        ];

        return $names[$tid] ?? 'Unknown';
    }

    /**
     * Generate Selective PDF for Triwulan based on selected sumber dana
     */
    public function generateSelectiveTriwulanPdf(Request $request, $tid, $tugasId)
    {
        // Set memory and time limits for PDF generation
        $this->setPdfLimits();
        
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2030',
            'sumber_dana_ids' => 'required|array|min:1',
            'sumber_dana_ids.*' => 'integer|exists:sumber_anggaran,id',
            'mode' => 'required|string|in:single,multiple,compare,all',
            'paper_size' => 'nullable|string|in:A4,A3,Letter',
            'orientation' => 'nullable|string|in:portrait,landscape',
            'margin_top' => 'nullable|numeric|min:10|max:50',
            'margin_right' => 'nullable|numeric|min:10|max:50',
            'margin_bottom' => 'nullable|numeric|min:10|max:50',
            'margin_left' => 'nullable|numeric|min:10|max:50',
            'penandatangan_1_tempat' => 'required|string|max:255',
            'penandatangan_1_tanggal' => 'required|date',
            'penandatangan_1_nama' => 'required|string|max:255',
            'penandatangan_1_jabatan' => 'required|string|max:255',
            'penandatangan_1_nip' => 'required|string|max:50',
        ]);

        try {
            // Use tahun from user input instead of current system year
            $tahun = $validated['tahun'];
            
            // Get SKPD tugas
            $skpdTugas = SkpdTugas::with('skpd')->findOrFail($tugasId);
            
            // Get periode by triwulan
            $periode = $this->getPeriodeByTriwulan($tid, $tahun);
            
            if (!$periode) {
                return response()->json(['error' => 'Periode tidak ditemukan'], 404);
            }

            // Get monitoring data filtered by selected sumber dana
            $monitoring = Monitoring::where('skpd_tugas_id', $tugasId)
                ->where('periode_id', $periode->id)
                ->where('tahun', $tahun)
                ->first();

            if (!$monitoring) {
                return response()->json(['error' => 'Data monitoring tidak ditemukan'], 404);
            }

            // Filter monitoring anggaran by selected sumber dana
            $selectedSumberDanaIds = $validated['sumber_dana_ids'];
            $filteredAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                ->whereIn('sumber_anggaran_id', $selectedSumberDanaIds)
                ->with(['sumberAnggaran', 'pagu', 'realisasi'])
                ->get();

            // Get selected sumber dana names
            $selectedSumberDanaNames = SumberAnggaran::whereIn('id', $selectedSumberDanaIds)
                ->pluck('nama')
                ->toArray();

            // Calculate filtered totals
            $filteredTotals = [
                'totalPagu' => $filteredAnggaran->sum(function ($anggaran) {
                    return $anggaran->pagu->sum('dana');
                }),
                'totalRealisasi' => $filteredAnggaran->sum(function ($anggaran) {
                    return $anggaran->realisasi->sum('keuangan');
                }),
                'totalSubKegiatan' => $filteredAnggaran->count(),
            ];

            $filteredTotals['persentaseCapaian'] = $filteredTotals['totalPagu'] > 0 
                ? round(($filteredTotals['totalRealisasi'] / $filteredTotals['totalPagu']) * 100, 2)
                : 0;

            // Prepare PDF data
            $pdfData = [
                'skpd' => $skpdTugas->skpd,
                'tugas' => $skpdTugas,
                'monitoring' => $monitoring,
                'filteredAnggaran' => $filteredAnggaran,
                'selectedSumberDanaNames' => $selectedSumberDanaNames,
                'filteredTotals' => $filteredTotals,
                'selectionMode' => $validated['mode'],
                'filteredItemsCount' => $filteredAnggaran->count(),
                'periode' => $periode,
                'jenis_laporan' => $this->getTriwulanName($tid) . ' Selective',
                'tahun' => $tahun,
                'tid' => $tid,
                'paper_size' => $validated['paper_size'] ?? 'A4',
                'orientation' => $validated['orientation'] ?? 'landscape',
                'margin_top' => $validated['margin_top'] ?? 20,
                'margin_right' => $validated['margin_right'] ?? 20,
                'margin_bottom' => $validated['margin_bottom'] ?? 20,
                'margin_left' => $validated['margin_left'] ?? 20,
                'penandatangan_1' => [
                    'tempat' => $validated['penandatangan_1_tempat'],
                    'tanggal' => $validated['penandatangan_1_tanggal'],
                    'nama' => $validated['penandatangan_1_nama'],
                    'jabatan' => $validated['penandatangan_1_jabatan'],
                    'nip' => $validated['penandatangan_1_nip'],
                ],
            ];

            // Generate PDF with selective template
            $pdf = Pdf::loadView('pdf.triwulan-selective', $pdfData);
            $pdf->setPaper($validated['paper_size'] ?? 'A4', $validated['orientation'] ?? 'landscape');

            // Basic options
            $pdf->setOptions([
                'enable_php' => false,
                'enable_javascript' => false,
                'enable_remote' => false,
            ]);

            // Generate filename
            $sumberDanaText = implode('_', array_map(function($name) {
                return str_replace(' ', '_', $name);
            }, $selectedSumberDanaNames));
            
            $filename = $this->getTriwulanName($tid) . '_Selective_' . 
                        $sumberDanaText . '_' . 
                        str_replace(' ', '_', $skpdTugas->skpd->nama_skpd) . '_' . 
                        date('Y-m-d') . '.pdf';

            // Log selective PDF download
            UserActivityService::logPdfDownload($this->getTriwulanName($tid) . ' Selective', [
                'tugas_id' => $tugasId,
                'skpd_id' => $skpdTugas->skpd_id,
                'skpd_nama' => $skpdTugas->skpd->nama_skpd,
                'selected_sources' => $selectedSumberDanaIds,
                'selected_source_names' => $selectedSumberDanaNames,
                'mode' => $validated['mode'],
                'filename' => $filename,
                'tid' => $tid,
                'triwulan' => $this->getTriwulanName($tid),
                'tahun' => $tahun,
                'total_filtered_items' => $filteredAnggaran->count(),
                'paper_size' => $validated['paper_size'] ?? 'A4',
                'orientation' => $validated['orientation'] ?? 'landscape',
            ]);

            // Return PDF as response with proper headers for download
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdf->output()),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating selective PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat membuat PDF'], 500);
        }
    }
}
