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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::role('perangkat_daerah')->with('skpd')->paginate(1000);

        return Inertia::render('PerangkatDaerah', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::role('perangkat_daerah')->get();

        $operators = User::role('operator')->get();  
        
        return Inertia::render('PerangkatDaerah/Create', [
            'users' => $users,
            'operators' => $operators,
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $skpd = Skpd::findOrFail($id);
        
        $users = User::role('perangkat_daerah')->get();

        $operator = TimKerja::where('skpd_id', $id)
            ->where('is_aktif', 1)
            ->with('user')
            ->first();

        $operators = User::role('operator')->get();

        return Inertia::render('PerangkatDaerah/Edit', [
            'skpd' => $skpd,
            'users' => $users,
            'operators' => $operators,
            'current_operator' => $operator ? $operator->user_id : null,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $skpd = Skpd::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'operator_id' => 'required|exists:users,id',
            'nama_skpd' => 'required|string|max:255',
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
        
        $currentKepala = SkpdKepala::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->first();
            
        if ($currentKepala && $currentKepala->user_id != $validated['user_id']) {
            $currentKepala->update(['is_aktif' => 0]);
            
            SkpdKepala::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['user_id'],
                'is_aktif' => 1,
            ]);
        } elseif (!$currentKepala) {
            SkpdKepala::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['user_id'],
                'is_aktif' => 1,
            ]);
        }

        $currentOperator = TimKerja::where('skpd_id', $skpd->id)
            ->where('is_aktif', 1)
            ->first();
            
        if ($currentOperator && $currentOperator->user_id != $validated['operator_id']) {
            $currentOperator->update(['is_aktif' => 0]);
            
            TimKerja::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['operator_id'],
                'is_aktif' => 1,
            ]);
        } elseif (!$currentOperator) {
            TimKerja::create([
                'skpd_id' => $skpd->id,
                'user_id' => $validated['operator_id'],
                'is_aktif' => 1,
            ]);
        }

        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil diperbarui.');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $skpd = Skpd::findOrFail($id);
        $skpd->delete();
        
        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil dihapus.');
    }
}