<?php

namespace App\Http\Controllers;

use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkpdTugasController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'kode_nomenklatur_id' => 'required|exists:kode_nomenklatur,id',
            'is_aktif' => 'required|integer'
        ]);

        $existingTask = SkpdTugas::where('skpd_id', $validated['skpd_id'])
            ->where('kode_nomenklatur_id', $validated['kode_nomenklatur_id'])
            ->first();
            
        if ($existingTask) {
            return back()->with('error', 'Tugas ini sudah ditambahkan sebelumnya.');
        }

        try {
            SkpdTugas::create($validated);

            $skpd = Skpd::findOrFail($validated['skpd_id']);
            
            return redirect()->route('perangkatdaerah.show', $skpd->user_id)
                ->with('success', 'Tugas berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error adding SKPD task: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $skpdTugas = SkpdTugas::findOrFail($id);
            $skpd = Skpd::findOrFail($skpdTugas->skpd_id);
            $skpdTugas->delete();
            
            return redirect()->route('perangkatdaerah.show', $skpd->user_id)
                ->with('success', 'Tugas berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting SKPD task: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}