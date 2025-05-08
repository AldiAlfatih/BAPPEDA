<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
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
        $users = User::role('perangkat_daerah')->with('skpd')->paginate(10);

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

        return Inertia::render('PerangkatDaerah/Create', [
            'users' => $users,
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
        ]);
  
        $user = User::find($validated['user_id']);

        $skpd = Skpd::create([
            'user_id' => $validated['user_id'],  
            'nama_skpd' => $user->name,  
            'nama_dinas' => $validated['nama_dinas'], 
            'no_dpa' => $validated['no_dpa'],
            'kode_organisasi' => $validated['kode_organisasi'],
        ]);
        
        return redirect()->route('perangkatdaerah.index')->with('success', 'Data SKPD berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('skpd')->findOrFail($id);
        
        // Get hierarchical nomenclature data
        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)->get();
        $bidangUrusanList = KodeNomenklatur::where('jenis_nomenklatur', 1)->get();
        $programList = KodeNomenklatur::where('jenis_nomenklatur', 2)->get();
        $kegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 3)->get();
        $subkegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 4)->get();
        
        // Debug data structures
        \Log::info('Urusan List Count: ' . $urusanList->count());
        \Log::info('Sample Urusan:', $urusanList->first() ? $urusanList->first()->toArray() : ['none']);
        
        \Log::info('Bidang Urusan List Count: ' . $bidangUrusanList->count());
        \Log::info('Sample Bidang Urusan:', $bidangUrusanList->first() ? $bidangUrusanList->first()->toArray() : ['none']);
        
        // Fix: Changed is_aktif from 0 to 1 to get active tasks
        $skpdTugas = SkpdTugas::where('skpd_id', $user->skpd->id)
            ->where('is_aktif', 1)
            ->with('kodeNomenklatur')
            ->get();
        
        \Log::info('SKPD Tugas Count: ' . $skpdTugas->count());
        if ($skpdTugas->count() > 0) {
            \Log::info('Sample SKPD Tugas:', $skpdTugas->first()->toArray());
            \Log::info('Sample Kode Nomenklatur from SKPD Tugas:', 
                $skpdTugas->first()->kodeNomenklatur ? $skpdTugas->first()->kodeNomenklatur->toArray() : ['none']);
        }
        
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
        
        return Inertia::render('PerangkatDaerah/Edit', [
            'skpd' => $skpd,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $skpd = Skpd::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_skpd' => 'required|string|max:255',
            'nama_dinas' => 'required|string|max:255',
            'no_dpa' => 'required|string|max:255',
            'kode_organisasi' => 'required|string|max:255',
        ]);
        
        $skpd->update($validated);
        
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