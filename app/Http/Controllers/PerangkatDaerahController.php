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
    public function index(Request $request)
    {
        // Force log to file immediately
        file_put_contents(storage_path('logs/debug.log'),
            "[" . date('Y-m-d H:i:s') . "] PerangkatDaerahController.index CALLED\n",
            FILE_APPEND | LOCK_EX
        );

        \Log::info('PerangkatDaerahController.index START', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_authenticated' => auth()->check()
        ]);

        $user = auth()->user();



        \Log::info('PerangkatDaerahController.index called', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->getRoleNames(),
            'has_perangkat_daerah_role' => $user->hasRole('perangkat_daerah')
        ]);

        if ($user->hasRole('perangkat_daerah')) {
            \Log::info('User has perangkat_daerah role, redirecting to show', [
                'redirect_to' => route('perangkatdaerah.show', $user->id)
            ]);
            return redirect()->route('perangkatdaerah.show', $user->id);
        }

        $query = User::role('perangkat_daerah')
            ->with([
                'skpd.kepalaAktif.user.userDetail',
                'skpd.operatorAktif.operator.userDetail'
            ]);

        \Log::info('PerangkatDaerahController.index query built', [
            'base_query_count' => User::role('perangkat_daerah')->count()
        ]);

        // Tambahkan filter search jika ada
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('skpd', function($subQ) use ($searchTerm) {
                    $subQ->where('nama_skpd', 'like', '%' . $searchTerm . '%')
                         ->orWhere('kode_organisasi', 'like', '%' . $searchTerm . '%')
                         ->orWhere('no_dpa', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('skpd.kepalaAktif.user', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('skpd.operatorAktif.operator', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $users = $query->paginate(1000);

        \Log::info('PerangkatDaerahController.index users fetched', [
            'users_count' => $users->count(),
            'users_total' => $users->total(),
            'first_user_id' => $users->count() > 0 ? $users->first()->id : null
        ]);

        $users->getCollection()->transform(function ($user) {
            $skpd = $user->skpd->first();
            $operatorName = null;
            $kepalaName = null;

            if ($skpd) {
                // Ambil nama operator yang aktif
                if ($skpd->operatorAktif && $skpd->operatorAktif->operator) {
                    $operatorName = $skpd->operatorAktif->operator->name;
                }

                // Ambil nama kepala daerah yang aktif
                if ($skpd->kepalaAktif && $skpd->kepalaAktif->user) {
                    $kepalaName = $skpd->kepalaAktif->user->name;
                }
            }

            $user->operator_name = $operatorName;
            $user->kepala_name = $kepalaName;
            return $user;
        });



        \Log::info('PerangkatDaerahController.index rendering view', [
            'final_users_count' => $users->count(),
            'search_filter' => $request->search
        ]);

        return Inertia::render('PerangkatDaerah', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    public function create()
    {
        \Log::info('PerangkatDaerahController.create called', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
            'user_roles' => auth()->user()->getRoleNames()
        ]);

        $users = User::role('perangkat_daerah')->get();
        $operators = User::role('operator')->get();

        \Log::info('PerangkatDaerahController.create data', [
            'users_count' => $users->count(),
            'operators_count' => $operators->count()
        ]);

        return Inertia::render('PerangkatDaerah/Create', [
            'users' => $users,
            'operators' => $operators,
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('PerangkatDaerahController.store called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown'
        ]);

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
            'nama_skpd' => $validated['nama_dinas'] ?? 'SKPD',
            'nama_dinas' => $validated['nama_dinas'],
            'kode_organisasi' => $validated['kode_organisasi'],
            'no_dpa' => $validated['no_dpa'],
        ]);

        SkpdKepala::create([
            'skpd_id' => $skpd->id,
            'user_id' => $validated['user_id'],
            'is_aktif' => 1
        ]);

        TimKerja::create([
            'skpd_id' => $skpd->id,
            'operator_id' => $validated['operator_id'],
            'is_aktif' => 1,
        ]);

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil disimpan.');
    }

    public function show(string $id)
    {
        $user = User::with(['skpd.kepala.user.userDetail', 'skpd.operatorAktif.operator.userDetail'])->findOrFail($id);

        $userSkpd = $user->skpd->first();

        $kepalaSkpd = null;
        $nipKepala = null;
        $operatorSkpd = null;
        $nipOperator = null;

        if ($userSkpd) {
            // Get Kepala SKPD data
            $kepala = $userSkpd->kepala()->with(['user.userDetail'])->latest()->first();
            if ($kepala && $kepala->user) {
                $kepalaSkpd = $kepala->user;
                $nipKepala = $kepala->user->userDetail->nip ?? null;
            }

            // Get Operator data
            if ($userSkpd->operatorAktif && $userSkpd->operatorAktif->operator) {
                $operatorSkpd = $userSkpd->operatorAktif->operator;
                $nipOperator = $operatorSkpd->userDetail->nip ?? null;
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
                'nama_dinas' => $userSkpd->nama_dinas ?? $userSkpd->nama_skpd ?? 'Tidak tersedia',
                'kode_organisasi' => $userSkpd->kode_organisasi ?? 'Tidak tersedia',
                'no_dpa' => $userSkpd->no_dpa ?? 'Tidak tersedia',
                'nama_operator' => $operatorSkpd->name ?? 'Tidak tersedia',
                'nip_operator' => $nipOperator ?? '-',
                'nama_kepala_skpd' => $kepalaSkpd->name ?? 'Tidak tersedia',
                'nip_kepala_skpd' => $nipKepala ?? '-',
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
        $kepala = SkpdKepala::where('user_id', $user_id)->first();

        if (!$kepala) {
            abort(404, 'SKPD not found for this user');
        }

        $skpd = Skpd::with(['kepala', 'timKerja.operator'])
            ->findOrFail($kepala->skpd_id);

        $users = User::role('perangkat_daerah')->get();
        $operators = User::role('operator')->get();

        // Get current operator from TimKerja
        $timKerja = TimKerja::with('operator')
            ->where('skpd_id', $skpd->id)
            ->latest()
            ->first();

        // Get operator name for display
        $operatorName = $timKerja?->operator?->name;

        return Inertia::render('PerangkatDaerah/Edit', [
            'skpd' => [
                'id' => $skpd->id,
                'user_id' => $kepala->user_id,
                'nama_skpd' => $skpd->nama_skpd,
                'kode_organisasi' => $skpd->kode_organisasi,
                'nama_operator' => $operatorName,
                'nama_dinas' => $skpd->nama_dinas,
                'no_dpa' => $skpd->no_dpa,
            ],
            'users' => $users,
            'operators' => $operators,
            'current_operator_id' => $timKerja?->operator_id,
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

        // Update SKPD
        $skpd->update([
            'kode_organisasi' => $validated['kode_organisasi'],
            'nama_dinas' => $validated['nama_dinas'],
            'no_dpa' => $validated['no_dpa'],
        ]);

        // === SKPD KEPALA ===
        $currentKepala = SkpdKepala::where('skpd_id', $skpd->id)->where('is_aktif', 1)->first();
        if (!$currentKepala || $currentKepala->user_id != $validated['user_id']) {
            // Nonaktifkan kepala lama
            if ($currentKepala) {
                $currentKepala->is_aktif = 0;
                $currentKepala->save();
            }
            // Tambah kepala baru
            SkpdKepala::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['user_id'],
                'is_aktif' => 1,
            ]);
        }

        // === OPERATOR ===
        $currentOperator = TimKerja::where('skpd_id', $skpd->id)->where('is_aktif', 1)->first();
        if (!$currentOperator || $currentOperator->operator_id != $validated['operator_id']) {
            // Nonaktifkan operator lama
            if ($currentOperator) {
                $currentOperator->is_aktif = 0;
                $currentOperator->save();
            }
            // Tambah operator baru
            TimKerja::create([
                'skpd_id' => $skpd->id,
                'operator_id' => $validated['operator_id'],
                'is_aktif' => 1,
            ]);
        }

        return redirect()
            ->route('perangkatdaerah.index')
            ->with('success', 'Data SKPD berhasil diperbarui.');
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
