<?php
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
     * Display a listing of the resource.
     */
    public function index(int $tid, int $tahun = null)
    {
        $user = Auth::user();
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini jika tidak disebutkan

        \Log::info("TriwulanController.index: Starting", [
            'tid' => $tid,
            'tahun' => $tahun,
            'user_id' => $user ? $user->id : 'NULL',
            'user_name' => $user ? $user->name : 'NULL',
            'user_roles' => $user ? $user->getRoleNames() : 'NULL'
        ]);

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        // Get periode information
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
        }

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('triwulan.show', ['tid' => $tid, 'id' => $user->id, 'tahun' => $tahun]);
        }

        if ($user->hasRole('operator')) {
            $skpdIds = \App\Models\TimKerja::where('operator_id', $user->id)
                ->where('is_aktif', 1)
                ->pluck('skpd_id');

            $users = User::whereHas('skpd', function($q) use ($skpdIds) {
                    $q->whereIn('skpd.id', $skpdIds);
                })
                ->role('perangkat_daerah')
                ->with(['skpd', 'skpd.operatorAktif.operator.userDetail', 'skpd.kepala.user.userDetail'])
                ->paginate(1000);

            $users->getCollection()->transform(function ($user) {
                $skpdList = $user->skpd && $user->skpd->isNotEmpty() ? $user->skpd->map(function ($skpd) {
                    return [
                        'nama_dinas' => $skpd->nama_skpd ?? '-',
                        'nama_operator' => optional(optional($skpd->operatorAktif)->operator)->name ?? '-',
                        'nip_operator' => optional(optional($skpd->operatorAktif)->operator)->userDetail->nip ?? '-',
                        'nama_kepala_skpd' => optional(optional($skpd->kepala->where('is_aktif', 1)->first())->user)->name ?? '-',
                        'nip_kepala_skpd' => optional(optional($skpd->kepala->where('is_aktif', 1)->first())->user)->userDetail->nip ?? '-',
                        'no_dpa' => $skpd->no_dpa ?? '-',
                        'kode_organisasi' => $skpd->kode_organisasi ?? '-',
                    ];
                })->toArray() : [];

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'skpd' => $skpdList,
                ];
            });

            \Log::info("TriwulanController.index: Sending data for operator", [
                'users_count' => $users->count(),
                'users_total' => $users->total(),
                'users_data_count' => count($users->items())
            ]);

            return Inertia::render('Triwulan', [
                'users' => $users,
                'tid' => $tid,
                'tahun' => $tahun,
                'periode' => $periode,
                'triwulanName' => $this->getTriwulanName($tid),
            ]);
        }

        $users = User::role('perangkat_daerah')
            ->with(['skpd', 'skpd.operatorAktif.operator.userDetail', 'skpd.kepala.user.userDetail'])
            ->paginate(1000);

        $users->getCollection()->transform(function ($user) {
            $skpdList = $user->skpd && $user->skpd->isNotEmpty() ? $user->skpd->map(function ($skpd) {
                return [
                    'nama_dinas' => $skpd->nama_skpd ?? '-',
                    'nama_operator' => optional(optional($skpd->operatorAktif)->operator)->name ?? '-',
                    'nip_operator' => optional(optional($skpd->operatorAktif)->operator)->userDetail->nip ?? '-',
                    'nama_kepala_skpd' => optional(optional($skpd->kepala->where('is_aktif', 1)->first())->user)->name ?? '-',
                    'nip_kepala_skpd' => optional(optional($skpd->kepala->where('is_aktif', 1)->first())->user)->userDetail->nip ?? '-',
                    'no_dpa' => $skpd->no_dpa ?? '-',
                    'kode_organisasi' => $skpd->kode_organisasi ?? '-',
                ];
            })->toArray() : [];

            return [
                'id' => $user->id,
                'name' => $user->name,
                'skpd' => $skpdList,
            ];
        });

        \Log::info("TriwulanController.index: Sending data for admin/other", [
            'users_count' => $users->count(),
            'users_total' => $users->total(),
            'users_data_count' => count($users->items())
        ]);

        return Inertia::render('Triwulan', [
            'users' => $users,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $tid, string $id, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->with('error', 'Invalid triwulan ID.');
        }

        // Find the user first, then get their SKPD
        $user = User::findOrFail($id);

        \Log::info("TriwulanController.show: Loading data for user", [
            'user_id' => $id,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()
        ]);

        // Get the SKPD associated with this user
        $skpd = $user->skpd()->with([
            'kepala' => function($query) {
                $query->where('is_aktif', 1);
            },
            'kepala.user.userDetail',
            'timKerja' => function($query) {
                $query->where('is_aktif', 1);
            },
            'timKerja.operator.userDetail'
        ])->first();

        if (!$skpd) {
            \Log::warning("TriwulanController.show: No SKPD found for user", ['user_id' => $id]);
            return redirect()->back()->with('error', 'SKPD tidak ditemukan untuk user ini.');
        }

        \Log::info("TriwulanController.show: SKPD found", [
            'skpd_id' => $skpd->id,
            'skpd_name' => $skpd->nama_skpd,
            'tim_kerja_count' => $skpd->timKerja->count(),
            'kepala_count' => $skpd->kepala->count()
        ]);

        // Transform data to ensure consistent structure
        $skpdArray = $skpd->toArray();

        // Process kepala aktif
        $skpdArray['kepala_aktif'] = $skpdArray['skpd_kepala'][0] ?? null;
        if ($skpdArray['kepala_aktif']) {
            $skpdArray['kepala_aktif']['user']['user_detail'] = $skpdArray['kepala_aktif']['user']['user_detail'] ?? [
                'nip' => '-',
                'alamat' => '-',
                'no_hp' => '-',
                'jenis_kelamin' => '-'
            ];
        }

        // Process tim kerja aktif (operator/penanggung jawab)
        $skpdArray['tim_kerja_aktif'] = $skpdArray['tim_kerja'][0] ?? null;
        if ($skpdArray['tim_kerja_aktif'] && isset($skpdArray['tim_kerja_aktif']['operator'])) {
            $skpdArray['tim_kerja_aktif']['operator']['user_detail'] = $skpdArray['tim_kerja_aktif']['operator']['user_detail'] ?? [
                'nip' => '-',
                'alamat' => '-',
                'no_hp' => '-',
                'jenis_kelamin' => '-'
            ];
        }

        // Add formatted data for the frontend
        $skpdArray['nama_dinas'] = $skpd->nama_skpd;
        $skpdArray['kode_organisasi'] = $skpd->kode_organisasi;
        $skpdArray['no_dpa'] = $skpd->no_dpa ?? '-';

        // Kepala SKPD info
        $skpdArray['nama_kepala_skpd'] = $skpdArray['kepala_aktif']['user']['name'] ?? 'Tidak tersedia';
        $skpdArray['nip_kepala_skpd'] = $skpdArray['kepala_aktif']['user']['user_detail']['nip'] ?? '-';

        // Tim kerja (operator/penanggung jawab) info
        $skpdArray['nama_operator'] = $skpdArray['tim_kerja_aktif']['operator']['name'] ?? 'Tidak tersedia';
        $skpdArray['nip_operator'] = $skpdArray['tim_kerja_aktif']['operator']['user_detail']['nip'] ?? '-';

        $periode = $this->getPeriodeByTriwulan($tid, $tahun);

        if (!$periode) {
            return redirect()->back()->with('error', 'Periode triwulan tidak ditemukan untuk tahun ' . $tahun . '.');
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

        $skpdTugas = SkpdTugas::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();

        // Determine the view based on triwulan
        $viewName = 'Triwulan/Show';

        return Inertia::render($viewName, [
            'user' => [
                'id' => $user->id,
                'nama_skpd' => $skpd->nama_skpd ?? $skpd->nama_dinas
            ],
            'skpd' => $skpdArray,
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

    public function showDetail(int $tid, string $id, string $taskId, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->withErrors(['error' => 'ID Triwulan tidak valid.']);
        }

        $tugas = SkpdTugas::with([
            'kodeNomenklatur.details',
            'skpd.kepala.user.userDetail',
            'skpd.kepala' => function($query) {
                $query->where('is_aktif', 1);
            },
            'skpd.timKerja.operator.userDetail'
        ])->findOrFail($taskId);

        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return redirect()->back()->withErrors(['error' => 'Periode ' . $this->getTriwulanName($tid) . ' tahun ' . $tahun . ' tidak ditemukan.']);
        }

        // Get all SKPD tasks for this SKPD with monitoring data
        $skpdTugas = SkpdTugas::where('skpd_id', $tugas->skpd_id)
            ->where('is_aktif', 1)
            ->with([
                'kodeNomenklatur.details',
                'monitoring' => function($query) {
                    $query->with([
                        'anggaran.target.periode',
                        'anggaran.realisasi.periode',
                        'anggaran.sumberAnggaran',
                        'anggaran.pagu'
                    ]);
                }
            ])
            ->get();

        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

        // Get urusan data
        $urusan = KodeNomenklatur::where('jenis_nomenklatur', 0)
            ->where('id', $urusanId)
            ->first();

        // Get bidang urusan data
        $bidangUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->whereHas('details', function($query) use ($urusanId) {
                $query->where('id_urusan', $urusanId);
            })
            ->first();

        // Find any monitoring that might have deskripsi for bidang urusan
        $bidangUrusanDeskripsi = '-';
        if ($bidangUrusan) {
            $monitoring = \App\Models\Monitoring::whereHas('tugas', function($query) use ($bidangUrusan) {
                $query->whereHas('kodeNomenklatur', function($query) use ($bidangUrusan) {
                    $query->where('id', $bidangUrusan->id);
                });
            })
            ->select('deskripsi')
            ->first();

            if ($monitoring && !empty($monitoring->deskripsi)) {
                $bidangUrusanDeskripsi = $monitoring->deskripsi;
            }
        }

        $bidangurusanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 1
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        $programTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 2
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        $kegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 3
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        $subkegiatanTugas = $skpdTugas->filter(function($item) use ($urusanId) {
            return $item->kodeNomenklatur->jenis_nomenklatur == 4
                && $item->kodeNomenklatur->details->first()
                && $item->kodeNomenklatur->details->first()->id_urusan == $urusanId;
        })->values();

        // Ambil monitoring data untuk tugas ini
        $monitoring = \App\Models\Monitoring::where('skpd_tugas_id', $tugas->id)
            ->with(['anggaran.target.periode', 'anggaran.realisasi.periode'])
            ->get();

        // Collect all monitoring targets and realisasi
        $monitoringTargets = [];
        $monitoringRealisasi = [];

        // Get all monitoring data for all tasks in this SKPD to build complete dataset
        $allMonitoring = \App\Models\Monitoring::whereHas('tugas', function($query) use ($tugas) {
                $query->where('skpd_id', $tugas->skpd_id);
            })
            ->with(['anggaran.target.periode', 'anggaran.realisasi.periode', 'anggaran.sumberAnggaran', 'anggaran.pagu'])
            ->get();

        foreach ($allMonitoring as $monitoringItem) {
            foreach ($monitoringItem->anggaran as $anggaran) {
                // Get pagu data for all categories (pokok, parsial, perubahan) with proper aggregation
                $paguData = [
                    'pokok' => 0,
                    'parsial' => 0,
                    'perubahan' => 0
                ];
                
                // PERBAIKAN: Aggregate pagu data from all records, not just override
                foreach ($anggaran->pagu as $pagu) {
                    switch ($pagu->kategori) {
                        case 1:
                            $paguData['pokok'] += $pagu->dana; // ✅ Sum, bukan override
                            break;
                        case 2:
                            $paguData['parsial'] += $pagu->dana; // ✅ Sum dari semua triwulan
                            break;
                        case 3:
                            $paguData['perubahan'] += $pagu->dana; // ✅ Sum dari semua budget change
                            break;
                    }
                }

                // Collect monitoring targets
                foreach ($anggaran->target as $target) {
                    $monitoringTargets[] = [
                        'id' => $target->id,
                        'task_id' => $monitoringItem->skpd_tugas_id,
                        'kinerja_fisik' => $target->kinerja_fisik,
                        'keuangan' => $target->keuangan,
                        'pagu_pokok' => $paguData['pokok'],
                        'pagu_parsial' => $paguData['parsial'],
                        'pagu_perubahan' => $paguData['perubahan'],
                        'periode' => $target->periode ? $target->periode->nama : 'Unknown',
                        'periode_id' => $target->periode_id,
                        'monitoring_id' => $monitoringItem->id,
                        'monitoring_anggaran_id' => $anggaran->id,
                        'deskripsi' => $monitoringItem->deskripsi,
                        'nama_pptk' => $monitoringItem->nama_pptk,
                        'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                        'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                    ];
                }

                // Collect monitoring realisasi
                foreach ($anggaran->realisasi as $realisasi) {
                    $monitoringRealisasi[] = [
                        'id' => $realisasi->id,
                        'task_id' => $monitoringItem->skpd_tugas_id,
                        'kinerja_fisik' => $realisasi->kinerja_fisik,
                        'keuangan' => $realisasi->keuangan,
                        'pagu_pokok' => $paguData['pokok'],
                        'pagu_parsial' => $paguData['parsial'],
                        'pagu_perubahan' => $paguData['perubahan'],
                        'periode' => $realisasi->periode ? $realisasi->periode->nama : 'Unknown',
                        'periode_id' => $realisasi->periode_id,
                        'monitoring_id' => $monitoringItem->id,
                        'monitoring_anggaran_id' => $anggaran->id,
                        'deskripsi' => $monitoringItem->deskripsi,
                        'nama_pptk' => $monitoringItem->nama_pptk,
                        'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
                        'sumber_anggaran_nama' => $anggaran->sumberAnggaran ? $anggaran->sumberAnggaran->nama : 'Unknown',
                    ];
                }
            }
        }

        \Log::info("TriwulanController.showDetail: Prepared data", [
            'task_id' => $taskId,
            'monitoring_targets_count' => count($monitoringTargets),
            'monitoring_realisasi_count' => count($monitoringRealisasi),
            'periode_id' => $periode->id,
            'skpd_data' => [
                'nama_skpd' => $tugas->skpd->nama_skpd ?? 'NULL',
                'nama_dinas' => $tugas->skpd->nama_dinas ?? 'NULL',
                'kode_organisasi' => $tugas->skpd->kode_organisasi ?? 'NULL',
                'no_dpa' => $tugas->skpd->no_dpa ?? 'NULL',
                'tim_kerja_count' => $tugas->skpd->timKerja ? $tugas->skpd->timKerja->count() : 0,
                'user_penanggung_jawab' => $tugas->skpd->userPenanggungJawab ? $tugas->skpd->userPenanggungJawab->name : 'NULL',
                'skpd_kepala_count' => $tugas->skpd->kepala ? $tugas->skpd->kepala->count() : 0,
                'skpd_kepala_first' => $tugas->skpd->kepala && $tugas->skpd->kepala->count() > 0 ? [
                    'user_name' => $tugas->skpd->kepala[0]->user->name ?? 'NULL',
                    'user_nip' => $tugas->skpd->kepala[0]->user->userDetail->nip ?? 'NULL'
                ] : 'NULL',
                'tim_kerja_first' => $tugas->skpd->timKerja && $tugas->skpd->timKerja->count() > 0 ? [
                    'operator_name' => $tugas->skpd->timKerja[0]->operator->name ?? 'NULL',
                    'operator_nip' => $tugas->skpd->timKerja[0]->operator->userDetail->nip ?? 'NULL'
                ] : 'NULL',
            ]
        ]);

        // Determine the view based on triwulan
        $viewName = $this->getDetailViewName($tid);

        // Debug logging untuk data user
        \Log::info("TriwulanController.showDetail: User data being sent", [
            'tugas_skpd_user_id' => $tugas->skpd->user_id ?? 'NULL',
            'tugas_skpd_nama_skpd' => $tugas->skpd->nama_skpd ?? 'NULL',
            'tugas_skpd_nama_dinas' => $tugas->skpd->nama_dinas ?? 'NULL',
            'user_id_parameter' => $id,
            'task_id_parameter' => $taskId
        ]);

        // Prepare SKPD data with kepala information
        $skpdData = $tugas->skpd->toArray();

        // Get kepala aktif
        $kepalaAktif = $tugas->skpd->kepala->where('is_aktif', 1)->first();
        if ($kepalaAktif && $kepalaAktif->user) {
            $skpdData['nama_kepala_skpd'] = $kepalaAktif->user->name;
            $skpdData['nip_kepala_skpd'] = $kepalaAktif->user->userDetail->nip ?? '-';
        } else {
            $skpdData['nama_kepala_skpd'] = 'Tidak tersedia';
            $skpdData['nip_kepala_skpd'] = '-';
        }

        // Get tim kerja aktif (operator)
        $timKerjaAktif = $tugas->skpd->timKerja->where('is_aktif', 1)->first();
        if ($timKerjaAktif && $timKerjaAktif->operator) {
            $skpdData['nama_operator'] = $timKerjaAktif->operator->name;
            $skpdData['nip_operator'] = $timKerjaAktif->operator->userDetail->nip ?? '-';
        } else {
            $skpdData['nama_operator'] = 'Tidak tersedia';
            $skpdData['nip_operator'] = '-';
        }

        // Add other SKPD info
        $skpdData['nama_dinas'] = $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas ?? 'SKPD';
        $skpdData['kode_organisasi'] = $tugas->skpd->kode_organisasi ?? '-';
        $skpdData['no_dpa'] = $tugas->skpd->no_dpa ?? '-';

        return Inertia::render($viewName, [
            'user' => [
                'id' => $tugas->skpd->user_id ?? $id, // Fallback ke parameter $id jika user_id null
                'nama_skpd' => $tugas->skpd->nama_skpd ?? $tugas->skpd->nama_dinas ?? 'SKPD'
            ],
            'skpd' => $skpdData,
            'tugas' => $tugas,
            'skpdTugas' => $skpdTugas,
            'bidangurusanTugas' => $bidangurusanTugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'urusan' => $urusan ? [
                'id' => $urusan->id,
                'nomor_kode' => $urusan->nomor_kode,
                'nomenklatur' => $urusan->nomenklatur,
            ] : null,
            'bidangUrusan' => $bidangUrusan ? [
                'id' => $bidangUrusan->id,
                'nomor_kode' => $bidangUrusan->nomor_kode,
                'nomenklatur' => $bidangUrusan->nomenklatur,
                'deskripsi' => $bidangUrusanDeskripsi,
            ] : null,
            'monitoringTargets' => $monitoringTargets,
            'monitoringRealisasi' => $monitoringRealisasi,
            'tid' => $tid,
            'tahun' => $tahun,
            'periode' => $periode,
            'triwulanName' => $this->getTriwulanName($tid),
            'monitoring' => $monitoring ?? [],
        ]);
    }

    /**
     * Save realization data for a subkegiatan
     */
    public function saveRealisasi(Request $request, int $tid, int $tahun = null)
    {
        \Log::info("TriwulanController.saveRealisasi: Route accessed", [
            'tid' => $tid,
            'tahun' => $tahun,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);

        try {
            $tahun = $tahun ?? date('Y'); // Default ke tahun saat ini

        // Validate triwulan ID
        if (!in_array($tid, [1, 2, 3, 4])) {
            return redirect()->back()->withErrors(['error' => 'ID Triwulan tidak valid.']);
        }

        $request->validate([
            'id' => 'required|numeric',
            'sumber_anggaran_id' => 'required|numeric',
            'realisasi_fisik' => 'required|numeric|min:0|max:100',
            'realisasi_keuangan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'nama_pptk' => 'nullable|string|max:255',
        ], [
            'realisasi_fisik.max' => 'Kinerja fisik tidak boleh lebih dari 100%.',
            'realisasi_fisik.min' => 'Kinerja fisik tidak boleh kurang dari 0%.',
            'realisasi_fisik.required' => 'Kinerja fisik wajib diisi.',
            'realisasi_fisik.numeric' => 'Kinerja fisik harus berupa angka.',
            'realisasi_keuangan.min' => 'Realisasi keuangan tidak boleh kurang dari 0.',
            'realisasi_keuangan.required' => 'Realisasi keuangan wajib diisi.',
            'realisasi_keuangan.numeric' => 'Realisasi keuangan harus berupa angka.',
            'id.required' => 'ID tugas wajib diisi.',
            'id.numeric' => 'ID tugas harus berupa angka.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.',
            'nama_pptk.max' => 'Nama PPTK tidak boleh lebih dari 255 karakter.',
        ]);

        \Log::info("TriwulanController.saveRealisasi: Starting", [
            'tid' => $tid,
            'task_id' => $request->id,
            'sumber_anggaran_id' => $request->sumber_anggaran_id,
            'realisasi_fisik' => $request->realisasi_fisik,
            'realisasi_keuangan' => $request->realisasi_keuangan,
            'keterangan' => $request->keterangan,
            'nama_pptk' => $request->nama_pptk,
            'tahun' => $tahun
        ]);

        // Check if the specified triwulan period is open
        $periode = $this->getPeriodeByTriwulan($tid, $tahun);
        if (!$periode) {
            return redirect()->back()->withErrors(['error' => 'Periode ' . $this->getTriwulanName($tid) . ' tahun ' . $tahun . ' tidak ditemukan.']);
        }
        if ($periode->status != 1) {
            return redirect()->back()->withErrors(['error' => 'Periode ' . $this->getTriwulanName($tid) . ' belum dibuka. Data tidak dapat disimpan.']);
        }

        // Get the task record
        $task = SkpdTugas::findOrFail($request->id);

        // First, find the Rencana Awal monitoring record to copy budget data from
        $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
            ->where('deskripsi', 'Rencana Awal')
            ->where('tahun', $tahun) // Filter berdasarkan tahun
            ->first();

        // PERBAIKAN: If not found by 'Rencana Awal' deskripsi, try alternative approach
        if (!$rencanaAwalMonitoring) {
            // Look for monitoring with Rencana periode (more flexible approach)
            $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
                ->where('tahun', $tahun)
                ->whereHas('periode.tahap', function($query) {
                    $query->where('tahap', 'Rencana');
                })
                ->first();

            \Log::info("TriwulanController.saveRealisasi: Rencana Awal lookup via periode", [
                'found' => $rencanaAwalMonitoring ? true : false,
                'monitoring_id' => $rencanaAwalMonitoring ? $rencanaAwalMonitoring->id : null
            ]);
        }

        // If still not found, try the latest monitoring for this task
        if (!$rencanaAwalMonitoring) {
            $rencanaAwalMonitoring = Monitoring::where('skpd_tugas_id', $task->id)
                ->where('tahun', $tahun)
                ->latest()
                ->first();

            \Log::info("TriwulanController.saveRealisasi: Using latest monitoring as fallback", [
                'found' => $rencanaAwalMonitoring ? true : false,
                'monitoring_id' => $rencanaAwalMonitoring ? $rencanaAwalMonitoring->id : null,
                'deskripsi' => $rencanaAwalMonitoring ? $rencanaAwalMonitoring->deskripsi : null
            ]);
        }

        // Get or create a monitoring record for REALIZATION specifically
        // Use a combination of skpd_tugas_id, tahun, and periode_id as identifier
        // deskripsi will be updated with user input (keterangan)

        \Log::info("TriwulanController.saveRealisasi: Searching for monitoring", [
            'skpd_tugas_id' => $task->id,
            'tahun' => $tahun,
            'periode_id' => $periode->id
        ]);

        $monitoring = Monitoring::firstOrCreate(
            [
                'skpd_tugas_id' => $task->id,
                'tahun' => $tahun,
                'periode_id' => $periode->id  // Use periode_id as part of identifier instead of deskripsi
            ],
            [
                'nama_pptk' => !empty($request->nama_pptk) ? $request->nama_pptk : '-',
                'deskripsi' => !empty($request->keterangan) ? $request->keterangan : '-' // deskripsi is user input (keterangan)
            ]
        );

        \Log::info("TriwulanController.saveRealisasi: Monitoring result", [
            'monitoring_id' => $monitoring->id,
            'was_recently_created' => $monitoring->wasRecentlyCreated,
            'deskripsi' => $monitoring->deskripsi,
            'tahun' => $monitoring->tahun
        ]);

        // Get or create monitoring anggaran dengan sumber_anggaran_id yang spesifik
        $anggaran = MonitoringAnggaran::firstOrCreate(
            [
                'monitoring_id' => $monitoring->id,
                'sumber_anggaran_id' => $request->sumber_anggaran_id
            ],
            ['sumber_anggaran_id' => $request->sumber_anggaran_id]
        );

        \Log::info("TriwulanController.saveRealisasi: MonitoringAnggaran result", [
            'anggaran_id' => $anggaran->id,
            'monitoring_id' => $anggaran->monitoring_id,
            'sumber_anggaran_id' => $anggaran->sumber_anggaran_id,
            'was_recently_created' => $anggaran->wasRecentlyCreated
        ]);

        // Copy budget data (pagu) from the Rencana Awal record if it exists
        if ($rencanaAwalMonitoring) {
            $rencanaAwalAnggaran = MonitoringAnggaran::where('monitoring_id', $rencanaAwalMonitoring->id)
                ->where('sumber_anggaran_id', $request->sumber_anggaran_id)
                ->first();

            if ($rencanaAwalAnggaran) {
                // PERBAIKAN: Copy pagu data for all categories with proper aggregation
                for ($kategori = 1; $kategori <= 3; $kategori++) {
                    // Get ALL pagu records for this category and sum them up
                    $paguRecords = MonitoringPagu::where('monitoring_anggaran_id', $rencanaAwalAnggaran->id)
                        ->where('kategori', $kategori)
                        ->get();

                    if ($paguRecords->isNotEmpty()) {
                        // Aggregate the dana values
                        $totalDana = $paguRecords->sum('dana');
                        
                        // Use the first record's periode_id as reference (or you can choose the latest)
                        $firstPagu = $paguRecords->first();
                        
                        // PERBAIKAN: Copy aggregated pagu data but keep the original periode_id for referencing
                        // This allows the triwulan detail page to access the same pagu data that RencanaAwal shows
                        MonitoringPagu::updateOrCreate(
                            [
                                'monitoring_anggaran_id' => $anggaran->id,
                                'kategori' => $kategori,
                                'periode_id' => $firstPagu->periode_id // Keep original periode_id from RencanaAwal
                            ],
                            [
                                'dana' => $totalDana // ✅ Use aggregated total
                            ]
                        );

                        \Log::info("TriwulanController.saveRealisasi: Copied aggregated pagu data", [
                            'kategori' => $kategori,
                            'records_count' => $paguRecords->count(),
                            'individual_amounts' => $paguRecords->pluck('dana')->toArray(),
                            'total_dana' => $totalDana,
                            'original_periode_id' => $firstPagu->periode_id,
                            'source_anggaran_id' => $rencanaAwalAnggaran->id,
                            'destination_anggaran_id' => $anggaran->id
                        ]);
                    }
                }
            }
        } else {
            \Log::warning("TriwulanController.saveRealisasi: No Rencana Awal monitoring found", [
                'task_id' => $task->id,
                'tahun' => $tahun,
                'sumber_anggaran_id' => $request->sumber_anggaran_id
            ]);
        }

        // Update monitoring record dengan nama_pptk dan deskripsi (keterangan)
        // Pastikan tidak ada nilai null yang disimpan - gunakan default '-' untuk field kosong
        $monitoring->nama_pptk = !empty($request->nama_pptk) ? trim($request->nama_pptk) : '-';
        $monitoring->deskripsi = !empty($request->keterangan) ? trim($request->keterangan) : '-';
        $monitoring->save();

        \Log::info("TriwulanController.saveRealisasi: Before MonitoringRealisasi operation", [
            'monitoring_id' => $monitoring->id,
            'anggaran_id' => $anggaran->id,
            'periode_id' => $periode->id,
            'monitoring_was_recently_created' => $monitoring->wasRecentlyCreated ?? 'unknown',
            'anggaran_was_recently_created' => $anggaran->wasRecentlyCreated ?? 'unknown'
        ]);

        // Try to find existing realisasi for this anggaran and period
        $realisasi = MonitoringRealisasi::where('monitoring_anggaran_id', $anggaran->id)
            ->where('periode_id', $periode->id)
            ->first();

        if ($realisasi) {
            // Update existing record
            $realisasi->kinerja_fisik = $request->realisasi_fisik;
            $realisasi->keuangan = $request->realisasi_keuangan;
            $realisasi->save();

            \Log::info("TriwulanController.saveRealisasi: Updated existing realisasi", [
                'realisasi_id' => $realisasi->id,
                'old_kinerja_fisik' => $realisasi->getOriginal('kinerja_fisik'),
                'new_kinerja_fisik' => $realisasi->kinerja_fisik,
                'old_keuangan' => $realisasi->getOriginal('keuangan'),
                'new_keuangan' => $realisasi->keuangan
            ]);
        } else {
            // Create new record
            $realisasi = MonitoringRealisasi::create([
                'monitoring_anggaran_id' => $anggaran->id,
                'periode_id' => $periode->id,
                'kinerja_fisik' => $request->realisasi_fisik,
                'keuangan' => $request->realisasi_keuangan,
            ]);

            \Log::info("TriwulanController.saveRealisasi: Created new realisasi", [
                'realisasi_id' => $realisasi->id,
                'monitoring_anggaran_id' => $anggaran->id,
                'periode_id' => $periode->id
            ]);
        }

        // Hitung dan simpan akumulasi persentase kinerja tahunan
        $akumulasiData = $this->hitungAkumulasiKinerjaTahunan($task->id, $tahun);

        \Log::info("TriwulanController.saveRealisasi: Data saved successfully", [
            'realisasi_id' => $realisasi->id,
            'monitoring_id' => $monitoring->id,
            'akumulasi_fisik_tahunan' => $akumulasiData['akumulasi_fisik'],
            'akumulasi_keuangan_tahunan' => $akumulasiData['akumulasi_keuangan'],
            'triwulan_data' => $akumulasiData['detail_triwulan']
        ]);

        // For Inertia requests, redirect back with success message
        return redirect()->back()->with('success', 'Data realisasi ' . $this->getTriwulanName($tid) . ' berhasil disimpan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("TriwulanController.saveRealisasi: Validation error", [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            // For Inertia, let Laravel handle validation errors automatically
            throw $e;

        } catch (\Exception $e) {
            \Log::error("TriwulanController.saveRealisasi: General error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan internal server: ' . $e->getMessage()]);
        }
    }

    /**
     * Hitung akumulasi persentase kinerja tahunan berdasarkan semua triwulan
     */
    private function hitungAkumulasiKinerjaTahunan($skpdTugasId, $tahun)
    {
        $detailTriwulan = [];
        $totalFisik = 0;
        $totalKeuangan = 0;
        $countTriwulan = 0;

        // Loop untuk semua triwulan (1-4)
        for ($tid = 1; $tid <= 4; $tid++) {
            $periode = $this->getPeriodeByTriwulan($tid, $tahun);
            if (!$periode) continue;

            // Cari monitoring realisasi untuk triwulan ini
            $monitoring = Monitoring::where('skpd_tugas_id', $skpdTugasId)
                ->where('periode_id', $periode->id)
                ->where('tahun', $tahun)
                ->first();

            if ($monitoring) {
                $anggaran = MonitoringAnggaran::where('monitoring_id', $monitoring->id)->first();
                if ($anggaran) {
                    $realisasi = MonitoringRealisasi::where('monitoring_anggaran_id', $anggaran->id)
                        ->where('periode_id', $periode->id)
                        ->first();

                    if ($realisasi) {
                        $detailTriwulan[$tid] = [
                            'triwulan' => $tid,
                            'nama_triwulan' => $this->getTriwulanName($tid),
                            'kinerja_fisik' => $realisasi->kinerja_fisik,
                            'keuangan' => $realisasi->keuangan,
                            'periode_id' => $periode->id,
                            'realisasi_id' => $realisasi->id
                        ];

                        $totalFisik += $realisasi->kinerja_fisik;
                        $totalKeuangan += $realisasi->keuangan;
                        $countTriwulan++;
                    }
                }
            }
        }

        // Hitung rata-rata atau total (sesuai kebutuhan bisnis)
        // Di sini saya menggunakan total akumulasi
        $akumulasiFisik = $totalFisik;
        $akumulasiKeuangan = $totalKeuangan;

        // Alternatif: jika ingin rata-rata
        // $akumulasiFisik = $countTriwulan > 0 ? $totalFisik / $countTriwulan : 0;
        // $akumulasiKeuangan = $countTriwulan > 0 ? $totalKeuangan / $countTriwulan : 0;

        return [
            'akumulasi_fisik' => $akumulasiFisik,
            'akumulasi_keuangan' => $akumulasiKeuangan,
            'jumlah_triwulan_tersimpan' => $countTriwulan,
            'detail_triwulan' => $detailTriwulan
        ];
    }

    /**
     * API endpoint untuk mendapatkan akumulasi kinerja tahunan
     */
    public function getAkumulasiKinerjaTahunan(Request $request, int $skpdTugasId, int $tahun = null)
    {
        $tahun = $tahun ?? date('Y');

        $akumulasiData = $this->hitungAkumulasiKinerjaTahunan($skpdTugasId, $tahun);

        return response()->json([
            'success' => true,
            'data' => $akumulasiData
        ]);
    }

    /**
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
            })
            ->first();
    }

    /**
     * Get triwulan name by ID
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
     * Get view name based on triwulan ID
     */
    private function getViewName(int $tid)
    {
        return 'Triwulan/Show';
    }

    /**
     * Get detail view name based on triwulan ID
     */
    private function getDetailViewName(int $tid)
    {
        return 'Triwulan/Detail';
    }

    /**
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
