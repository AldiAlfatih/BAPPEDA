<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\Skpd;
use App\Models\TimKerja;
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

        $query = User::role('perangkat_daerah')
            ->with(['skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            }]);

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

        // Transform the data to include all required information
        $users->getCollection()->transform(function ($user) {
            $skpd = $user->skpd->first();
            $operatorName = null;
            $kepalaName = null;
            $namaDinas = null;
            $kodeOrganisasi = null;

            if ($skpd) {
                // Get operator name from operatorAktif relation
                if ($skpd->operatorAktif && $skpd->operatorAktif->operator) {
                    $operatorName = $skpd->operatorAktif->operator->name;
                }
                
                // Get kepala name from kepalaAktif relation
                if ($skpd->kepalaAktif && $skpd->kepalaAktif->user) {
                    $kepalaName = $skpd->kepalaAktif->user->name;
                }

                // Get SKPD details
                $namaDinas = $skpd->nama_skpd;
                $kodeOrganisasi = $skpd->kode_organisasi;
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'nama_dinas' => $namaDinas,
                'operator_name' => $operatorName,
                'kepala_name' => $kepalaName,
                'kode_organisasi' => $kodeOrganisasi
            ];
        });

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
        $user = User::with([
            'skpd' => function($q) {
                $q->with(['kepalaAktif.user', 'operatorAktif.operator']);
            },
            'userDetail'
        ])->findOrFail($id);

        $skpd = $user->skpd->first();
        $operatorName = null;
        $kepalaName = null;
        
        if ($skpd) {
            if ($skpd->operatorAktif && $skpd->operatorAktif->operator) {
                $operatorName = $skpd->operatorAktif->operator->name;
            }
            if ($skpd->kepalaAktif && $skpd->kepalaAktif->user) {
                $kepalaName = $skpd->kepalaAktif->user->name;
            }
        }

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

        // Format skpd data
        $skpdData = null;
        if ($skpd) {
            $skpdData = [
                'id' => $skpd->id,
                'nama_skpd' => $skpd->nama_skpd,
                'kode_organisasi' => $skpd->kode_organisasi,
                'operator_name' => $operatorName,
                'kepala_name' => $kepalaName
            ];
        }

        return Inertia::render('ManajemenAnggaran/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->userDetail?->nip
            ],
            'skpd' => $skpdData,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            // ...rest of your data
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
                            $key = $this->reverseMapNamaSumberAnggaran($anggaran->sumberAnggaran->nama);
                            if ($key) {
                                $sumberAnggaranData[$key] = $anggaran->pagu->first()->dana ?? 0;
                            }
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

    DB::beginTransaction();

    try {
        $skpdTugas = SkpdTugas::findOrFail($validated['skpd_tugas_id']);

        $monitoring = Monitoring::where('skpd_tugas_id', $validated['skpd_tugas_id'])
            ->where('tahun', date('Y'))
            ->first();

        if (!$monitoring) {
            $monitoring = new Monitoring();
            $monitoring->skpd_tugas_id = $validated['skpd_tugas_id'];
            $monitoring->tahun = date('Y');
            $monitoring->deskripsi = 'Monitoring anggaran ' . $skpdTugas->kodeNomenklatur->nomenklatur;
            $monitoring->nama_pptk = '-';
            $monitoring->save();
        }

        $periode = $aktivPeriode;

        foreach ($validated['sumber_anggaran'] as $key => $value) {
            if ($value) {
                // Cari sumber anggaran yang sudah ada, **jangan buat baru jika tidak ditemukan**
                $sumberAnggaran = SumberAnggaran::where('nama', $this->mapNamaSumberAnggaran($key))->first();

                if (!$sumberAnggaran) {
                    // Jika sumber anggaran tidak ditemukan, skip proses penyimpanan untuk sumber ini
                    continue;
                }

                // Cari atau buat monitoring_anggaran
                $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                    'monitoring_id' => $monitoring->id,
                    'sumber_anggaran_id' => $sumberAnggaran->id,
                ]);

                // Simpan atau update pagu anggaran kategori pokok
                $monitoringPagu = MonitoringPagu::where('monitoring_anggaran_id', $monitoringAnggaran->id)
                    ->where('periode_id', $periode->id)
                    ->where('kategori', 1)
                    ->first();

                if ($monitoringPagu) {
                    $monitoringPagu->dana = (int)$validated['values'][$key];
                    $monitoringPagu->save();
                } else {
                    $monitoringPagu = new MonitoringPagu();
                    $monitoringPagu->monitoring_anggaran_id = $monitoringAnggaran->id;
                    $monitoringPagu->periode_id = $periode->id;
                    $monitoringPagu->kategori = 1;
                    $monitoringPagu->dana = (int)$validated['values'][$key];
                    $monitoringPagu->save();
                }
            }
        }

        // Hapus monitoring_pagu untuk sumber anggaran yang tidak dipilih di periode ini
        $allMonitoringAnggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)
            ->with('sumberAnggaran')
            ->get();

        foreach ($allMonitoringAnggaran as $anggaran) {
            $nama = $anggaran->sumberAnggaran->nama;

            if (!isset($validated['sumber_anggaran'][$this->reverseMapNamaSumberAnggaran($nama)]) ||
                !$validated['sumber_anggaran'][$this->reverseMapNamaSumberAnggaran($nama)]) {
                MonitoringPagu::where('monitoring_anggaran_id', $anggaran->id)
                    ->where('periode_id', $periode->id)
                    ->where('kategori', 1)
                    ->delete();
            }
        }

        DB::commit();

        return back()->with('success', 'Data sumber anggaran berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

/**
 * Mapping key sumber_anggaran dari input ke nama di database sumber_anggaran
 * Contoh: 'dak' => 'DAK Fisik', dsb.
 */
private function mapNamaSumberAnggaran(string $key): string
{
    $mapping = [
        'dak' => 'DAU',
        'dak_peruntukan' => 'DAU Peruntukan',
        'dak_fisik' => 'DAK Fisik',
        'dak_non_fisik' => 'DAK Non Fisik',
        'blud' => 'BLUD',
    ];

    return $mapping[$key] ?? $key;
}

/**
 * Kebalikan dari fungsi mapNamaSumberAnggaran untuk validasi penghapusan
 */
private function reverseMapNamaSumberAnggaran(string $nama): string
{
    $reverseMapping = [
        'DAU' => 'dak',
        'DAU Peruntukan' => 'dak_peruntukan',
        'DAK Fisik' => 'dak_fisik',
        'DAK Non Fisik' => 'dak_non_fisik',
        'BLUD' => 'blud',
    ];

    return $reverseMapping[$nama] ?? $nama;
}
}
