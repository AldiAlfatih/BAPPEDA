<?php
namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\MonitoringPagu;
use App\Models\MonitoringRealisasi;
use App\Models\MonitoringTarget;
use App\Models\SumberAnggaran;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManajemenAnggaranController extends Controller
{
    public function index()
    {
        $data = Monitoring::with([
            'skpdTugas',
            'periode',
            'monitoringAnggaran.sumberAnggaran',
            'monitoringAnggaran.pagu.periode',
            'monitoringAnggaran.realisasi.periode',
            'monitoringAnggaran.target.periode',
        ])->get();

        return inertia('ManajemenAnggaran', [
            'monitorings' => $data,
        ]);
    }

    public function show($id)
    {
        $monitoring = Monitoring::with([
            'skpdTugas',
            'periode',
            'monitoringAnggaran.sumberAnggaran',
            'monitoringAnggaran.pagu.periode',
            'monitoringAnggaran.realisasi.periode',
            'monitoringAnggaran.target.periode',
        ])->findOrFail($id);

        // return Inertia:render('MonitoringAnggaran', [
        //     'monitoring' => $monitoring,
        // ]);
        return Inertia::render('ManajemenAnggaran', [
            'users' => $monitoring,
        ]); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'skpd_tugas_id' => 'required|exists:skpd_tugas,id',
            'periode_id' => 'nullable|exists:periode,id',
            'tahun' => 'required|integer',
            'deskripsi' => 'required|string',
            'nama_pptk' => 'required|string',
            'anggarans' => 'required|array',
            'anggarans.*.sumber_anggaran_id' => 'required|exists:sumber_anggaran,id',
        ]);

        DB::beginTransaction();

        try {
            $monitoring = Monitoring::create([
                'skpd_tugas_id' => $request->skpd_tugas_id,
                'periode_id' => $request->periode_id,
                'tahun' => $request->tahun,
                'deskripsi' => $request->deskripsi,
                'nama_pptk' => $request->nama_pptk,
            ]);

            foreach ($request->anggarans as $anggaran) {
                $monitoringAnggaran = MonitoringAnggaran::create([
                    'monitoring_id' => $monitoring->id,
                    'sumber_anggaran_id' => $anggaran['sumber_anggaran_id'],
                ]);

                // Pagu
                foreach ($anggaran['pagus'] ?? [] as $pagu) {
                    MonitoringPagu::create([
                        'monitoring_anggaran_id' => $monitoringAnggaran->id,
                        'periode_id' => $pagu['periode_id'] ?? null,
                        'kategori' => $pagu['kategori'],
                        'dana' => $pagu['dana'],
                    ]);
                }

                // Realisasi
                foreach ($anggaran['realisasis'] ?? [] as $realisasi) {
                    MonitoringRealisasi::create([
                        'monitoring_anggaran_id' => $monitoringAnggaran->id,
                        'periode_id' => $realisasi['periode_id'] ?? null,
                        'kinerja_fisik' => $realisasi['kinerja_fisik'],
                        'keuangan' => $realisasi['keuangan'],
                    ]);
                }

                // Target
                foreach ($anggaran['targets'] ?? [] as $target) {
                    MonitoringTarget::create([
                        'monitoring_anggaran_id' => $monitoringAnggaran->id,
                        'periode_id' => $target['periode_id'] ?? null,
                        'kinerja_fisik' => $target['kinerja_fisik'],
                        'keuangan' => $target['keuangan'],
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Monitoring berhasil ditambahkan.'], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $monitoring = Monitoring::findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($monitoring->monitoringAnggaran as $anggaran) {
                $anggaran->pagu()->delete();
                $anggaran->realisasi()->delete();
                $anggaran->target()->delete();
                $anggaran->delete();
            }

            $monitoring->delete();

            DB::commit();
            return response()->json(['message' => 'Monitoring berhasil dihapus.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Opsional: Mendapatkan daftar sumber anggaran
    public function sumberAnggaranList()
    {
        return SumberAnggaran::all();
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
}
