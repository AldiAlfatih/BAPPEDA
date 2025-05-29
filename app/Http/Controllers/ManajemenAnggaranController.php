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

    public function show(string $id)
    {
        $user = User::with('skpd')->findOrFail($id);

        // Cek apakah ada periode yang aktif (status = 1 = buka)
        $aktivPeriode = Periode::where('status', 1)->first();
        $periodeStatus = $aktivPeriode ? true : false;
        $periodeName = $aktivPeriode ? $aktivPeriode->tahap->tahap . ' ' . $aktivPeriode->tahun->tahun : 'Tidak ada periode aktif';

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

        // Ambil data skpd_tugas
        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();
            
        // Ambil data monitoring dan sumber anggaran yang sudah tersimpan
        $monitoringData = [];
        
        // Ambil semua monitoring untuk skpd ini
        $monitorings = Monitoring::where('tahun', date('Y'))
            ->whereIn('skpd_tugas_id', $skpdTugas->pluck('id'))
            ->with(['monitoringAnggaran.sumberAnggaran', 'monitoringAnggaran.pagu'])
            ->get();
            
        // Susun data untuk setiap skpd_tugas
        foreach ($monitorings as $monitoring) {
            $sumberAnggaranData = [
                'dak' => false,
                'dak_peruntukan' => false,
                'dak_fisik' => false,
                'dak_non_fisik' => false,
                'blud' => false
            ];
            
            $nilaiData = [
                'dak' => 0,
                'dak_peruntukan' => 0,
                'dak_fisik' => 0,
                'dak_non_fisik' => 0,
                'blud' => 0
            ];
            
            // Mapping nama sumber anggaran dari database ke key di frontend
            $sumberAnggaranMap = [
                'DAU' => 'dak',
                'DAU Peruntukan' => 'dak_peruntukan',
                'DAK Fisik' => 'dak_fisik',
                'DAK Non Fisik' => 'dak_non_fisik',
                'BLUD' => 'blud'
            ];
            
            // Ambil data sumber anggaran dan pagu
            foreach ($monitoring->monitoringAnggaran as $anggaran) {
                $sumberNama = $anggaran->sumberAnggaran->nama;
                
                // Konversi nama sumber anggaran dari database ke key di frontend
                $key = $sumberAnggaranMap[$sumberNama] ?? null;
                
                if ($key) {
                    $sumberAnggaranData[$key] = true;
                    
                    // Ambil nilai pagu (kategori 1 = pokok)
                    $pagu = $anggaran->pagu->where('kategori', 1)->first();
                    if ($pagu) {
                        $nilaiData[$key] = $pagu->dana;
                    }
                }
            }
            
            $monitoringData[$monitoring->skpd_tugas_id] = [
                'sumber_anggaran' => $sumberAnggaranData,
                'nilai' => $nilaiData
            ];
        }

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'periodeStatus' => $periodeStatus,
            'periodeName' => $periodeName,
            'monitoringData' => $monitoringData,
        ]);
    }

    public function showRencanaAwal($id)
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

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'user' => [
                'id' => $skpdUser?->id ?? $tugas->skpd_id, // Use user ID instead of skpd_id
                'nama_skpd' => $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas,
                'skpd_id' => $tugas->skpd_id // Keep skpd_id for other purposes
            ]
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
        // Cek terlebih dahulu apakah ada periode yang aktif
        $aktivPeriode = Periode::where('status', 1)->first();
        if (!$aktivPeriode) {
            return back()->withErrors(['message' => 'Tidak dapat menyimpan data. Tidak ada periode yang aktif saat ini.']);
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
                $monitoring->deskripsi = null; // Biarkan deskripsi NULL, akan diisi nanti di triwulan
                $monitoring->nama_pptk = '-';
                $monitoring->save();
            }
            
            // Dapatkan periode aktif (status 1 = aktif/buka)
            $periode = Periode::where('status', 1)->first();
            if (!$periode) {
                // Jika tidak ada periode aktif, ambil periode terbaru saja
                $periode = Periode::latest()->first();
                
                if (!$periode) {
                    throw new \Exception('Tidak ada periode tersedia saat ini');
                }
            }
            
            // Simpan sumber anggaran yang dipilih
            foreach ($validated['sumber_anggaran'] as $key => $value) {
                if ($value) {
                    // Gunakan mapping ID untuk sumber anggaran yang sudah ada
                    $sumberAnggaranMap = [
                        'dak' => 1,      // DAU di database
                        'dak_peruntukan' => 5, // DAU Peruntukan di database
                        'dak_fisik' => 2,  // DAK Fisik di database
                        'dak_non_fisik' => 3, // DAK Non Fisik di database
                        'blud' => 4      // BLUD di database
                    ];
                    
                    // Dapatkan ID sumber anggaran dari mapping
                    if (isset($sumberAnggaranMap[$key])) {
                        $sumberAnggaranId = $sumberAnggaranMap[$key];
                        
                        // Ambil dari database jika ada
                        $sumberAnggaran = DB::table('sumber_anggaran')->where('id', $sumberAnggaranId)->first();
                        
                        // Jika tidak ada, buat objek sederhana dengan ID yang benar
                        if (!$sumberAnggaran) {
                            $sumberAnggaran = (object)[
                                'id' => $sumberAnggaranId,
                                'nama' => ucfirst(str_replace('_', ' ', $key))
                            ];
                        }
                    } else {
                        // Fallback jika tidak ada dalam mapping (seharusnya tidak terjadi)
                        throw new \Exception('Sumber anggaran tidak valid: ' . $key);
                    }
                    
                    // Cari atau buat monitoring_anggaran
                    $monitoringAnggaran = MonitoringAnggaran::firstOrCreate([
                        'monitoring_id' => $monitoring->id,
                        'sumber_anggaran_id' => $sumberAnggaran->id
                    ]);
                    
                    // Simpan pagu anggaran dengan kategori = 1 (pokok)
                    // Kategori: 1 = pokok, 2 = parsial, 3 = perubahan
                    
                    // Cek apakah monitoring_pagu sudah ada
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