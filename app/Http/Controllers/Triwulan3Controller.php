<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
class Triwulan3Controller extends Controller
{
    public function index()
    {

        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('triwulan3.show', $user->id);
        }

        if ($user->hasRole('operator')) {
            $skpdUserIds = Skpd::where('nama_operator', $user->name)->pluck('user_id');
            $users = User::whereIn('id', $skpdUserIds)
                ->role('perangkat_daerah')
                ->with('skpd')
                ->paginate(1000);

            return Inertia::render('Triwulan3', [
                'users' => $users,
            ]);
        }

        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);
        return Inertia::render('Triwulan3', [
            'users' => $users,
        ]);
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

       return Inertia::render('Triwulan3/Show', [
           'user' => $user,
           'skpdTugas' => $skpdTugas,
           'urusanList' => $urusanList,
           'bidangUrusanList' => $bidangUrusanList,
           'programList' => $programList,
           'kegiatanList' => $kegiatanList,
           'subkegiatanList' => $subkegiatanList,
       ]);
   }

   public function showDetail($id)
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

        $urusanId = $tugas->kodeNomenklatur->details->first()->id_urusan;

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

        $kepalaSkpd = '-';
        $kepala = $tugas->skpd->skpdKepala->first();
        if ($kepala) {
            if ($kepala->user && $kepala->user->userDetail && $kepala->user->userDetail->nama) {
                $kepalaSkpd = $kepala->user->userDetail->nama;
            } elseif ($kepala->user && $kepala->user->name) {
                $kepalaSkpd = $kepala->user->name;
            }
        }

        return Inertia::render('Triwulan3/Detail', [
            'tugas' => $tugas,
            'programTugas' => $programTugas,
            'kegiatanTugas' => $kegiatanTugas,
            'subkegiatanTugas' => $subkegiatanTugas,
            'kepalaSkpd' => $kepalaSkpd,
            'user' => [
                'id' => $tugas->skpd_id,
                'nama_skpd' => $tugas->skpd->nama_skpd
            ]
        ]);
    }
}