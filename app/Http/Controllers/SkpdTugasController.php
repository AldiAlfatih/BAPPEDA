<?php

namespace App\Http\Controllers;

use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SkpdTugasController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skpd_id' => 'required|exists:skpd,id',
            'nomenklatur_ids' => 'required|array',
            'nomenklatur_ids.*' => 'exists:kode_nomenklatur,id',
            'is_aktif' => 'required|integer'
        ]);

        $skpdId = $validated['skpd_id'];
        $isAktif = $validated['is_aktif'];
        $addedCount = 0;
        $skippedCount = 0;
        $skippedNomenklaturs = [];

        try {
            foreach ($validated['nomenklatur_ids'] as $nomenklaturId) {
                $existingTask = SkpdTugas::where('skpd_id', $skpdId)
                    ->where('kode_nomenklatur_id', $nomenklaturId)
                    ->first();
                
                if (!$existingTask) {
                    SkpdTugas::create([
                        'skpd_id' => $skpdId,
                        'kode_nomenklatur_id' => $nomenklaturId,
                        'is_aktif' => $isAktif
                    ]);
                    $addedCount++;
                } else {
                    $skippedCount++;
                    $nomenklatur = KodeNomenklatur::find($nomenklaturId);
                    if ($nomenklatur) {
                        $skippedNomenklaturs[] = $nomenklatur->nomor_kode . ' - ' . $nomenklatur->nomenklatur;
                    }
                }
            }

            $skpd = Skpd::findOrFail($skpdId);
            
            if ($addedCount > 0 && $skippedCount > 0) {
                return redirect()->back()
                    ->with('success', "$addedCount tugas berhasil ditambahkan dan $skippedCount tugas dilewati karena sudah ada sebelumnya.");
            } elseif ($addedCount > 0) {
                return redirect()->back()
                    ->with('success', 'Tugas berhasil ditambahkan');
            } else {
                $message = 'Semua tugas sudah ada sebelumnya';
                if (!empty($skippedNomenklaturs)) {
                    $message .= ': ' . implode(', ', array_slice($skippedNomenklaturs, 0, 3));
                    if (count($skippedNomenklaturs) > 3) {
                        $message .= ', dan ' . (count($skippedNomenklaturs) - 3) . ' lainnya';
                    }
                }
                return redirect()->back()
                    ->with('info', $message);
            }
        } catch (\Exception $e) {
            Log::error('Error adding SKPD task: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $skpdTugas = SkpdTugas::findOrFail($id);
            $userId = Skpd::findOrFail($skpdTugas->skpd_id)->user_id;
            $skpdTugas->delete();
            
            return redirect()->back()
                ->with('success', 'Tugas berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting SKPD task: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}