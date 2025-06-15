<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\TimKerja;
use App\Models\KodeNomenklatur;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PerangkatDaerahController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('perangkatdaerah.show', $user->id);
        }

        $users = User::role('perangkat_daerah')->with(['skpd'])->paginate(1000);
        $users->getCollection()->transform(function ($user) {
            $skpd = $user->skpd->first();
            $operatorName = null;
            if ($skpd) {
                $timKerja = \App\Models\TimKerja::with('operator')->where('skpd_id', $skpd->id)->first();
                $operatorName = $timKerja?->operator?->name;
            }
            $user->operator_name = $operatorName;
            return $user;
        });

        return Inertia::render('PerangkatDaerah', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $users = User::role('perangkat_daerah')->get();
        $operators = User::role('operator')->get();  

        return Inertia::render('PerangkatDaerah/Create', [
            'users' => $users,
            'operators' => $operators,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_organisasi' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'operator_id' => 'required|exists:users,id',
            'nama_dinas' => 'nullable|string|max:255',
            'no_dpa' => 'nullable|string|max:255',
        ]);

        $user = User::find($validated['user_id']);
        $operator = User::find($validated['operator_id']);

        $skpd = Skpd::create([
            'nama_skpd' => $validated['nama_skpd'] ?? $validated['nama_dinas'],
            'kode_organisasi' => $validated['kode_organisasi'],
        ]);

        SkpdKepala::create([
            'skpd_id' => $skpd->id,
            'user_id' => $validated['user_id']
        ]);

        TimKerja::create([
            'skpd_id' => $skpd->id,
            'operator_id' => $validated['operator_id'],
        ]);

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil disimpan.');
    }

    public function show(string $id)
    {
        $user = User::with(['skpd.skpdKepala.user.userDetail'])->findOrFail($id);

        $userSkpd = $user->skpd->first();
        
        $kepalaSkpd = null;
        $nipKepala = null;
        if ($userSkpd) {
            $skpdKepala = $userSkpd->skpdKepala()->with(['user.userDetail'])->latest()->first();
            if ($skpdKepala && $skpdKepala->user) {
                $kepalaSkpd = $skpdKepala->user;
                $nipKepala = $skpdKepala->user->userDetail->nip ?? null;
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
                    'urusan_id' => $item->details->first()?->id_urusan,
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
                    'bidang_urusan_id' => $item->details->first()?->id_bidang_urusan,
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
                    'program_id' => $item->details->first()?->id_program,
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
                    'kegiatan_id' => $item->details->first()?->id_kegiatan,
                ];
            });

        // Make sure we have a valid SKPD before proceeding
        if ($userSkpd) {
            $skpdTugas = SkpdTugas::where('skpd_id', $userSkpd->id)
                ->with('kodeNomenklatur')
                ->get();
        } else {
            $skpdTugas = collect();
        }

        return Inertia::render('PerangkatDaerah/Show', [
            'user' => $user,
            'skpd' => [
                'id' => $userSkpd->id ?? null,
                'nama_skpd' => $userSkpd->nama_skpd ?? null,
                'nama_dinas' => $userSkpd->nama_dinas ?? null,
                'kode_organisasi' => $userSkpd->kode_organisasi ?? null,
                'no_dpa' => $userSkpd->no_dpa ?? null,
                'kepala_skpd' => $kepalaSkpd ? [
                    'id' => $kepalaSkpd->id,
                    'name' => $kepalaSkpd->name,
                    'nip' => $nipKepala
                ] : null
            ],
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'skpdTugas' => $skpdTugas
        ]);
    }

    public function edit(string $user_id)
    {
        // Get the SKPD through the SkpdKepala relationship since the skpd table doesn't have user_id
        $skpdKepala = SkpdKepala::where('user_id', $user_id)->first();
        
        if (!$skpdKepala) {
            abort(404, 'SKPD not found for this user');
        }
        
        $skpd = Skpd::with(['skpdKepala', 'timKerja'])
            ->findOrFail($skpdKepala->skpd_id);

        $users = User::role('perangkat_daerah')->get();
        $operators = User::role('operator')->get();

        // $currentOperator = TimKerja::where('skpd_id', $skpd->id)
        //     ->where('is_aktif', 1)
        //     ->first();

        return Inertia::render('PerangkatDaerah/Edit', [
            'skpd' => [
                'id' => $skpd->id,
                'user_id' => $skpdKepala->user_id, 
                'nama_skpd' => $skpd->nama_skpd,
                'kode_organisasi' => $skpd->kode_organisasi,
                'nama_operator' => null,
                'nama_dinas' => null,
                'no_dpa' => null,
            ],
            'users' => $users,
            'operators' => $operators,
            // 'current_operator_id' => $currentOperator?->operator_id,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $skpd = Skpd::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'operator_id' => 'required|exists:users,id',
            'kode_organisasi' => 'required|string|max:255',
            'nama_dinas' => 'nullable|string|max:255',
            'no_dpa' => 'nullable|string|max:255',
        ]);

        $user = User::find($validated['user_id']);

        $skpd->update([
            'nama_skpd' => $validated['nama_skpd'] ?? $validated['nama_dinas'],
            'kode_organisasi' => $validated['kode_organisasi'],
        ]);

        $currentKepala = SkpdKepala::where('skpd_id', $skpd->id)
                               ->where('user_id', $validated['user_id'])
                               ->first();
        
        if (!$currentKepala) {
            SkpdKepala::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['user_id']
            ]);
        }

        // Update Operator 
        // $currentOperator = TimKerja::where('skpd_id', $skpd->id)
        //                        ->where('is_aktif', 1)
        //                        ->first();
        // if (!$currentOperator || $currentOperator->operator_id != $validated['operator_id']) {
        //     // Deactivate current operator
        //     if ($currentOperator) {
        //         $currentOperator->update(['is_aktif' => 0]);
        //     }
        //     // Create new operator
        //     TimKerja::create([
        //         'skpd_id' => $skpd->id,
        //         'operator_id' => $validated['operator_id'],
        //         'is_aktif' => 1,
        //     ]);
        // }

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $skpd = Skpd::findOrFail($id);
        
        SkpdKepala::where('skpd_id', $id)->delete(); 
        TimKerja::where('skpd_id', $id)->update(['is_aktif' => 0]);
        SkpdTugas::where('skpd_id', $id)->update(['is_aktif' => 0]);
        
        $skpd->delete();

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil dihapus.');
    }
}