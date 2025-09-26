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
     * Show PDF configuration form for Triwulan
     */
    public function showTriwulanPdfForm($tid, $tugasId)
    {
        try {
            // Set memory and time limits for PDF generation
            $this->setPdfLimits();
            
            // Log basic request information for debugging
            \Log::info('PDF Controller Triwulan Form Request Debug', [
                'method' => request()->method(),
                'url' => request()->url(),
                'user_id' => Auth::id(),
                'tid' => $tid,
                'tugasId' => $tugasId
            ]);
            
            // Validate triwulan ID
            if (!in_array($tid, [1, 2, 3, 4])) {
                return redirect()->back()->with('error', 'Invalid triwulan ID.');
            }
            
            // Get SKPD tugas with relations
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'skpd.kepalaAktif.user.userDetail',
                'skpd.timKerja.operator.userDetail'
            ])->find($tugasId);
            
            if (!$skpdTugas) {
                return redirect()->back()->with('error', 'SKPD Tugas tidak ditemukan.');
            }
            
            // Get available years
            $availableTahun = \App\Models\PeriodeTahun::orderByDesc('tahun')
                ->pluck('tahun')
                ->toArray();
            
            // Get triwulan name
            $triwulanNames = [
                1 => 'Triwulan I',
                2 => 'Triwulan II', 
                3 => 'Triwulan III',
                4 => 'Triwulan IV'
            ];
            
            $triwulanName = $triwulanNames[$tid] ?? 'Triwulan';
            
            return Inertia::render('PDF/TriwulanPdfForm', [
                'tugas' => $skpdTugas,
                'skpd' => $skpdTugas->skpd,
                'availableTahun' => $availableTahun,
                'tid' => $tid,
                'triwulanName' => $triwulanName,
                'defaultValues' => [
                    'tahun' => date('Y'),
                    'penandatangan_1_tempat' => 'Banjarmasin',
                    'penandatangan_1_tanggal' => date('Y-m-d'),
                    'penandatangan_1_nama' => $skpdTugas->skpd->kepalaAktif?->user?->name ?? '',
                    'penandatangan_1_jabatan' => 'Kepala ' . ($skpdTugas->skpd->nama_skpd ?? 'SKPD'),
                    'penandatangan_1_nip' => $skpdTugas->skpd->kepalaAktif?->user?->userDetail?->nip ?? '',
                    'paper_size' => 'A4',
                    'orientation' => 'landscape',
                    'margin_top' => 20,
                    'margin_right' => 20,
                    'margin_bottom' => 20,
                    'margin_left' => 20,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('PDF Controller Triwulan Form Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'tid' => $tid,
                'tugasId' => $tugasId
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat form PDF.');
        }
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
     * Generate PDF for Triwulan
     */
    public function generateTriwulanPdf(Request $request, $tid, $tugasId)
    {
        // DEBUGGING: Log route access immediately
        \Log::info('=== PDF TRIWULAN ROUTE ACCESSED ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'full_url' => $request->fullUrl(),
            'user_id' => Auth::id(),
            'tid' => $tid,
            'tugasId' => $tugasId,
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'timestamp' => now()->toDateTimeString()
        ]);
        
        try {
            // Set memory and time limits for PDF generation
            $this->setPdfLimits();
            
            // Log basic request information for debugging
            \Log::info('PDF Controller Triwulan Generate Request Debug', [
                'method' => $request->method(),
                'url' => $request->url(),
                'user_id' => Auth::id(),
                'tid' => $tid,
                'tugasId' => $tugasId
            ]);
            
            // Validate triwulan ID
            if (!in_array($tid, [1, 2, 3, 4])) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'ID Triwulan tidak valid. Harus 1-4.'], 400);
                }
                return redirect()->back()->with('error', 'ID Triwulan tidak valid. Harus 1-4.');
            }
            
            // Handle both JSON and form data - MENGIKUTI POLA RENCANA AWAL
            $data = $request->all();
            
            // Override tid jika ada di request body (dari Inertia)
            if (isset($data['tid'])) {
                $tid = $data['tid'];
            }
            if (isset($data['tugasId'])) {
                $tugasId = $data['tugasId'];
            }

            $validated = validator($data, [
                'tahun' => 'required|integer|min:2020|max:2030',
                'penandatangan_1_tempat' => 'required|string|max:100',
                'penandatangan_1_tanggal' => 'required|date',
                'penandatangan_1_nama' => 'required|string|max:255',
                'penandatangan_1_jabatan' => 'required|string|max:255',
                'penandatangan_1_nip' => 'required|string|max:50',
                'paper_size' => 'required|string|in:A4,A3,Letter',
                'orientation' => 'required|string|in:portrait,landscape',
                'margin_top' => 'required|integer|min:0|max:50',
                'margin_right' => 'required|integer|min:0|max:50',
                'margin_bottom' => 'required|integer|min:0|max:50',
                'margin_left' => 'required|integer|min:0|max:50',
            ])->validate();

            // Get SKPD tugas with relations
            $skpdTugas = SkpdTugas::with([
                'kodeNomenklatur.details',
                'skpd.kepalaAktif.user.userDetail',
                'skpd.timKerja.operator.userDetail'
            ])->find($tugasId);
            
            if (!$skpdTugas) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'SKPD Tugas tidak ditemukan.'], 404);
                }
                return redirect()->back()->with('error', 'SKPD Tugas tidak ditemukan.');
            }
            
            // Get periode by triwulan and year - SIMPLIFIED APPROACH
            $periode = $this->getPeriodeByTriwulan($tid, $validated['tahun']);
            
            // FALLBACK: Jika periode tidak ditemukan, cari periode yang ada untuk tahun ini (samakan dgn TriwulanController)
            if (!$periode) {
                \Log::warning("Periode triwulan tidak ditemukan untuk tahun {$validated['tahun']}, mencari periode alternatif (pattern Triwulan 1-4)");

                $triwulanNames = [
                    1 => 'Triwulan 1',
                    2 => 'Triwulan 2',
                    3 => 'Triwulan 3',
                    4 => 'Triwulan 4'
                ];

                // Ambil record tahun terlebih dahulu
                $periodeTahun = \App\Models\PeriodeTahun::where('tahun', $validated['tahun'])->first();

                if ($periodeTahun) {
                    $periode = \App\Models\Periode::with(['tahap', 'tahun'])
                        ->where('tahun_id', $periodeTahun->id)
                        ->whereHas('tahap', function($q) use ($triwulanNames, $tid) {
                            $q->where('tahap', 'like', '%' . $triwulanNames[$tid] . '%');
                        })
                        ->first();
                }

                // If still not found, create a minimal fallback (id null so template can skip strict filtering)
                if (!$periode) {
                    \Log::warning("No periode found at all, creating minimal fallback with null ID");
                    $periode = (object) [
                        'id' => null,
                        'tahap' => (object) ['tahap' => $triwulanNames[$tid] ?? ("Triwulan " . $tid)],
                        'tahun' => (object) ['tahun' => $validated['tahun']]
                    ];
                }
            }
            
            // PERBAIKAN: Get monitoring data for ALL urusan in this SKPD for this triwulan
            // This ensures PDF includes all urusan, not just specific task
            $triwulanNames = [
                1 => 'Triwulan I',
                2 => 'Triwulan II',
                3 => 'Triwulan III', 
                4 => 'Triwulan IV'
            ];
            
            // DEBUGGING: Test different query approaches
            \Log::info('=== PDF TRIWULAN DEBUG: QUERY EXPLORATION ===', [
                'skpd_id' => $skpdTugas->skpd_id,
                'tahun' => $validated['tahun'],
                'tid' => $tid,
                'triwulan_name' => $triwulanNames[$tid]
            ]);
            
            // EMERGENCY FALLBACK: Get latest monitoring data regardless of periode
            // This is to test if the issue is periode filtering
            $emergencyMonitoring = \App\Models\Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                    $query->where('skpd_id', $skpdTugas->skpd_id);
                })
                ->where('tahun', $validated['tahun'])
                ->with([
                    'anggaran.target',
                    'anggaran.realisasi', 
                    'anggaran.sumberAnggaran',
                    'anggaran.pagu',
                    'tugas.kodeNomenklatur'
                ])
                ->latest()
                ->take(5) // Just get first 5 records for testing
                ->get();
                
            \Log::info('EMERGENCY FALLBACK - Latest monitoring records (no periode filter):', [
                'emergency_count' => $emergencyMonitoring->count(),
                'emergency_sample' => $emergencyMonitoring->first() ? [
                    'id' => $emergencyMonitoring->first()->id,
                    'task_id' => $emergencyMonitoring->first()->task_id,
                    'tahun' => $emergencyMonitoring->first()->tahun,
                    'anggaran_first_target_count' => $emergencyMonitoring->first()->anggaran->first() ? $emergencyMonitoring->first()->anggaran->first()->target->count() : 0,
                    'anggaran_first_target_sample' => $emergencyMonitoring->first()->anggaran->first() && $emergencyMonitoring->first()->anggaran->first()->target->first() ? [
                        'kinerja_fisik' => $emergencyMonitoring->first()->anggaran->first()->target->first()->kinerja_fisik,
                        'keuangan' => $emergencyMonitoring->first()->anggaran->first()->target->first()->keuangan,
                        'periode_id' => $emergencyMonitoring->first()->anggaran->first()->target->first()->periode_id,
                    ] : 'NO_TARGET_SAMPLE'
                ] : 'NO_EMERGENCY_DATA'
            ]);
            
            // Method 1: Original query
            $monitoring = \App\Models\Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                    $query->where('skpd_id', $skpdTugas->skpd_id);
                })
                ->where('tahun', $validated['tahun'])
                ->with([
                    'anggaran.target.periode.tahap',
                    'anggaran.realisasi.periode.tahap',
                    'anggaran.sumberAnggaran',
                    'anggaran.pagu',
                    'tugas.kodeNomenklatur',
                    'periode.tahap'
                ])
                ->get();
                
            \Log::info('Method 1 - Original query results:', [
                'total_records' => $monitoring->count(),
                'sample_monitoring' => $monitoring->first() ? [
                    'id' => $monitoring->first()->id,
                    'tahun' => $monitoring->first()->tahun,
                    'task_id' => $monitoring->first()->task_id,
                    'periode_id' => $monitoring->first()->periode_id,
                    'anggaran_count' => $monitoring->first()->anggaran->count(),
                    'periode_tahap' => $monitoring->first()->periode->tahap->tahap ?? 'N/A'
                ] : 'NO_DATA'
            ]);
                
            // Method 2: Try without periode filtering first to see raw data
            $monitoringRaw = \App\Models\Monitoring::whereHas('tugas', function($query) use ($skpdTugas) {
                    $query->where('skpd_id', $skpdTugas->skpd_id);
                })
                ->where('tahun', $validated['tahun'])
                ->with([
                    'anggaran.target',
                    'anggaran.realisasi', 
                    'anggaran.sumberAnggaran',
                    'anggaran.pagu',
                    'tugas.kodeNomenklatur'
                ])
                ->get();
                
            \Log::info('Method 2 - Raw monitoring data (no periode filter):', [
                'total_raw_records' => $monitoringRaw->count(),
                'raw_sample' => $monitoringRaw->first() ? [
                    'id' => $monitoringRaw->first()->id,
                    'tahun' => $monitoringRaw->first()->tahun,
                    'task_id' => $monitoringRaw->first()->task_id,
                    'anggaran_count' => $monitoringRaw->first()->anggaran->count(),
                    'first_anggaran_targets' => $monitoringRaw->first()->anggaran->first() ? $monitoringRaw->first()->anggaran->first()->target->count() : 0,
                    'first_anggaran_realisasi' => $monitoringRaw->first()->anggaran->first() ? $monitoringRaw->first()->anggaran->first()->realisasi->count() : 0
                ] : 'NO_RAW_DATA'
            ]);
                
            // IMPORTANT: Jangan filter berdasarkan monitoring.periode_id karena banyak record monitoring tidak menyimpan periode_id,
            // sedangkan target/realisasi menyimpan periode_id yang benar. Gunakan dataset tahunan penuh.
            $monitoring = $monitoringRaw;
                
            \Log::info('=== PDF TRIWULAN DEBUG: DATA RETRIEVAL ===', [
                'skpd_id' => $skpdTugas->skpd_id,
                'total_monitoring_records' => $monitoring->count(),
                'tid' => $tid,
                'tahun' => $validated['tahun'],
                'periode_found' => $periode ? $periode->id : 'NULL',
                'periode_details' => $periode ? [
                    'id' => $periode->id,
                    'tahap' => $periode->tahap->tahap ?? 'N/A',
                    'tahun' => $periode->tahun->tahun ?? 'N/A'
                ] : null,
                'first_monitoring_sample' => $monitoring->first() ? [
                    'id' => $monitoring->first()->id,
                    'task_id' => $monitoring->first()->task_id,
                    'tahun' => $monitoring->first()->tahun,
                    'periode_id' => $monitoring->first()->periode_id ?? 'NULL',
                    'anggaran_count' => $monitoring->first()->anggaran->count(),
                ] : 'NO_MONITORING_DATA'
            ]);
            
            // Prepare data for PDF
            $monitoringTargets = [];
            $monitoringRealisasi = [];
            
            // DEBUG: Log each step of data processing
            $processedItems = 0;
            
            foreach ($monitoring as $monitoringItem) {
                \Log::info("Processing monitoring item {$processedItems}", [
                    'monitoring_id' => $monitoringItem->id,
                    'task_id' => $monitoringItem->task_id,
                    'anggaran_count' => $monitoringItem->anggaran->count()
                ]);
                
                foreach ($monitoringItem->anggaran as $anggaranIndex => $anggaran) {
                    \Log::info("Processing anggaran {$anggaranIndex} for monitoring {$monitoringItem->id}", [
                        'anggaran_id' => $anggaran->id ?? 'N/A',
                        'sumber_anggaran_id' => $anggaran->sumber_anggaran_id ?? 'N/A',
                        'target_count' => $anggaran->target->count(),
                        'realisasi_count' => $anggaran->realisasi->count(),
                        'pagu_count' => $anggaran->pagu->count()
                    ]);
                    
                    // Get pagu data
                    $paguData = [
                        'pokok' => 0,
                        'parsial' => 0,
                        'perubahan' => 0
                    ];
                    
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
                    
                    // Collect targets
                    foreach ($anggaran->target as $targetIndex => $target) {
                        $targetData = [
                            // Penting: gunakan skpd_tugas_id agar cocok dengan $subkegiatan->id di template
                            'task_id' => $monitoringItem->skpd_tugas_id,
                            'periode_id' => $target->periode_id ?? $periode->id, // Use fallback periode if target periode is null
                            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan,
                            'pagu_pokok' => $paguData['pokok'],
                            'pagu_parsial' => $paguData['parsial'],
                            'pagu_perubahan' => $paguData['perubahan'],
                            'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                            'deskripsi' => $monitoringItem->deskripsi,
                            'nama_pptk' => $monitoringItem->nama_pptk,
                        ];
                        
                        $monitoringTargets[] = $targetData;
                        
                        \Log::info("Added target {$targetIndex} for monitoring {$monitoringItem->id}", [
                            'target_data' => $targetData
                        ]);
                    }
                    
                    // Collect realisasi
                    foreach ($anggaran->realisasi as $realisasiIndex => $realisasi) {
                        $realisasiData = [
                            // Penting: gunakan skpd_tugas_id agar cocok dengan $subkegiatan->id di template
                            'task_id' => $monitoringItem->skpd_tugas_id,
                            'periode_id' => $realisasi->periode_id ?? $periode->id, // Use fallback periode if realisasi periode is null
                            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'pagu_pokok' => $paguData['pokok'],
                            'pagu_parsial' => $paguData['parsial'],
                            'pagu_perubahan' => $paguData['perubahan'],
                            'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                            'deskripsi' => $monitoringItem->deskripsi,
                            'nama_pptk' => $monitoringItem->nama_pptk,
                        ];
                        
                        $monitoringRealisasi[] = $realisasiData;
                        
                        \Log::info("Added realisasi {$realisasiIndex} for monitoring {$monitoringItem->id}", [
                            'realisasi_data' => $realisasiData
                        ]);
                    }
                }
                $processedItems++;
            }

            // HARMONIZE: Make sure periode->id aligns with data present for this triwulan
            try {
                $candidatePeriodeIds = array_values(array_unique(array_filter(array_merge(
                    array_column($monitoringTargets, 'periode_id'),
                    array_column($monitoringRealisasi, 'periode_id')
                ))));

                if (!empty($candidatePeriodeIds)) {
                    $numericNames = [1 => 'Triwulan 1', 2 => 'Triwulan 2', 3 => 'Triwulan 3', 4 => 'Triwulan 4'];
                    $romanNames   = [1 => 'Triwulan I', 2 => 'Triwulan II', 3 => 'Triwulan III', 4 => 'Triwulan IV'];

                    $matchedPeriode = \App\Models\Periode::whereIn('id', $candidatePeriodeIds)
                        ->whereHas('tahap', function($q) use ($numericNames, $romanNames, $tid) {
                            $q->where('tahap', 'like', '%' . ($numericNames[$tid] ?? '') . '%')
                              ->orWhere('tahap', 'like', '%' . ($romanNames[$tid] ?? '') . '%');
                        })
                        ->first();

                    $usedPeriodeId = $matchedPeriode ? $matchedPeriode->id : $candidatePeriodeIds[0];

                    if (!$periode || !isset($periode->id) || (int)$periode->id !== (int)$usedPeriodeId) {
                        if (is_object($periode)) {
                            $periode->id = $usedPeriodeId;
                        } else {
                            $periode = (object) ['id' => $usedPeriodeId];
                        }
                    }

                    \Log::info('Periode harmonized for Triwulan PDF', [
                        'tid' => $tid,
                        'used_periode_id' => $usedPeriodeId,
                        'candidate_periode_ids' => $candidatePeriodeIds
                    ]);
                } else {
                    \Log::warning('No candidate periode_ids found in targets/realisasi; template may render empty if it filters by periode_id');
                }
            } catch (\Throwable $th) {
                \Log::error('Error during periode harmonization', [
                    'message' => $th->getMessage()
                ]);
            }

            // Get triwulan name
            $triwulanNames = [
                1 => 'Triwulan I',
                2 => 'Triwulan II',
                3 => 'Triwulan III',
                4 => 'Triwulan IV'
            ];
            $triwulanName = $triwulanNames[$tid];

            // DIAGNOSTIC: Count how many entries match the final periode->id
            try {
                $periodeIdForFilter = $periode->id ?? null;
                $matchingTargets = array_values(array_filter($monitoringTargets, function($t) use ($periodeIdForFilter) {
                    return isset($t['periode_id']) && $periodeIdForFilter !== null && (int)$t['periode_id'] === (int)$periodeIdForFilter;
                }));
                $matchingRealisasi = array_values(array_filter($monitoringRealisasi, function($r) use ($periodeIdForFilter) {
                    return isset($r['periode_id']) && $periodeIdForFilter !== null && (int)$r['periode_id'] === (int)$periodeIdForFilter;
                }));

                \Log::info('Triwulan PDF matching counts for periode filter', [
                    'periode_id' => $periodeIdForFilter,
                    'total_targets' => count($monitoringTargets),
                    'total_realisasi' => count($monitoringRealisasi),
                    'matching_targets' => count($matchingTargets),
                    'matching_realisasi' => count($matchingRealisasi),
                    'sample_matching_target' => !empty($matchingTargets) ? $matchingTargets[0] : 'NONE',
                    'sample_matching_realisasi' => !empty($matchingRealisasi) ? $matchingRealisasi[0] : 'NONE',
                ]);
            } catch (\Throwable $th) {
                \Log::error('Error computing matching counts', ['message' => $th->getMessage()]);
            }
            
            // PERBAIKAN: Get hierarchical data for all urusan in SKPD
            // This ensures PDF displays complete SKPD structure with all urusan
            $allSkpdTugas = \App\Models\SkpdTugas::where('skpd_id', $skpdTugas->skpd_id)
                ->with(['kodeNomenklatur.details'])
                ->get();

            // Group by hierarchical structure for complete SKPD display
            $bidangurusanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 1; // Bidang Urusan
            })->values();

            $programTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 2; // Program
            })->values();

            $kegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 3; // Kegiatan
            })->values();

            $subkegiatanTugas = $allSkpdTugas->filter(function($item) {
                return $item->kodeNomenklatur->jenis_nomenklatur == 4; // Sub Kegiatan
            })->values();

            \Log::info('=== PDF TRIWULAN DEBUG: FINAL DATA SUMMARY ===', [
                'bidang_urusan_count' => $bidangurusanTugas->count(),
                'program_count' => $programTugas->count(),
                'kegiatan_count' => $kegiatanTugas->count(),
                'subkegiatan_count' => $subkegiatanTugas->count(),
                'monitoring_targets_count' => count($monitoringTargets),
                'monitoring_realisasi_count' => count($monitoringRealisasi),
                'periode_id_used' => $periode->id,
                'sample_target' => !empty($monitoringTargets) ? $monitoringTargets[0] : 'NO_TARGET_DATA',
                'sample_realisasi' => !empty($monitoringRealisasi) ? $monitoringRealisasi[0] : 'NO_REALISASI_DATA',
                'bidang_urusan_sample' => $bidangurusanTugas->first() ? [
                    'id' => $bidangurusanTugas->first()->id,
                    'nomor_kode' => $bidangurusanTugas->first()->kodeNomenklatur->nomor_kode ?? 'N/A',
                    'nomenklatur' => $bidangurusanTugas->first()->kodeNomenklatur->nomenklatur ?? 'N/A'
                ] : 'NO_BIDANG_URUSAN',
                'target_task_ids' => array_unique(array_column($monitoringTargets, 'task_id')),
                'subkegiatan_ids' => $subkegiatanTugas->pluck('id')->toArray()
            ]);
            
            // PERBAIKAN: Filter data untuk template berdasarkan periode yang fleksibel
            // Jika periode->id adalah null, filter berdasarkan task_id saja
            if ($periode->id === null) {
                \Log::info('Using flexible filtering for null periode_id');
                
                // Group targets and realisasi by task_id for easier template access
                $targetsByTask = collect($monitoringTargets)->groupBy('task_id');
                $realisasiByTask = collect($monitoringRealisasi)->groupBy('task_id');
                
                // Create filtered arrays that template can use without periode_id filtering
                $filteredTargets = [];
                $filteredRealisasi = [];
                
                foreach ($subkegiatanTugas as $subkegiatan) {
                    $taskTargets = $targetsByTask->get($subkegiatan->id, collect());
                    $taskRealisasi = $realisasiByTask->get($subkegiatan->id, collect());
                    
                    foreach ($taskTargets as $target) {
                        $target['periode_match'] = true; // Mark as matching for template
                        $filteredTargets[] = $target;
                    }
                    
                    foreach ($taskRealisasi as $realisasi) {
                        $realisasi['periode_match'] = true; // Mark as matching for template
                        $filteredRealisasi[] = $realisasi;
                    }
                }
                
                // Override original arrays with filtered ones
                $monitoringTargets = $filteredTargets;
                $monitoringRealisasi = $filteredRealisasi;
                
                \Log::info('Flexible filtering completed', [
                    'filtered_targets_count' => count($monitoringTargets),
                    'filtered_realisasi_count' => count($monitoringRealisasi)
                ]);
            }
            
            // Prepare penandatangan data
            $penandatangan_1 = [
                'tempat' => $validated['penandatangan_1_tempat'],
                'tanggal' => $validated['penandatangan_1_tanggal'],
                'nama' => $validated['penandatangan_1_nama'],
                'jabatan' => $validated['penandatangan_1_jabatan'],
                'nip' => $validated['penandatangan_1_nip']
            ];
            
            // Prepare PDF data
            $pdfData = array_merge($validated, [
                'skpd' => $skpdTugas->skpd,
                'tugas' => $skpdTugas,
                'periode' => $periode,
                'triwulanName' => $triwulanName,
                'jenis_laporan' => 'Laporan Monitoring ' . $triwulanName . ' - Semua Urusan',
                'monitoringTargets' => $monitoringTargets,
                'monitoringRealisasi' => $monitoringRealisasi,
                'bidangurusanTugas' => $bidangurusanTugas,
                'programTugas' => $programTugas,
                'kegiatanTugas' => $kegiatanTugas,
                'subkegiatanTugas' => $subkegiatanTugas,
                'currentUrusan' => null, // For all urusan PDF, set to null
                'penandatangan_1' => $penandatangan_1, // Add penandatangan data
                'generated_at' => now()->format('d/m/Y H:i:s'),
                'generated_by' => Auth::user()->name,
            ]);
            
            // Generate PDF with error handling
            try {
                \Log::info('PDF Triwulan: Starting PDF generation', [
                    'template' => 'pdf.triwulan',
                    'data_keys' => array_keys($pdfData),
                    'paper_size' => $validated['paper_size'],
                    'orientation' => $validated['orientation']
                ]);
                
                $pdf = Pdf::loadView('pdf.triwulan', array_merge($pdfData, ['tid' => $tid, 'tugasId' => $tugasId]))
                    ->setPaper($validated['paper_size'], $validated['orientation'])
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isPhpEnabled' => true,
                        'defaultFont' => 'sans-serif',
                        'enable_remote' => true,
                    ]);
                    
                \Log::info('PDF Triwulan: PDF object created successfully');
                
            } catch (\Exception $pdfException) {
                \Log::error('PDF Triwulan: PDF generation failed', [
                    'error' => $pdfException->getMessage(),
                    'file' => $pdfException->getFile(),
                    'line' => $pdfException->getLine(),
                    'trace' => $pdfException->getTraceAsString()
                ]);
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Gagal membuat PDF: ' . $pdfException->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $pdfException->getMessage());
            }
            
            // Generate filename - indicate it includes all urusan
            $filename = 'Laporan_' . str_replace(' ', '_', $triwulanName) . '_Semua_Urusan_' . 
                       str_replace([' ', '/', '\\'], '_', $skpdTugas->skpd->nama_skpd) . '_' . 
                       $validated['tahun'] . '.pdf';
            
            // Log successful PDF generation
            \Log::info('PDF Triwulan generated successfully', [
                'filename' => $filename,
                'user_id' => Auth::id(),
                'tid' => $tid,
                'tugasId' => $tugasId,
                'tahun' => $validated['tahun']
            ]);
            
            // Log activity
            UserActivityService::logPdfDownload('Triwulan PDF', [
                'triwulan_id' => $tid,
                'tugas_id' => $tugasId,
                'skpd_id' => $skpdTugas->skpd_id,
                'tahun' => $validated['tahun'],
                'filename' => $filename
            ]);
            
            // Return PDF download with error handling
            try {
                \Log::info('PDF Triwulan: Starting PDF download', [
                    'filename' => $filename,
                    'user_id' => Auth::id()
                ]);
                
                return $pdf->download($filename);
                
            } catch (\Exception $downloadException) {
                \Log::error('PDF Triwulan: PDF download failed', [
                    'error' => $downloadException->getMessage(),
                    'filename' => $filename,
                    'file' => $downloadException->getFile(),
                    'line' => $downloadException->getLine()
                ]);
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Gagal mengunduh PDF: ' . $downloadException->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Gagal mengunduh PDF: ' . $downloadException->getMessage());
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('PDF Triwulan Validation Error', [
                'errors' => $e->errors(),
                'tid' => $tid,
                'tugasId' => $tugasId
            ]);
            
            // Handle AJAX requests differently
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Data tidak valid',
                    'details' => $e->errors()
                ], 422);
            }
            
            $errorMessage = 'Data tidak valid: ' . implode(', ', array_flatten($e->errors()));
            return redirect()->back()->with('error', $errorMessage);
            
        } catch (\Exception $e) {
            \Log::error('PDF Triwulan Generation Error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'tid' => $tid,
                'tugasId' => $tugasId
            ]);
            
            // Handle AJAX requests differently
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Get periode by triwulan and year
     */
    private function getPeriodeByTriwulan($tid, $tahun)
    {
        $triwulanNames = [
            1 => 'Triwulan I',
            2 => 'Triwulan II',
            3 => 'Triwulan III',
            4 => 'Triwulan IV'
        ];
        
        $triwulanName = $triwulanNames[$tid] ?? null;
        if (!$triwulanName) {
            return null;
        }
        
        return \App\Models\Periode::whereHas('tahap', function($query) use ($triwulanName) {
                $query->where('tahap', $triwulanName);
            })
            ->whereHas('tahun', function($query) use ($tahun) {
                $query->where('tahun', $tahun);
            })
            ->with(['tahap', 'tahun'])
            ->first();
    }

    /**
     * Generate PDF for Rencana Awal
     */
    public function generateRencanaAwalPdf(Request $request, $tugasId)
    {
        try {
            // Set memory and time limits for PDF generation
            $this->setPdfLimits();
            
            // Log basic request information for debugging
            \Log::info('PDF Controller Request Debug', [
                'method' => $request->method(),
                'url' => $request->url(),
                'user_id' => Auth::id(),
                'tugasId' => $tugasId
            ]);
            
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

            // Log request data for debugging
            \Log::info('PDF Generation Request', [
                'tugasId' => $tugasId,
                'validated_data' => $validated
            ]);

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

            \Log::info('SKPD Tugas found successfully', [
                'tugasId' => $tugasId,
                'skpd_id' => $skpdTugas->skpd_id,
                'skpd_name' => $skpdTugas->skpd->nama_skpd ?? 'N/A'
            ]);

            // Get monitoring data for Rencana Awal with hierarchical structure
            $tahun = $validated['tahun'];

            // SIMPLIFIED APPROACH: Get all data first, filter later if needed
            $allSkpdTugas = SkpdTugas::where('skpd_id', $skpdTugas->skpd_id)
                ->with(['kodeNomenklatur.details'])
                ->get();

            // Group by hierarchical structure
            // Include both jenis 1 and 2 as Bidang Urusan to cover datasets that code bidang urusan differently
            $bidangurusanTugas = $allSkpdTugas->filter(function($item) {
                return in_array($item->kodeNomenklatur->jenis_nomenklatur, [1, 2]);
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

            // NEW APPROACH: Export per SKPD instead of per urusan
            \Log::info("RENCANA AWAL PDF - Using SKPD-based filtering (showing all data for SKPD)", [
                'skpd_id' => $skpdTugas->skpd_id,
                'skpd_name' => $skpdTugas->skpd->nama_skpd,
                'total_tasks' => $allSkpdTugas->count(),
                'total_monitoring' => $allMonitoring->count()
            ]);

            // No filtering by urusan - include ALL data for this SKPD
            // This ensures complete SKPD report regardless of which task was selected
            $currentUrusan = null; // Set to null to indicate full SKPD report
            
            // Keep all data as-is (no urusan filtering)
            // $bidangurusanTugas, $programTugas, $kegiatanTugas, $subkegiatanTugas, $allMonitoring
            // remain with their original SKPD-wide data

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
                'all_skpd_tugas_count' => $allSkpdTugas->count(),
                'skpd_data' => [
                    'id' => $skpdTugas->skpd_id,
                    'nama' => $skpdTugas->skpd->nama_skpd ?? 'N/A',
                    'kode_organisasi' => $skpdTugas->skpd->kode_organisasi ?? 'N/A'
                ]
            ];

            \Log::info("Rencana Awal PDF data query result - FIXED FILTERING", $debugInfo);
            
            // Validate essential data before PDF generation
            if ($bidangurusanTugas->isEmpty() && $subkegiatanTugas->isEmpty()) {
                \Log::warning('No data found for PDF generation', [
                    'tugasId' => $tugasId,
                    'tahun' => $tahun,
                    'skpd_id' => $skpdTugas->skpd_id
                ]);
            }

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

            // Generate PDF with error handling
            \Log::info('Starting PDF generation', [
                'tugasId' => $tugasId,
                'tahun' => $tahun,
                'paper_size' => $validated['paper_size'],
                'orientation' => $validated['orientation']
            ]);

            try {
                $pdf = Pdf::loadView('pdf.rencana-awal', $pdfData);
                
                // Note: CSS @page in template will override these settings, but kept for fallback
                $pdf->setPaper($validated['paper_size'], $validated['orientation']);

                // Basic options (margins handled by CSS @page)
                $pdf->setOptions([
                    'enable_php' => false,
                    'enable_javascript' => false,
                    'enable_remote' => false,
                ]);
                
                \Log::info('PDF generated successfully', ['tugasId' => $tugasId]);
                
            } catch (\Exception $pdfError) {
                \Log::error('PDF Generation Error in loadView', [
                    'tugasId' => $tugasId,
                    'error' => $pdfError->getMessage(),
                    'file' => $pdfError->getFile(),
                    'line' => $pdfError->getLine()
                ]);
                throw $pdfError;
            }

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
            \Log::error('Error generating Rencana Awal PDF: ' . $e->getMessage(), [
                'tugasId' => $tugasId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            // Return more specific error information in development
            if (config('app.debug')) {
                return response()->json([
                    'error' => 'Terjadi kesalahan saat membuat PDF',
                    'debug' => [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }
            
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
     * Get Roman numeral for triwulan
     */
    private function getRomanNumeral(int $tid)
    {
        $numerals = [
            1 => 'I',
            2 => 'II', 
            3 => 'III',
            4 => 'IV'
        ];
        
        return $numerals[$tid] ?? 'I';
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
