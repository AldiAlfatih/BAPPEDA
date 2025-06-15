<?php
<<<<<<< HEAD
=======

>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Inertia\Inertia;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use App\Models\Monitoring;
use App\Models\MonitoringAnggaran;
use App\Models\Periode;
use App\Models\PeriodeTahap;
use App\Models\PeriodeTahun;
use App\Models\MonitoringRealisasi;
use App\Models\MonitoringPagu;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Support\Facades\Log;

class TriwulanController extends Controller
{
    /**
<<<<<<< HEAD
     * Display a listing of the resource.
     */
    public function index(int $tid, int $tahun = null)
    {
        $user = Auth::user();
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini jika tidak disebutkan

        // Validate triwulan ID
=======
     * Menampilkan daftar resource.
     */
    public function index(int $tid, ?int $tahun = null)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tahun = $tahun ?? date('Y');

        // Validasi triwulan ID
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

<<<<<<< HEAD
        // Get periode information
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
=======
        // Mendapatkan informasi periode tahun
        $periodeTahun = PeriodeTahun::where('tahun', $tahun)->first();
        if (!$periodeTahun) {
            return redirect()->back()->with('error', 'Periode tahun tidak ditemukan.');
        }

        // Mendapatkan periode berdasarkan triwulan dan tahun
        $periode = Periode::where('tahun_id', $periodeTahun->id)
            ->whereHas('tahap', function ($query) use ($tid) {
                $query->where('id', $tid);
            })
            ->first();

>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }

        if ($user->hasRole('perangkat_daerah')) {
<<<<<<< HEAD
            return redirect()->route('triwulan.show', ['tid' => $tid, 'id' => $user->id, 'tahun' => $tahun]);
=======
            return redirect()->route('triwulan.show', [
                'tid' => $tid,
                'id' => $user->id,
                'tahun' => $tahun
            ]);
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
<<<<<<< HEAD
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('Triwulan' . $tid, [
=======

            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with(['skpd', 'monitoring' => function ($query) use ($periode) {
                    $query->where('periode_id', $periode->id);
                }])
                ->paginate(1000);

            return Inertia::render('Triwulan',  [
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                'users' => $users,
                'tid' => $tid,
                'tahun' => $tahun,
                'periode' => $periode,
                'triwulanName' => $this->getTriwulanName($tid),
            ]);
        }

<<<<<<< HEAD
        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);
        return Inertia::render('Triwulan' . $tid, [
=======
        // Filter data untuk admin
        $users = User::role('perangkat_daerah')
            ->with(['skpd', 'monitoring' => function ($query) use ($periode) {
                $query->where('periode_id', $periode->id);
            }])
            ->paginate(1000);

        return Inertia::render('Triwulan', [
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            'users' => $users,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    /**
<<<<<<< HEAD
     * Display the specified resource.
     */
    public function show(int $tid, string $id, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini
=======
     * Menampilkan resource yang dipilih.
     */
    public function show(int $tid, string $id, ?int $tahun = null)
    {
        $tahun = $tahun ?? date('Y');
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

<<<<<<< HEAD
        $skpd = Skpd::with('skpdKepala.user','skpdKepala.user.userDetail','timKerja.user','timKerja.user.userDetail')->findOrFail($id);
        dd($skpd);

=======
        $user = User::with('skpd')->findOrFail($id);
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);

        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }

        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();

        $bidangUrusanList = KodeNomenklatur::where('jenis_nomenklatur', 1)
<<<<<<< HEAD
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan');
            }])
            ->get()
            ->map(function($item) {
=======
            ->with(['details' => function ($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan');
            }])
            ->get()
            ->map(function ($item) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null
                ];
            });

        $programList = KodeNomenklatur::where('jenis_nomenklatur', 2)
<<<<<<< HEAD
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->get()
            ->map(function($item) {
=======
            ->with(['details' => function ($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->get()
            ->map(function ($item) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null
                ];
            });

        $kegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 3)
<<<<<<< HEAD
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_program');
            }])
            ->get()
            ->map(function($item) {
=======
            ->with(['details' => function ($query) {
                $query->select('id', 'id_nomenklatur', 'id_program');
            }])
            ->get()
            ->map(function ($item) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null
                ];
            });

        $subkegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 4)
<<<<<<< HEAD
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_kegiatan');
            }])
            ->get()
            ->map(function($item) {
=======
            ->with(['details' => function ($query) {
                $query->select('id', 'id_nomenklatur', 'id_kegiatan');
            }])
            ->get()
            ->map(function ($item) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'jenis_nomenklatur' => $item->jenis_nomenklatur,
                    'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null
                ];
            });

