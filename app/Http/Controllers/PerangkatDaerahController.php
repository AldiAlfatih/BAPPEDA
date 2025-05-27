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

        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);

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
            'nama_dinas' => 'required|string|max:255',
            'no_dpa' => 'required|string|max:255',
            'kode_organisasi' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'operator_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);
        $operator = User::find($validated['operator_id']);

        $skpd = Skpd::create([
            'user_id' => $validated['user_id'],
            'nama_skpd' => $user->name,
            'nama_operator' => $operator->name,
            'nama_dinas' => $validated['nama_dinas'],
            'no_dpa' => $validated['no_dpa'],
            'kode_organisasi' => $validated['kode_organisasi'],
        ]);

        SkpdKepala::create([
            'skpd_id' => $skpd->id,
            'user_id' => $validated['user_id'],
            'is_aktif' => 1,
        ]);

        TimKerja::create([
            'skpd_id' => $skpd->id,
            'user_id' => $validated['operator_id'],
            'is_aktif' => 1,
        ]);

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil disimpan.');
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

        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

        return Inertia::render('PerangkatDaerah/Show', [
            'user' => $user,
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
        ]);
    }

    public function edit(string $user_id)
    {
        $skpd = Skpd::with(['user', 'skpdKepala' => function($query) {
            $query->where('is_aktif', 1);
        }, 'timKerja' => function($query) {
            $query->where('is_aktif', 1);
        }])->where('user_id', $user_id)->firstOrFail();

        $users = User::role('perangkat_daerah')->get();
        $operators = User::role('operator')->get();

        $currentOperator = TimKerja::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->first();

        return Inertia::render('PerangkatDaerah/Edit', [
            'skpd' => [
                'id' => $skpd->id,
                'user_id' => $skpd->user_id,
                'nama_skpd' => $skpd->nama_skpd,
                'nama_operator' => $skpd->nama_operator,
                'nama_dinas' => $skpd->nama_dinas,
                'no_dpa' => $skpd->no_dpa,
                'kode_organisasi' => $skpd->kode_organisasi,
            ],
            'users' => $users,
            'operators' => $operators,
            'current_operator_id' => $currentOperator?->user_id,
        ]);
    }


    public function update(Request $request, string $id)
    {
        $skpd = Skpd::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'operator_id' => 'required|exists:users,id',
            'nama_dinas' => 'required|string|max:255',
            'no_dpa' => 'required|string|max:255',
            'kode_organisasi' => 'required|string|max:255',
        ]);

        $user = User::find($validated['user_id']);
        $operator = User::find($validated['operator_id']);

        $skpd->update([
            'user_id' => $validated['user_id'],
            'nama_skpd' => $user->name,
            'nama_operator' => $operator->name,
            'nama_dinas' => $validated['nama_dinas'],
            'no_dpa' => $validated['no_dpa'],
            'kode_organisasi' => $validated['kode_organisasi'],
        ]);

        // Update Kepala SKPD
        $currentKepala = SkpdKepala::where('skpd_id', $skpd->id)->where('is_aktif', 1)->first();
        if (!$currentKepala || $currentKepala->user_id != $validated['user_id']) {
            // Deactivate current kepala
            if ($currentKepala) {
                $currentKepala->update(['is_aktif' => 0]);
            }
            // Create new kepala
            SkpdKepala::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['user_id'],
                'is_aktif' => 1,
            ]);
        }

        // Update Operator (Fixed the syntax error here)
        $currentOperator = TimKerja::where('skpd_id', $skpd->id)->where('is_aktif', 1)->first();
        if (!$currentOperator || $currentOperator->user_id != $validated['operator_id']) {
            // Deactivate current operator
            if ($currentOperator) {
                $currentOperator->update(['is_aktif' => 0]);
            }
            // Create new operator
            TimKerja::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['operator_id'],
                'is_aktif' => 1,
            ]);
        }

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $skpd = Skpd::findOrFail($id);
        
        // Soft delete related records
        SkpdKepala::where('skpd_id', $id)->update(['is_aktif' => 0]);
        TimKerja::where('skpd_id', $id)->update(['is_aktif' => 0]);
        SkpdTugas::where('skpd_id', $id)->update(['is_aktif' => 0]);
        
        $skpd->delete();

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil dihapus.');
    }
}