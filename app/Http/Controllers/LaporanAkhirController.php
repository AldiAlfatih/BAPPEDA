<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\TimKerja;
use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\MonitoringRealisasi;
use App\Models\MonitoringTarget;
use App\Models\SumberAnggaran;
use App\Models\ArsipMonitoring;
use App\Models\PeriodeTahun;
use App\Services\UserActivityService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanAkhirController extends Controller
{
    /**
     * Get tahun aktif atau tahun yang dipilih
     */
    private function getTahunAktif(Request $request)
    {
        if ($request->has('tahun_id') && $request->tahun_id) {
            return PeriodeTahun::find($request->tahun_id);
        }

        return PeriodeTahun::getTahunAktif();
    }

    /**
     * Get semua tahun untuk dropdown
     */
    private function getAllTahun()
    {
        return PeriodeTahun::orderByDesc('tahun')->get();
    }

    /**
     * Get the correct User ID for breadcrumb navigation to arsip-monitoring.show
     * This should be the user with 'perangkat_daerah' role for the same SKPD as the tugas
     */
    private function getBreadcrumbUserId($tugas)
    {
        // Method 1: Cari user dengan role perangkat_daerah untuk SKPD ini
        $perangkatDaerahUser = \App\Models\User::whereHas('skpd', function($query) use ($tugas) {
                $query->where('skpd.id', $tugas->skpd_id);
            })
            ->whereHas('roles', function($query) {
                $query->where('name', 'perangkat_daerah');
            })
            ->first();

        if ($perangkatDaerahUser) {
            \Log::info('Found perangkat_daerah user for arsip breadcrumb', [
                'user_id' => $perangkatDaerahUser->id,
                'user_name' => $perangkatDaerahUser->name,
                'skpd_id' => $tugas->skpd_id,
                'tugas_id' => $tugas->id
            ]);
            return $perangkatDaerahUser->id;
        }

        // Method 2: Fallback ke kepala SKPD aktif
        $kepalaAktif = \App\Models\SkpdKepala::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with('user')
            ->first();

        if ($kepalaAktif && $kepalaAktif->user) {
            \Log::info('Using kepala SKPD for arsip breadcrumb fallback', [
                'user_id' => $kepalaAktif->user->id,
                'user_name' => $kepalaAktif->user->name,
                'skpd_id' => $tugas->skpd_id,
                'tugas_id' => $tugas->id
            ]);
            return $kepalaAktif->user->id;
        }

        // Method 3: Fallback ke operator tim kerja
        $timKerja = \App\Models\TimKerja::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with('operator')
            ->first();

        if ($timKerja && $timKerja->operator) {
            \Log::info('Using tim kerja operator for arsip breadcrumb fallback', [
                'user_id' => $timKerja->operator->id,
                'user_name' => $timKerja->operator->name,
                'skpd_id' => $tugas->skpd_id,
                'tugas_id' => $tugas->id
            ]);
            return $timKerja->operator->id;
        }

        \Log::warning('Could not find appropriate user for arsip breadcrumb', [
            'skpd_id' => $tugas->skpd_id,
            'tugas_id' => $tugas->id
        ]);

        return null;
    }
    /**
     * Display listing of SKPD for laporan akhir (tampilan pertama)
     */
    public function index(Request $request, $tahun = null)
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('laporan-akhir.show', $user->id);
        }

        $query = User::role('perangkat_daerah')
            ->with([
                'skpd' => function($q) {
                    $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
                },
                'userDetail'
            ]);

        if ($user->hasRole('operator')) {
            // Get SKPD IDs where the user is an operator
            $skpdIds = TimKerja::where('operator_id', $user->id)
                ->where('is_aktif', 1)
                ->pluck('skpd_id');

            $query->whereHas('skpd', function($q) use ($skpdIds) {
                $q->whereIn('skpd.id', $skpdIds);
            });
        }

        $users = $query->paginate(1000);

        // Transform the data but keep the original model instance
        foreach ($users as $user) {
            $skpd = $user->skpd->first();
            $user->nama_dinas = $skpd?->nama_skpd;
            $user->operator_name = $skpd?->operatorAktif?->operator?->name;
            $user->kepala_name = $skpd?->kepalaAktif?->user?->name;
            $user->kode_organisasi = $skpd?->kode_organisasi;
            $user->operator_nip = $skpd?->operatorAktif?->operator?->userDetail?->nip;
            $user->kepala_nip = $skpd?->kepalaAktif?->user?->userDetail?->nip;
        }

        return Inertia::render('LaporanAkhir/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Display SKPD detail with tugas list (tampilan kedua)
     */
    public function show(string $id)
    {
        $user = User::with([
            'skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            },
            'userDetail'
        ])->findOrFail($id);

        $skpd = $user->skpd->first();

        // Format data sesuai dengan interface props yang diharapkan
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'user_detail' => $user->userDetail,
            'skpd' => $skpd ? [
                'nama_skpd' => $skpd->nama_skpd,
                'operator_name' => $skpd->operatorAktif?->operator?->name,
                'operator_nip' => $skpd->operatorAktif?->operator?->userDetail?->nip,
                'kepala_name' => $skpd->kepalaAktif?->user?->name,
                'no_dpa' => $skpd->no_dpa,
                'kode_organisasi' => $skpd->kode_organisasi
            ] : null
        ];

        // Get SKPD Tugas with its relations
        $skpdTugas = SkpdTugas::where('skpd_id', $skpd?->id)
            ->with(['kodeNomenklatur'])
            ->get()
            ->map(function($tugas) {
                return [
                    'id' => $tugas->id,
                    'kode_nomenklatur' => [
                        'id' => $tugas->kodeNomenklatur->id,
                        'nomor_kode' => $tugas->kodeNomenklatur->nomor_kode,
                        'nomenklatur' => $tugas->kodeNomenklatur->nomenklatur,
                        'jenis_nomenklatur' => $tugas->kodeNomenklatur->jenis_nomenklatur,
                    ]
                ];
            });

        // Get Urusan List
        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();

        // Get Bidang Urusan List with details
        $bidangUrusanList = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'urusan_id' => $item->details->first()?->id_urusan
                ];
            });

        // Get Program List with details
        $programList = KodeNomenklatur::where('jenis_nomenklatur', 2)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'bidang_urusan_id' => $item->details->first()?->id_bidang_urusan
                ];
            });

        // Get Kegiatan List with details
        $kegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 3)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_program');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'program_id' => $item->details->first()?->id_program
                ];
            });

        // Get Subkegiatan List with details
        $subkegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 4)
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_kegiatan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'kegiatan_id' => $item->details->first()?->id_kegiatan
                ];
            });

        return Inertia::render('LaporanAkhir/Show', [
            'user' => $userData,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'skpdTugas' => $skpdTugas
        ]);
    }

    /**
     * Display arsip monitoring page with upload/download functionality
     */
    public function detail(Request $request, string $tugasId, $tahun = null)
    {
        $skpdTugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.kepalaAktif.user',
            'skpd.operatorAktif.operator'
        ])->findOrFail($tugasId);

        // Get tahun aktif atau tahun yang dipilih
        if ($tahun) {
            $tahunAktif = PeriodeTahun::where('tahun', $tahun)->first();
            if (!$tahunAktif) {
                $tahunAktif = PeriodeTahun::getTahunAktif();
            }
        } else {
            $tahunAktif = $this->getTahunAktif($request);
        }
        $allTahun = $this->getAllTahun();
        $currentYear = $tahunAktif ? $tahunAktif->tahun : date('Y');

        // Get arsip monitoring data for all periods
        $arsipData = [];
        $periodeOptions = ArsipMonitoring::getPeriodeOptions();

        foreach ($periodeOptions as $periode => $label) {
            $arsip = ArsipMonitoring::where('skpd_tugas_id', $tugasId)
                ->where('periode', $periode)
                ->where('tahun', $currentYear)
                ->with('uploadedBy')
                ->first();

            $arsipData[$periode] = [
                'label' => $label,
                'arsip' => $arsip ? [
                    'id' => $arsip->id,
                    'nama_file' => $arsip->nama_file,
                    'ukuran_file' => $arsip->formatted_size,
                    'tipe_file' => $arsip->tipe_file,
                    'tanggal_upload' => $arsip->tanggal_upload->format('d/m/Y H:i'),
                    'uploaded_by' => $arsip->uploadedBy->name,
                    'keterangan' => $arsip->keterangan,
                    'file_url' => $arsip->file_url,
                ] : null
            ];
        }

        // Format SKPD data
        $skpdData = [
            'id' => $skpdTugas->skpd->id,
            'nama_skpd' => $skpdTugas->skpd->nama_skpd,
            'kode_organisasi' => $skpdTugas->skpd->kode_organisasi,
            'kepala_name' => $skpdTugas->skpd->kepalaAktif?->user?->name,
            'kepala_nip' => $skpdTugas->skpd->kepalaAktif?->user?->userDetail?->nip,
            'operator_name' => $skpdTugas->skpd->operatorAktif?->operator?->name,
            'operator_nip' => $skpdTugas->skpd->operatorAktif?->operator?->userDetail?->nip,
        ];

        // Format tugas data
        $tugasData = [
            'id' => $skpdTugas->id,
            'kode_nomenklatur' => [
                'nomor_kode' => $skpdTugas->kodeNomenklatur->nomor_kode,
                'nomenklatur' => $skpdTugas->kodeNomenklatur->nomenklatur,
            ]
        ];

        // Get the correct user ID for breadcrumb navigation
        $breadcrumbUserId = $this->getBreadcrumbUserId($skpdTugas);

        return Inertia::render('LaporanAkhir/Detail', [
            'skpd' => $skpdData,
            'tugas' => $tugasData,
            'arsipData' => $arsipData,
            'tahun' => $currentYear,
            'tahunAktif' => $tahunAktif,
            'allTahun' => $allTahun,
            'periodeOptions' => $periodeOptions,
            'breadcrumbUserId' => $breadcrumbUserId, // Add breadcrumb user ID for correct navigation
        ]);
    }

    /**
     * Upload file arsip monitoring
     */
    public function uploadArsip(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // max 10MB
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'periode' => 'required|in:rencana_awal,triwulan_1,triwulan_2,triwulan_3,triwulan_4',
            'tahun' => 'required|digits:4|integer',
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs(
                'arsip_monitoring/' . $request->tahun . '/' . $request->skpd_tugas_id,
                $fileName,
                'public'
            );

            // Check if arsip already exists for this periode and tugas
            $existingArsip = ArsipMonitoring::where('skpd_tugas_id', $request->skpd_tugas_id)
                ->where('periode', $request->periode)
                ->where('tahun', $request->tahun)
                ->first();

            if ($existingArsip) {
                // Delete old file
                $existingArsip->deleteFile();

                // Update existing record
                $existingArsip->update([
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $filePath,
                    'ukuran_file' => $file->getSize(),
                    'tipe_file' => $file->getClientOriginalExtension(),
                    'tanggal_upload' => now(),
                    'uploaded_by' => Auth::id(),
                    'keterangan' => $request->keterangan
                ]);

                $arsip = $existingArsip;
            } else {
                // Create new record
                $arsip = ArsipMonitoring::create([
                    'skpd_tugas_id' => $request->skpd_tugas_id,
                    'periode' => $request->periode,
                    'tahun' => $request->tahun,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $filePath,
                    'ukuran_file' => $file->getSize(),
                    'tipe_file' => $file->getClientOriginalExtension(),
                    'tanggal_upload' => now(),
                    'uploaded_by' => Auth::id(),
                    'keterangan' => $request->keterangan
                ]);
            }

            // Log aktivitas upload file
            UserActivityService::logFileUpload($file->getClientOriginalName(), [
                'arsip_id' => $arsip->id,
                'skpd_tugas_id' => $request->skpd_tugas_id,
                'periode' => $request->periode,
                'tahun' => $request->tahun,
                'nama_file' => $file->getClientOriginalName(),
                'ukuran_file' => $file->getSize(),
                'tipe_file' => $file->getClientOriginalExtension(),
                'path_file' => $filePath,
                'keterangan' => $request->keterangan
            ]);

            return back()->with('success', 'File berhasil diunggah!');

        } catch (\Exception $e) {
            Log::error('Upload arsip error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Download file arsip monitoring
     */
    public function downloadArsip($id)
    {
        $arsip = ArsipMonitoring::findOrFail($id);

        if (!$arsip->fileExists()) {
            return back()->with('error', 'File tidak ditemukan!');
        }

        return Storage::disk('public')->download($arsip->path_file, $arsip->nama_file);
    }

    /**
     * Delete arsip monitoring
     */
    public function deleteArsip($id)
    {
        try {
            $arsip = ArsipMonitoring::findOrFail($id);

            // Delete file from storage
            $arsip->deleteFile();

            // Delete record from database
            $arsip->delete();

            return back()->with('success', 'File arsip berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Delete arsip error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    /**
     * View file arsip monitoring in browser
     */
    public function viewArsip($id)
    {
        try {
            $arsip = ArsipMonitoring::findOrFail($id);

            Log::info('ViewArsip Debug - Arsip found:', [
                'id' => $arsip->id,
                'nama_file' => $arsip->nama_file,
                'path_file' => $arsip->path_file,
                'tipe_file' => $arsip->tipe_file,
                'full_storage_path' => storage_path('app/public/' . $arsip->path_file),
                'file_exists' => $arsip->fileExists(),
                'storage_exists' => Storage::disk('public')->exists($arsip->path_file)
            ]);

            if (!$arsip->fileExists()) {
                Log::error('ViewArsip Error - File not found:', [
                    'path_file' => $arsip->path_file,
                    'full_path' => storage_path('app/public/' . $arsip->path_file)
                ]);
                return back()->with('error', 'File tidak ditemukan di storage!');
            }

            // For PDF files, return inline view
            if (strtolower($arsip->tipe_file) === 'pdf') {
                Log::info('ViewArsip - Serving PDF file:', ['file' => $arsip->nama_file]);

                try {
                    // Try to get file content directly
                    $fileContent = Storage::disk('public')->get($arsip->path_file);

                    return response($fileContent, 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'inline; filename="' . $arsip->nama_file . '"')
                        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', '0');

                } catch (\Exception $e) {
                    Log::error('ViewArsip - Failed to get file content:', ['error' => $e->getMessage()]);

                    // Fallback to Storage response method
                    return Storage::disk('public')->response($arsip->path_file, $arsip->nama_file, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="' . $arsip->nama_file . '"'
                    ]);
                }
            }

            // For other files, force download
            Log::info('ViewArsip - Downloading non-PDF file:', ['file' => $arsip->nama_file]);
            return $this->downloadArsip($id);

        } catch (\Exception $e) {
            Log::error('ViewArsip Exception:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
