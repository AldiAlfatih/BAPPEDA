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

        return Inertia::render('MonitoringAnggaran/Sumberdana', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
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
        $validated = $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'kategori' => 'required|in:pokok,parsial,perubahan',
            'sumber_anggaran' => 'required|array',
            'sumber_anggaran.dak' => 'required|boolean',
            'sumber_anggaran.dak_peruntukan' => 'required|boolean',
            'sumber_anggaran.dak_fisik' => 'required|boolean',
            'sumber_anggaran.dak_non_fisik' => 'required|boolean',
            'sumber_anggaran.blud' => 'required|boolean',
            'values' => 'required|array',
            'values.dak' => 'required|numeric|min:0',
            'values.dak_peruntukan' => 'required|numeric|min:0',
            'values.dak_fisik' => 'required|numeric|min:0',
            'values.dak_non_fisik' => 'required|numeric|min:0',
            'values.blud' => 'required|numeric|min:0',
        ]);
        
        // Map category to database value
        $kategoriMap = [
            'pokok' => 1,
            'parsial' => 2,
            'perubahan' => 3,
        ];
        $kategori = $kategoriMap[$validated['kategori']];
        
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
                    
                    // Cek apakah monitoring_pagu sudah ada untuk kategori ini
                    $monitoringPagu = MonitoringPagu::firstOrNew([
                        'monitoring_anggaran_id' => $monitoringAnggaran->id,
                        'periode_id' => $periode->id,
                        'kategori' => $kategori
                    ]);
                    
                    // Update nilai dana
                    $monitoringPagu->dana = (int)$validated['values'][$key];
                    $monitoringPagu->save();
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