<<<<<<< HEAD
        $skpdTugas = SkpdTugas::where('skpd_id', $skpd->id)
=======
        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

<<<<<<< HEAD
        // Determine the view based on triwulan
        $viewName = 'Triwulan' . $tid . '/Show';

        return Inertia::render($viewName, [
            'skpd' => $skpd,
=======
        $viewName = $this->getViewName($tid);

        return Inertia::render($viewName, [
            'user' => $user,
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            'skpdTugas' => $skpdTugas,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

<<<<<<< HEAD
    public function showDetail(int $tid, string $id, string $taskId, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

=======
    /**
     * Menampilkan detail resource yang dipilih.
     */
    public function showDetail(int $tid, $id, ?int $tahun = null)
    {
        $tahun = $tahun ?? date('Y');
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }
<<<<<<< HEAD

        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function($query) {
                $query->where('is_aktif', 1);
            },
        ])->findOrFail($taskId);

=======
        $tugas = SkpdTugas::with([
            'kodeNomenklatur',
            'skpd.skpdKepala.user.userDetail',
            'skpd.skpdKepala' => function ($query) {
                $query->where('is_aktif', 1);
            },
        ])->findOrFail($id);
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with([
                'kodeNomenklatur.details',
<<<<<<< HEAD
                'monitoring' => function($query) {
                    $query->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode']);
                }
            ])
            ->get();

        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

        // Get bidang urusan data
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function($query) use ($urusanId) {
=======
                'monitoring' => function ($query) use ($periode) {
                    $query->where('periode_id', $periode->id)
                        ->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode']);
                }
            ])
            ->get();
        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function ($query) use ($urusanId) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                $query->where('id_urusan', $urusanId);
            })
            ->first();

