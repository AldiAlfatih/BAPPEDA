<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\Skpd;
use App\Models\Periode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index()
    {
    $user = auth()->user();

    if ($user->hasRole('perangkat_daerah')) {
        return redirect()->route('monitoring.show', $user->id);}

        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);

        return Inertia::render('Monitoring', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $skpds = Skpd::all();
        $periodes = Periode::all();
        return view('monitoring.create', compact('skpds', 'periodes'));
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
                   'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null
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
                   'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null
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
                   'program_id' => $item->details->first() ? $item->details->first()->id_program : null
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
                   'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null
               ];
           });

       $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
           ->where('is_aktif', 1)
           ->with('kodeNomenklatur')
           ->get();

       return Inertia::render('Monitoring/Show', [
           'user' => $user,
           'skpdTugas' => $skpdTugas,
           'urusanList' => $urusanList,
           'bidangUrusanList' => $bidangUrusanList,
           'programList' => $programList,
           'kegiatanList' => $kegiatanList,
           'subkegiatanList' => $subkegiatanList,
       ]);
   }

    // public function rencanaAwal($id)
    // {
    //     $tugas = SkpdTugas::with('kodeNomenklatur')->findOrFail($id);
    //     return Inertia::render('Monitoring/RencanaAwal', [
    //         'tugas' => $tugas,
    //     ]);
    // }
    // public function edit($id)
    // {
    //     $monitoring = Monitoring::findOrFail($id);
    //     $skpds = Skpd::all();
    //     $periodes = Periode::all();
    //     return view('monitoring.edit', compact('monitoring', 'skpds', 'periodes'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $monitoring = Monitoring::findOrFail($id);

    //     $validated = $request->validate([
    //         'skpd_id' => 'required|exists:skpd,id',
    //         'sumber_dana' => 'required|string|max:255',
    //         'periode_id' => 'nullable|exists:periode,id',
    //         'tahun' => 'required|digits:4|integer',
    //         'deskripsi' => 'required|string',
    //         'pagu_anggaran' => 'required|integer',
    //     ]);

    //     $monitoring->update($validated);

    //     return redirect()->route('monitoring.index')->with('success', 'Data monitoring berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     $monitoring = Monitoring::findOrFail($id);
    //     $monitoring->delete();

    //     return redirect()->route('monitoring.index')->with('success', 'Data monitoring berhasil dihapus.');
    // }
}
