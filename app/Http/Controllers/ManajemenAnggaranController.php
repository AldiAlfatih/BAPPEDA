<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\Skpd;
use App\Models\MonitoringTarget;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\SumberAnggaran;
use App\Models\Periode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ManajemenAnggaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('ManajemenAnggaran.show', $user->id);
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('ManajemenAnggaran', [
                'users' => $users,
            ]);
        }

        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);

        return Inertia::render('ManajemenAnggaran', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $kodeNomenklatur = KodeNomenklatur::all();

        return Inertia::render('Monitoring/Create', [
            'kodeNomenklatur' => $kodeNomenklatur,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'sumber_dana' => 'required|string|max:255',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|digits:4|integer',
            'deskripsi' => 'required|string',
            'pagu_pokok' => 'required|integer',
            'pagu_parsial' => 'nullable|integer',
            'pagu_perubahan' => 'nullable|integer',
        ]);

        Monitoring::create($validated);

        return redirect()->route('monitoring.index')->with('success', 'Data monitoring berhasil ditambahkan.');
    }

    public function show(string $id, Request $request)
    {
        $user = User::with(['skpd', 'userDetail'])->findOrFail($id);

        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();

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

        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

        // Dapatkan periode aktif (status 1 = aktif/buka) yang tahapnya adalah "Rencana"
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->get();

        // Dapatkan semua periode aktif untuk informasi tambahan
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->get();

        // Dapatkan tahun dari periode aktif
        $tahunAktif = null;
        if ($semuaPeriodeAktif->isNotEmpty()) {
            $tahunAktif = $semuaPeriodeAktif->first()->tahun;
        }
        
        // Ambil data sumber dana terakhir untuk setiap SKPD tugas berdasarkan periode aktif
        $dataAnggaranTerakhir = [];
        $periodeId = null;
        
        // Check if a specific period was requested
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        }
        // Otherwise use Rencana period ID if active
        elseif ($periodeAktif->isNotEmpty()) {
            $periodeId = $periodeAktif->first()->id;
        }
        
        foreach ($skpdTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Hanya ambil sub kegiatan
                // Cari monitoring yang terkait dengan SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();
                    
                if ($monitoring) {
                    // Ambil data anggaran untuk monitoring ini berdasarkan periode
                    $sumberAnggaranData = [];
                    
                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran']);
                    
                    if ($periodeId) {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) use ($periodeId) {
                            $query->where('kategori', 1) // Kategori 1 = pokok
                                  ->where('periode_id', $periodeId); // Filter berdasarkan periode
                        }]);
                    } else {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) {
                            $query->where('kategori', 1); // Kategori 1 = pokok
                        }]);
                    }
                    
                    $monitoringAnggaran = $monitoringAnggaranQuery->get();
                    
                    foreach ($monitoringAnggaran as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $nama = $anggaran->sumberAnggaran->nama;
                            $dana = $anggaran->pagu->first()->dana ?? 0;
                            $sumberAnggaranData[$nama] = $dana;
                        }
                    }
                    
                    // Simpan data per SKPD tugas
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => [
                            'dak' => isset($sumberAnggaranData['dak']),
                            'dak_peruntukan' => isset($sumberAnggaranData['dak_peruntukan']),
                            'dak_fisik' => isset($sumberAnggaranData['dak_fisik']),
                            'dak_non_fisik' => isset($sumberAnggaranData['dak_non_fisik']),
                            'blud' => isset($sumberAnggaranData['blud']),
                        ],
                        'values' => [
                            'dak' => $sumberAnggaranData['dak'] ?? 0,
                            'dak_peruntukan' => $sumberAnggaranData['dak_peruntukan'] ?? 0,
                            'dak_fisik' => $sumberAnggaranData['dak_fisik'] ?? 0,
                            'dak_non_fisik' => $sumberAnggaranData['dak_non_fisik'] ?? 0,
                            'blud' => $sumberAnggaranData['blud'] ?? 0,
                        ]
                    ];
                }
            }
        }

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
        ]);
    }

    public function showRencanaAwal($id, Request $request)
    {
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            }
        ])->findOrFail($id);

        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur.details')
            ->get();

        $urusanId = $tugas->kodeNomenklatur->details->first()?->id_urusan;

        $bidangurusanTugas = $skpdTugas->filter(fn($item) =>
            $item->kodeNomenklatur->jenis_nomenklatur == 1 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $programTugas = $skpdTugas->filter(fn($item) => 
            $item->kodeNomenklatur->jenis_nomenklatur == 2 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $kegiatanTugas = $skpdTugas->filter(fn($item) => 
            $item->kodeNomenklatur->jenis_nomenklatur == 3 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $subkegiatanTugas = $skpdTugas->filter(fn($item) => 
            $item->kodeNomenklatur->jenis_nomenklatur == 4 &&
            $item->kodeNomenklatur->details->first()?->id_urusan == $urusanId
        )->values();

        $kepala = $tugas->skpd->skpdKepala->first();
        $kepalaSkpd = $kepala?->user?->userDetail?->nama ?? $kepala?->user?->name ?? '-';

        // Get the user associated with this SKPD for proper navigation
        $skpdUser = User::where('id', $tugas->skpd->user_id)->first();

        // Get active periods
        $periodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->get();

        // Get all periods for the dropdown
        $semuaPeriodeAktif = Periode::with(['tahap', 'tahun'])
            ->where('status', 1)
            ->get();

        // Get current year
        $tahunAktif = null;
        if ($semuaPeriodeAktif->isNotEmpty()) {
            $tahunAktif = $semuaPeriodeAktif->first()->tahun;
        }
        
        // Get funding data for each subkegiatan filtered by active period
        $dataAnggaranTerakhir = [];
        $periodeId = null;
        
        // Get period ID from request if specified
        if ($request->has('periode_id') && $request->periode_id) {
            $periodeId = $request->periode_id;
        }
        // Otherwise use Rencana period ID if active
        elseif ($periodeAktif->isNotEmpty()) {
            $periodeId = $periodeAktif->first()->id;
        }
        
        foreach ($subkegiatanTugas as $tugas) {
            if ($tugas->kodeNomenklatur->jenis_nomenklatur == 4) { // Only get sub kegiatan
                // Find monitoring related to this SKPD tugas
                $monitoring = Monitoring::where('skpd_tugas_id', $tugas->id)
                    ->latest()
                    ->first();
                    
                if ($monitoring) {
                    // Get funding data for this monitoring filtered by period
                    $sumberAnggaranData = [];
                    
                    $monitoringAnggaranQuery = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
                        ->with(['sumberAnggaran']);
                    
                    if ($periodeId) {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) use ($periodeId) {
                            $query->where('kategori', 1) // Category 1 = pokok
                                  ->where('periode_id', $periodeId); // Filter by period
                        }]);
                    } else {
                        $monitoringAnggaranQuery->with(['pagu' => function($query) {
                            $query->where('kategori', 1); // Category 1 = pokok
                        }]);
                    }
                    
                    $monitoringAnggaran = $monitoringAnggaranQuery->get();
                    
                    foreach ($monitoringAnggaran as $anggaran) {
                        if ($anggaran->sumberAnggaran && $anggaran->pagu->isNotEmpty()) {
                            $nama = $anggaran->sumberAnggaran->nama;
                            $dana = $anggaran->pagu->first()->dana ?? 0;
                            $sumberAnggaranData[$nama] = $dana;
                        }
                    }
                    
                    // Save data per SKPD tugas
                    $dataAnggaranTerakhir[$tugas->id] = [
                        'sumber_anggaran' => [
                            'dak' => isset($sumberAnggaranData['dak']),
                            'dak_peruntukan' => isset($sumberAnggaranData['dak_peruntukan']),
                            'dak_fisik' => isset($sumberAnggaranData['dak_fisik']),
                            'dak_non_fisik' => isset($sumberAnggaranData['dak_non_fisik']),
                            'blud' => isset($sumberAnggaranData['blud']),
                        ],
                        'values' => [
                            'dak' => $sumberAnggaranData['dak'] ?? 0,
                            'dak_peruntukan' => $sumberAnggaranData['dak_peruntukan'] ?? 0,
                            'dak_fisik' => $sumberAnggaranData['dak_fisik'] ?? 0,
                            'dak_non_fisik' => $sumberAnggaranData['dak_non_fisik'] ?? 0,
                            'blud' => $sumberAnggaranData['blud'] ?? 0,
                        ]
                    ];
                }
            }
        }

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'user' => [
                'id' => $skpdUser?->id ?? $tugas->skpd_id, // Use user ID instead of skpd_id
                'nama_skpd' => $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas,
                'skpd_id' => $tugas->skpd_id // Keep skpd_id for other purposes
            ],
            'periodeAktif' => $periodeAktif,
            'tahunAktif' => $tahunAktif,
            'semuaPeriodeAktif' => $semuaPeriodeAktif,
            'dataAnggaranTerakhir' => $dataAnggaranTerakhir,
            'bidangUrusanList' => $bidangurusanTugas,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'sumber_dana' => 'required|string|max:255',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|digits:4|integer',
            'deskripsi' => 'required|string',
            'pagu_pokok' => 'required|integer',
            'pagu_parsial' => 'nullable|integer',
            'pagu_perubahan' => 'nullable|integer'
        ]);

        $monitoring = Monitoring::findOrFail($id);
        $monitoring->update($validated);

        return response()->json([
            'success' => true,
            'monitoring' => $monitoring
        ]);
    }

    public function saveMonitoringData(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'sumber_dana' => 'required|string|max:255',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|digits:4|integer',
            'deskripsi' => 'required|string',
            'pagu_pokok' => 'required|integer',
            'pagu_parsial' => 'nullable|integer',
            'pagu_perubahan' => 'nullable|integer',
            'pokok' => 'required|string',
            'parsial' => 'required|string',
            'perubahan' => 'nullable|string',
            'targets' => 'required|array',
            'targets.*.kinerja_fisik' => 'required|numeric',
            'targets.*.keuangan' => 'required|numeric',
        ]);

        $monitoring = Monitoring::create([
            'skpd_id' => $validated['skpd_id'],
            'sumber_dana' => $validated['sumber_dana'],
            'periode_id' => $validated['periode_id'],
            'tahun' => $validated['tahun'],
            'deskripsi' => $validated['deskripsi'],
            'pagu_pokok' => $validated['pagu_pokok'],
            'pagu_parsial' => $validated['pagu_parsial'],
            'pagu_perubahan' => $validated['pagu_perubahan'],
            'pokok' => $validated['pokok'],
            'parsial' => $validated['parsial'],
            'perubahan' => $validated['perubahan'] ?? null,
        ]);

        foreach ($validated['targets'] as $target) {
            $monitoring->targets()->create([
                'kinerja_fisik' => $target['kinerja_fisik'],
                'keuangan' => $target['keuangan'],
            ]);
        }

        return back()->with('success', 'Data monitoring berhasil disimpan.');
    }

    public function showMonitoringDetails($id)
    {
        $monitoring = Monitoring::with([
            'skpd',
            'periode',
            'targets',
            'realisasi'
        ])->findOrFail($id);

        return Inertia::render('Monitoring/Details', [
            'monitoring' => $monitoring,
            'sumber_dana' => $monitoring->sumber_dana,
            'pagu_pokok' => $monitoring->pagu_pokok,
            'pagu_parsial' => $monitoring->pagu_parsial,
            'pagu_perubahan' => $monitoring->pagu_perubahan,
            'targets' => $monitoring->targets,
            'realisasi' => $monitoring->realisasi,
        ]);
    }
    
    public function saveSumberDana(Request $request)
    {
        // Cek apakah ada periode yang aktif (status 1 = buka) dengan tahap "Rencana"
        $aktivPeriode = Periode::where('status', 1)
            ->whereHas('tahap', function($query) {
                $query->where('tahap', 'Rencana');
            })
            ->first();
        
        if (!$aktivPeriode) {
            return back()->withErrors(['message' => 'Periode Rencana belum dibuka. Sumber dana hanya dapat diisi pada periode Rencana.']);
        }

        $validated = $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'sumber_anggaran' => 'required|array',
            'sumber_anggaran.dak' => 'required|boolean',
            'sumber_anggaran.dak_peruntukan' => 'required|boolean',
            'sumber_anggaran.dak_fisik' => 'required|boolean',
            'sumber_anggaran.dak_non_fisik' => 'required|boolean',
            'sumber_anggaran.blud' => 'required|boolean',
            'values' => 'required|array',
            'values.dak' => 'required|numeric',
            'values.dak_peruntukan' => 'required|numeric',
            'values.dak_fisik' => 'required|numeric',
            'values.dak_non_fisik' => 'required|numeric',
            'values.blud' => 'required|numeric',
        ]);
        
        // Menggunakan transaksi database untuk memastikan konsistensi data
        DB::beginTransaction();
        
        try {
            // Cari atau buat monitoring baru
            $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);
            
            // Cek apakah monitoring sudah ada
            $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
                ->where('tahun', date('Y'))
                ->first();
                
            // Jika belum ada, buat baru
            if (!$monitoring) {
                $monitoring = new Monitoring();
                $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
                $monitoring->tahun = date('Y');
                $monitoring->deskripsi = 'Monitoring anggaran ' . $skpdTugas->kodeNomenklatur->nomenklatur;
                $monitoring->nama_pptk = '-';
                $monitoring->save();
            }
            
            // Gunakan periode aktif yang sudah ditemukan di atas
            $periode = $aktivPeriode;
            
            // Simpan sumber anggaran yang dipilih
            foreach ($validated['sumber_anggaran'] as $key => $value) {
                if ($value) {
                    // Cari ID sumber anggaran berdasarkan key
                    $sumberAnggaran = SumberAnggaran::where('nama', $key)->first();
                    
                    if (!$sumberAnggaran) {
                        // Jika tidak ada, buat sumber anggaran baru menggunakan query builder
                        // karena tabel sumber_anggaran tidak memiliki kolom created_at dan updated_at
                        $sumberAnggaranId = DB::table('sumber_anggaran')->insertGetId([
                            'nama' => $key
                        ]);
                        
                        // Buat objek sederhana untuk digunakan selanjutnya
                        $sumberAnggaran = (object)[
                            'id' => $sumberAnggaranId,
                            'nama' => $key
                        ];
                    }
                    
                    // Cari atau buat monitoring_anggaran
                    $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                        'monitoring_id' => $monitoring->id,
                        'sumber_anggaran_id' => $sumberAnggaran->id
                    ]);
                    
                    // Simpan pagu anggaran dengan kategori = 1 (pokok)
                    // Kategori: 1 = pokok, 2 = parsial, 3 = perubahan
                    
                    // Cek apakah monitoring_pagu sudah ada untuk periode ini
                    $monitoringPagu = MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                        ->where('periode_id', $periode->id)
                        ->where('kategori', 1) // 1 = pokok
                        ->first();
                        
                    if ($monitoringPagu) {
                        // Update jika sudah ada
                        $monitoringPagu->dana = (int)$validated['values'][$key];
                        $monitoringPagu->save();
                    } else {
                        // Buat baru jika belum ada
                        $monitoringPagu = new MonitoringPagu();
                        $monitoringPagu->monitoring_anggaran_id = $monitoringAnggaran->id;
                        $monitoringPagu->periode_id = $periode->id;
                        $monitoringPagu->kategori = 1; // 1 = pokok
                        $monitoringPagu->dana = (int)$validated['values'][$key];
                        $monitoringPagu->save();
                    }
                }
            }
            
            // Tidak perlu update total pagu karena kolom pagu_anggaran tidak ada di tabel monitoring
            
            DB::commit();
            
            return back()->with('success', 'Data sumber anggaran berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}