<<<<<<< HEAD
        // Find any monitoring that might have deskripsi for bidang urusan
        $bidangUrusanDeskripsi = '-';
        if ($bidangUrusan) {
            $monitoring = \App\Models\Monitoring::whereHas('skpdTugas', function($query) use ($bidangUrusan) {
                $query->whereHas('kodeNomenklatur', function($query) use ($bidangUrusan) {
                    $query->where('id', $bidangUrusan->id);
                });
            })
            ->select('deskripsi')
            ->first();
=======
        $bidangUrusanDeskripsi = '-';
        if ($bidangUrusan) {
            $monitoring = \App\Models\Monitoring::whereHas('skpdTugas', function ($query) use ($bidangUrusan) {
                $query->whereHas('kodeNomenklatur', function ($query) use ($bidangUrusan) {
                    $query->where('id', $bidangUrusan->id);
                });
            })
                ->select('deskripsi')
                ->first();
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18

            if ($monitoring && !empty($monitoring->deskripsi)) {
                $bidangUrusanDeskripsi = $monitoring->deskripsi;
            }
        }

<<<<<<< HEAD
        $bidangurusanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
=======
        $bidangurusanTugas = $skpdTugas->filter(function ($item) use ($urusanId) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            return $item->kodeNomenklatur->jenis_nomenklatur == 1
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

<<<<<<< HEAD
        $programTugas = $skpdTugas->filter(function($item) use ($urusanId) {
=======
        $programTugas = $skpdTugas->filter(function ($item) use ($urusanId) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            return $item->kodeNomenklatur->jenis_nomenklatur == 2
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

<<<<<<< HEAD
        $kegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
=======
        $kegiatanTugas = $skpdTugas->filter(function ($item) use ($urusanId) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            return $item->kodeNomenklatur->jenis_nomenklatur == 3
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

<<<<<<< HEAD
        $subkegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
=======
        $subkegiatanTugas = $skpdTugas->filter(function ($item) use ($urusanId) {
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            return $item->kodeNomenklatur->jenis_nomenklatur == 4
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

<<<<<<< HEAD
        // Ambil monitoring data untuk tugas ini
        $monitoring = \App\Models\Monitoring::where('skpd_tugas_id', $tugas->id)
            ->with(['monitoringAnggaran.monitoringTarget.periode', 'monitoringAnggaran.monitoringRealisasi.periode'])
            ->get();

        // Determine the view based on triwulan
        $viewName = $this->getDetailViewName($tid);

        return Inertia::render($viewName, [
            'user' => [
                'id' => $tugas->skpd->user_id,
                'nama_skpd' => $tugas->skpd->nama_dinas
            ],
            'tugas' => $tugas,
            'skpdTugas' => $skpdTugas,
=======
        $kepalaSkpd = '-';
        $kepala = $tugas->skpd->skpdKepala->first();
        if ($kepala) {
            if ($kepala->user && $kepala->user->userDetail && $kepala->user->userDetail->nama) {
                $kepalaSkpd = $kepala->user->userDetail->nama;
            } elseif ($kepala->user && $kepala->user->name) {
                $kepalaSkpd = $kepala->user->name;
            }
        }

        $monitoringTargets = [];
        $monitoringRealisasi = [];
        $allTasks = collect()->concat($programTugas)->concat($kegiatanTugas)->concat($subkegiatanTugas);

        $taskIds = $allTasks->pluck('id')->toArray();

        $monitorings = \App\Models\Monitoring::whereIn('skpd_tugas_id', $taskIds)
            ->where('tahun', $tahun)
            ->with(['monitoringAnggaran' => function ($query) use ($periode) {
                $query->with(['monitoringTarget' => function ($query) use ($periode) {
                    $query->where('periode_id', $periode->id);
                    $query->with('periode');
                }, 'monitoringRealisasi' => function ($query) use ($periode) {
                    $query->where('periode_id', $periode->id);
                    $query->with('periode');
                }]);
            }])
            ->get();

        Log::info('Total monitoring records fetched for Triwulan ' . $tid . ' Tahun ' . $tahun . ': ' . $monitorings->count());

        foreach ($monitorings as $monitoring) {
            $taskId = $monitoring->skpd_tugas_id;

            if ($monitoring->monitoringAnggaran->isEmpty()) {
                continue;
            }

            foreach ($monitoring->monitoringAnggaran as $anggaran) {

                if (!$anggaran->monitoringTarget->isEmpty()) {
                    foreach ($anggaran->monitoringTarget as $target) {
                        if ($target->periode_id != $periode->id) {
                            continue;
                        }

                        $monitoringTargets[] = [
                            'id' => $target->id,
                            'kinerja_fisik' => $target->kinerja_fisik,
                            'keuangan' => $target->keuangan,
                            'periode_id' => $target->periode_id,
                            'periode' => $target->periode ? $target->periode->nama : null,
                            'monitoring_id' => $monitoring->id,
                            'task_id' => $taskId,
                            'deskripsi' => $monitoring->deskripsi,
                            'nama_pptk' => $monitoring->nama_pptk ?? '-'
                        ];
                    }
                }

                if (!$anggaran->monitoringRealisasi->isEmpty()) {
                    foreach ($anggaran->monitoringRealisasi as $realisasi) {
                        if ($realisasi->periode_id != $periode->id) {
                            continue;
                        }

                        $monitoringRealisasi[] = [
                            'id' => $realisasi->id,
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'periode_id' => $realisasi->periode_id,
                            'periode' => $realisasi->periode ? $realisasi->periode->nama : null,
                            'monitoring_id' => $monitoring->id,
                            'task_id' => $taskId,
                            'monitoring_anggaran_id' => $anggaran->id,
                            'deskripsi' => $monitoring->deskripsi,
                            'nama_pptk' => $monitoring->nama_pptk ?? '-'
                        ];
                    }
                }
            }
        }

        Log::info('Total monitoring targets (periode_id = ' . $periode->id . '): ' . count($monitoringTargets));
        Log::info('Total monitoring realisasi (periode_id = ' . $periode->id . '): ' . count($monitoringRealisasi));

        $detailViewName = $this->getDetailViewName($tid);

        return Inertia::render($detailViewName, [
            'tugas' => $tugas,
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
<<<<<<< HEAD
            'bidangUrusanDeskripsi' => $bidangUrusanDeskripsi,
=======
            'kepalaSkpd' => $kepalaSkpd,
            'monitoringTargets' => $monitoringTargets,
            'monitoringRealisasi' => $monitoringRealisasi,
            'bidangUrusan' => $bidangUrusan ? [
                'id' => $bidangUrusan->id,
                'nomor_kode' => $bidangUrusan->nomor_kode,
                'nomenklatur' => $bidangUrusan->nomenklatur,
                'deskripsi' => $bidangUrusanDeskripsi
            ] : null,
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ],
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
<<<<<<< HEAD
            'monitoring' => $monitoring ?? [],
=======
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        ]);
    }

    /**
<<<<<<< HEAD
     * Save realization data for a subkegiatan
     */
    public function saveRealisasi(Request $request, int $tid, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
=======
     * Simpan data realisasi untuk subkegiatan
     */
    public function saveRealisasi(Request $request, int $tid, ?int $tahun = null)
    {
        $tahun = $tahun ?? date('Y');

        // Validasi triwulan ID
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if (!in_array($tid, [1, 2, 3, 4])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid triwulan ID.'
            ], 400);
        }

        $request->validate([
            'id' => 'required|numeric',
            'realisasi_fisik' => 'required|numeric',
            'realisasi_keuangan' => 'required|numeric',
            'capaian_fisik' => 'required|numeric',
            'capaian_keuangan' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'nama_pptk' => 'nullable|string',
        ]);

<<<<<<< HEAD
        // Check if the specified triwulan period is open
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
=======
        // Mendapatkan periode berdasarkan triwulan dan tahun
        $periodeTahun = PeriodeTahun::where('tahun', $tahun)->first();
        if (!$periodeTahun) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tahun tidak ditemukan.'
            ], 404);
        }
        $periode = Periode::where('tahun_id', $periodeTahun->id)
            ->whereHas('tahap', function ($query) use ($tid) {
                $query->where('id', $tid);
            })
            ->first();
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if (!$periode) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $this->getTriwulanName($tid) . ' tahun ' . $tahun . ' tidak ditemukan.'
            ], 404);
        }
<<<<<<< HEAD

=======
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if ($periode->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Periode ' . $this->getTriwulanName($tid) . ' belum dibuka. Data tidak dapat disimpan.'
            ], 403);
        }

