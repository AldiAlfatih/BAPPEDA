<?php

namespace App\Http\Controllers;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\KodeNomenklatur;
use App\Models\SkpdTugas;
use App\Models\TimKerja;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('monitoring.show', $user->id);
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

        return Inertia::render('Monitoring', [
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

        return Inertia::render('Monitoring/Show', [
            'user' => $userData,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'skpdTugas' => $skpdTugas
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
}
