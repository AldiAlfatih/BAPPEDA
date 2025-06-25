<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringTarget;
use App\Models\MonitoringRealisasi;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use App\Models\User;
use App\Models\Periode;
use App\Models\PeriodeTahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
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

        return Inertia::render('PDF/TriwulanPdfForm', [
            'tugas' => $skpdTugas,
            'skpd' => $skpdTugas->skpd,
            'tid' => $tid,
            'triwulanName' => $triwulanName,
        ]);
    }

    /**
     * Generate PDF for Rencana Awal
     */
    public function generateRencanaAwalPdf(Request $request, $tugasId)
    {
        // Handle both JSON and form data
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        
        $validated = validator($data, [
            'penandatangan_1_tempat' => 'required|string|max:100',
            'penandatangan_1_tanggal' => 'required|date',
            'penandatangan_1_nama' => 'required|string|max:255',
            'penandatangan_1_jabatan' => 'required|string|max:255',
            'penandatangan_1_nip' => 'required|string|max:50',
            'penandatangan_2_tempat' => 'required|string|max:100',
            'penandatangan_2_tanggal' => 'required|date',
            'penandatangan_2_nama' => 'required|string|max:255',
            'penandatangan_2_jabatan' => 'required|string|max:255',
            'penandatangan_2_nip' => 'required|string|max:50',
            'paper_size' => 'required|in:A4,A3,Letter',
            'orientation' => 'required|in:portrait,landscape',
            'margin_top' => 'required|numeric|min:0|max:50',
            'margin_right' => 'required|numeric|min:0|max:50',  
            'margin_bottom' => 'required|numeric|min:0|max:50',
            'margin_left' => 'required|numeric|min:0|max:50',
        ])->validate();

        $skpdTugas = SkpdTugas::with([
            'kodeNomenklatur.details',
            'skpd.kepalaAktif.user.userDetail',
            'skpd.timKerja.operator.userDetail'
        ])->findOrFail($tugasId);

        // Get monitoring data for Rencana Awal with hierarchical structure
        $tahun = date('Y');
        
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
            'penandatangan_2' => [
                'tempat' => $validated['penandatangan_2_tempat'],
                'tanggal' => $validated['penandatangan_2_tanggal'],
                'nama' => $validated['penandatangan_2_nama'],
                'jabatan' => $validated['penandatangan_2_jabatan'],
                'nip' => $validated['penandatangan_2_nip'],
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
        
        // Return PDF as response with proper headers for download
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdf->output()),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate PDF for Triwulan
     */
    public function generateTriwulanPdf(Request $request, $tid, $tugasId)
    {
        // Handle both JSON and form data
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        
        $validated = validator($data, [
            'penandatangan_1_tempat' => 'required|string|max:100',
            'penandatangan_1_tanggal' => 'required|date',
            'penandatangan_1_nama' => 'required|string|max:255',
            'penandatangan_1_jabatan' => 'required|string|max:255',
            'penandatangan_1_nip' => 'required|string|max:50',
            'penandatangan_2_tempat' => 'required|string|max:100',
            'penandatangan_2_tanggal' => 'required|date',
            'penandatangan_2_nama' => 'required|string|max:255',
            'penandatangan_2_jabatan' => 'required|string|max:255',
            'penandatangan_2_nip' => 'required|string|max:50',
            'paper_size' => 'required|in:A4,A3,Letter',
            'orientation' => 'required|in:portrait,landscape',
            'margin_top' => 'required|numeric|min:0|max:50',
            'margin_right' => 'required|numeric|min:0|max:50',  
            'margin_bottom' => 'required|numeric|min:0|max:50',
            'margin_left' => 'required|numeric|min:0|max:50',
        ])->validate();

        $skpdTugas = SkpdTugas::with([
            'kodeNomenklatur.details',
            'skpd.kepalaAktif.user.userDetail',
            'skpd.timKerja.operator.userDetail'
        ])->findOrFail($tugasId);

        // Get periode by triwulan
        $tahun = date('Y');
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        
        if (!$periode) {
            return back()->withErrors(['error' => 'Periode tidak ditemukan']);
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
            'penandatangan_2' => [
                'tempat' => $validated['penandatangan_2_tempat'],
                'tanggal' => $validated['penandatangan_2_tanggal'],
                'nama' => $validated['penandatangan_2_nama'],
                'jabatan' => $validated['penandatangan_2_jabatan'],
                'nip' => $validated['penandatangan_2_nip'],
            ],
            'jenis_laporan' => $this->getTriwulanName($tid),
            'tahun' => $tahun,
            'tid' => $tid,
            // Add PDF settings for dynamic CSS
            'paper_size' => $validated['paper_size'],
            'orientation' => $validated['orientation'],
            'margin_top' => $validated['margin_top'],
            'margin_right' => $validated['margin_right'],
            'margin_bottom' => $validated['margin_bottom'],
            'margin_left' => $validated['margin_left'],
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.triwulan', $pdfData);
        
        // Note: CSS @page in template will override these settings, but kept for fallback
        $pdf->setPaper($validated['paper_size'], $validated['orientation']);
        
        // Basic options (margins handled by CSS @page)
        $pdf->setOptions([
            'enable_php' => false,
            'enable_javascript' => false,
            'enable_remote' => false,
        ]);

        $filename = $this->getTriwulanName($tid) . '_' . str_replace(' ', '_', $skpdTugas->skpd->nama_skpd) . '_' . date('Y-m-d') . '.pdf';
        
        // Return PDF as response with proper headers for download
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdf->output()),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
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
} 