<<<<<<< HEAD
        // Get the task record
        $task = SkpdTugas::findOrFail($request->id);

        // First, find the Rencana Awal monitoring record to copy budget data from
        $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
            ->where('deskripsi', 'Rencana Awal')
            ->where('tahun', $tahun) // Filter berdasarkan tahun
            ->first();

        // Get or create a monitoring record for REALIZATION specifically
=======
        $task = SkpdTugas::findOrFail($request->id);


        $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
            ->where('deskripsi', 'Rencana Awal')
            ->where('tahun', $tahun)
            ->first();

>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        $deskripsiRealisasi = 'Realisasi ' . $this->getTriwulanName($tid);
        $monitoring = Monitoring::firstOrCreate(
            [
                'skpd_tugas_id' => $task->id,
                'deskripsi' => $deskripsiRealisasi
            ],
            [
                'tahun' => date('Y'),
                'nama_pptk' => $request->nama_pptk ?? '-'
            ]
        );

<<<<<<< HEAD
        // Update monitoring info if provided
=======
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if ($request->has('keterangan') || $request->has('nama_pptk')) {
            $monitoring->nama_pptk = $request->nama_pptk ?? $monitoring->nama_pptk;
            $monitoring->save();
        }

<<<<<<< HEAD
        // Get or create monitoring anggaran
        $anggaran = MonitoringAnggaran::firstOrCreate(
            ['monitoring_id' => $monitoring->id],
            ['sumber_anggaran_id' => 1] // Default sumber anggaran
        );

        // Copy budget data (pagu) from the Rencana Awal record if it exists
=======
        $anggaran = MonitoringAnggaran::firstOrCreate(
            ['monitoring_id' => $monitoring->id],
            ['sumber_anggaran_id' => 1]
        );

>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if ($rencanaAwalMonitoring) {
            $rencanaAwalAnggaran = MonitoringAnggaran::where('monitoring_id', $rencanaAwalMonitoring->id)
                ->first();

            if ($rencanaAwalAnggaran) {
<<<<<<< HEAD
                // Copy pagu data for all categories (1=Pokok, 2=Parsial, 3=Perubahan)
=======
                // data pagu untuk semua kategori (1=Pokok, 2=Parsial, 3=Perubahan)
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
                for ($kategori = 1; $kategori <= 3; $kategori++) {
                    $pagu = MonitoringPagu::where('monitoring_anggaran_id', $rencanaAwalAnggaran->id)
                        ->where('kategori', $kategori)
                        ->first();

                    if ($pagu) {
                        MonitoringPagu::updateOrCreate(
                            [
                                'monitoring_anggaran_id' => $anggaran->id,
                                'kategori' => $kategori,
                                'periode_id' => $pagu->periode_id
                            ],
                            [
                                'dana' => $pagu->dana
                            ]
                        );
                    }
                }
            }
        }

