<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SkpdTugasController extends Controller
{
    /**
     * Menyimpan tugas SKPD baru
     */
    public function store(Request $request)
    {
        \Log::info('Received request data:', $request->all());
        
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'kode_nomenklatur_id' => 'required|exists:kode_nomenklatur,id',
            'is_aktif' => 'required|integer',
        ]);
        
        \Log::info('Validated data:', $validated);

        // Check if this nomenklatur is already assigned to this SKPD
        $existingTask = SkpdTugas::where('skpd_id', $validated['skpd_id'])
            ->where('kode_nomenklatur_id', $validated['kode_nomenklatur_id'])
            ->where('is_aktif', 1)
            ->first();

        if ($existingTask) {
            \Log::warning('Attempted to add duplicate task');
            return back()->withErrors(['error' => 'Tugas ini sudah ditambahkan sebelumnya']);
        }

        // Verify the selected nomenklatur exists and get its details
        $nomenklatur = KodeNomenklatur::find($validated['kode_nomenklatur_id']);
        if (!$nomenklatur) {
            \Log::error('Invalid nomenklatur ID: ' . $validated['kode_nomenklatur_id']);
            return back()->withErrors(['error' => 'Kode nomenklatur tidak valid']);
        }
        
        \Log::info('Found nomenklatur:', [
            'id' => $nomenklatur->id,
            'nomor_kode' => $nomenklatur->nomor_kode,
            'nomenklatur' => $nomenklatur->nomenklatur,
            'jenis' => $nomenklatur->jenis_nomenklatur ?? $nomenklatur->jenis
        ]);

        // Create new task
        $skpdTugas = SkpdTugas::create($validated);
        \Log::info('Created new task with ID: ' . $skpdTugas->id);
        
        $skpd = Skpd::findOrFail($validated['skpd_id']);
        
        return Redirect::route('perangkatdaerah.show', $skpd->user_id)
            ->with('success', 'Tugas berhasil ditambahkan');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $skpdTugas = SkpdTugas::findOrFail($id);
        
        $skpdId = $skpdTugas->skpd_id;
        
        $skpdTugas->delete();
    
        $skpd = Skpd::findOrFail($skpdId);
        
        return Redirect::route('perangkatdaerah.show', $skpd->user_id)
            ->with('success', 'Tugas berhasil dihapus');
    }
}