<<<<<<< HEAD
        // Try to find existing realisasi for this anggaran and period
=======
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        $realisasi = MonitoringRealisasi::firstOrCreate(
            [
                'monitoring_anggaran_id' => $anggaran->id,
                'periode_id' => $periode->id
            ],
            [
                'kinerja_fisik' => $request->realisasi_fisik,
                'keuangan' => $request->realisasi_keuangan
            ]
        );

<<<<<<< HEAD
        // Update values if record already existed
=======
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
        if ($realisasi->wasRecentlyCreated === false) {
            $realisasi->kinerja_fisik = $request->realisasi_fisik;
            $realisasi->keuangan = $request->realisasi_keuangan;
            $realisasi->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data realisasi ' . $this->getTriwulanName($tid) . ' berhasil disimpan'
        ]);
    }

    /**
<<<<<<< HEAD
     * Get periode by triwulan ID
     */
    private function getPeriodeByTriwulan(int $tid, int $tahun)
    {
        $triwulanNames = [
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4'
        ];

        if (!isset($triwulanNames[$tid])) {
            return null;
        }

        // First get the tahun record
        $periodeTahun = PeriodeTahun::where('tahun', $tahun)->first();
        if (!$periodeTahun) {
            return null;
        }

        // Then get the periode with the correct tahap and tahun
        return Periode::where('tahun_id', $periodeTahun->id)
            ->whereHas('tahap', function($query) use ($triwulanNames, $tid) {
                $query->where('tahap', 'like', '%' . $triwulanNames[$tid] . '%');
=======
     * Ambil data periode berdasarkan ID triwulan dan tahun
     */
    private function getPeriodeByTriwulan(int $tid, int $tahun)
    {
        $periodeTahun = PeriodeTahun::where('tahun', $tahun)->first();
        if (!$periodeTahun) return null;
        return Periode::where('tahun_id', $periodeTahun->id)
            ->whereHas('tahap', function ($query) use ($tid) {
                $query->where('id', $tid);
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
            })
            ->first();
    }

    /**
<<<<<<< HEAD
     * Get triwulan name by ID
=======
     * Ambil nama triwulan berdasarkan ID
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
     */
    private function getTriwulanName(int $tid)
    {
        $names = [
            1 => 'Triwulan 1',
            2 => 'Triwulan 2',
            3 => 'Triwulan 3',
            4 => 'Triwulan 4'
        ];

        return $names[$tid] ?? 'Unknown';
    }

    /**
<<<<<<< HEAD
     * Get view name based on triwulan ID
=======
     * Ambil nama view berdasarkan ID triwulan
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
     */
    private function getViewName(int $tid)
    {
        $views = [
            1 => 'Triwulan1/Show',
            2 => 'Triwulan2/Show',
            3 => 'Triwulan3/Show',
            4 => 'Triwulan4/Show'
        ];

<<<<<<< HEAD
        return $views[$tid] ?? 'Unknown';
    }

    /**
     * Get detail view name based on triwulan ID
=======
        return $views[$tid] ?? 'Triwulan1/Show';
    }

    /**
     * Ambil nama view detail berdasarkan ID triwulan
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
     */
    private function getDetailViewName(int $tid)
    {
        $views = [
            1 => 'Triwulan1/Detail',
            2 => 'Triwulan2/Detail',
            3 => 'Triwulan3/Detail',
            4 => 'Triwulan4/Detail'
        ];

        return $views[$tid] ?? 'Triwulan1/Detail';
    }

    /**
<<<<<<< HEAD
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
=======
     * Menampilkan form untuk membuat resource baru.
     */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Simpan resource baru ke database.
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Menampilkan form untuk mengedit resource yang dipilih.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update resource yang dipilih di database.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Hapus resource yang dipilih dari database.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
>>>>>>> 6b3722e955bf271a0ef21b9c0dd0e250eb3afe18
