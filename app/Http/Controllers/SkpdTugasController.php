<?php

namespace App\Http\Controllers;

use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
     * Implements hierarchical deletion of tasks based on nomenklatur hierarchy
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Get the task to delete
            $task = SkpdTugas::findOrFail($id);
            $skpdId = $task->skpd_id;

            // Get the nomenklatur associated with this task
            $nomenklaturId = $task->kode_nomenklatur_id;
            $nomenklatur = KodeNomenklatur::findOrFail($nomenklaturId);
            $jenisNomenklatur = $nomenklatur->jenis_nomenklatur;

            // Find child tasks to delete based on hierarchy
            $childTasksToDelete = [];

            // First identify which tasks should be deleted based on the hierarchy
            if ($jenisNomenklatur === 0) { // Urusan
                // Find all child tasks (Bidang Urusan, Program, Kegiatan, SubKegiatan) related to this Urusan
                $childNomenklaturs = $this->findChildNomenklatursForUrusan($nomenklaturId);
                $childTasksToDelete = $this->findSkpdTasksWithNomenklaturs($skpdId, $childNomenklaturs);
            }
            else if ($jenisNomenklatur === 1) { // Bidang Urusan
                // Find all child tasks (Program, Kegiatan, SubKegiatan) related to this Bidang Urusan
                $childNomenklaturs = $this->findChildNomenklatursForBidangUrusan($nomenklaturId);
                $childTasksToDelete = $this->findSkpdTasksWithNomenklaturs($skpdId, $childNomenklaturs);
            }
            else if ($jenisNomenklatur === 2) { // Program
                // Find all child tasks (Kegiatan, SubKegiatan) related to this Program
                $childNomenklaturs = $this->findChildNomenklatursForProgram($nomenklaturId);
                $childTasksToDelete = $this->findSkpdTasksWithNomenklaturs($skpdId, $childNomenklaturs);
            }
            else if ($jenisNomenklatur === 3) { // Kegiatan
                // Find all child tasks (SubKegiatan) related to this Kegiatan
                $childNomenklaturs = $this->findChildNomenklatursForKegiatan($nomenklaturId);
                $childTasksToDelete = $this->findSkpdTasksWithNomenklaturs($skpdId, $childNomenklaturs);
            }
            // For jenisNomenklatur 4 (SubKegiatan), no child tasks to delete

            // Count how many tasks will be deleted
            $totalDeleteCount = count($childTasksToDelete) + 1; // +1 for the current task

            // Delete child tasks if any exist
            if (!empty($childTasksToDelete)) {
                SkpdTugas::whereIn('id', $childTasksToDelete)->delete();
            }

            // Delete the original task
            $task->delete();

            DB::commit();

            // Prepare success message
            $message = "Berhasil menghapus tugas";
            if ($totalDeleteCount > 1) {
                $message .= " beserta " . ($totalDeleteCount - 1) . " tugas terkait dibawahnya";
            }

            return redirect()->back()->with('success', $message);
        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting SKPD task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus tugas: ' . $e->getMessage());
        }
    }

    /**
     * Find all child nomenklaturs for an Urusan
     * @param int $urusanId The Urusan ID
     * @return array Array of nomenklatur IDs
     */
    private function findChildNomenklatursForUrusan($urusanId)
    {
        // Get all bidang urusan related to this urusan
        $bidangUrusanIds = DB::table('kode_nomenklatur_detail')
            ->where('id_urusan', $urusanId)
            ->pluck('id_nomenklatur')
            ->toArray();

        $allNomenklaturIds = $bidangUrusanIds;

        // For each bidang urusan, get all programs
        foreach ($bidangUrusanIds as $bidangId) {
            $programIds = $this->findChildNomenklatursForBidangUrusan($bidangId);
            $allNomenklaturIds = array_merge($allNomenklaturIds, $programIds);
        }

        return array_unique($allNomenklaturIds);
    }

    /**
     * Find all child nomenklaturs for a Bidang Urusan
     * @param int $bidangUrusanId The Bidang Urusan ID
     * @return array Array of nomenklatur IDs
     */
    private function findChildNomenklatursForBidangUrusan($bidangUrusanId)
    {
        // Get all programs related to this bidang urusan
        $programIds = DB::table('kode_nomenklatur_detail')
            ->where('id_bidang_urusan', $bidangUrusanId)
            ->pluck('id_nomenklatur')
            ->toArray();

        $allNomenklaturIds = $programIds;

        // For each program, get all kegiatan
        foreach ($programIds as $programId) {
            $kegiatanIds = $this->findChildNomenklatursForProgram($programId);
            $allNomenklaturIds = array_merge($allNomenklaturIds, $kegiatanIds);
        }

        return array_unique($allNomenklaturIds);
    }

    /**
     * Find all child nomenklaturs for a Program
     * @param int $programId The Program ID
     * @return array Array of nomenklatur IDs
     */
    private function findChildNomenklatursForProgram($programId)
    {
        // Get all kegiatan related to this program
        $kegiatanIds = DB::table('kode_nomenklatur_detail')
            ->where('id_program', $programId)
            ->pluck('id_nomenklatur')
            ->toArray();

        $allNomenklaturIds = $kegiatanIds;

        // For each kegiatan, get all sub kegiatan
        foreach ($kegiatanIds as $kegiatanId) {
            $subKegiatanIds = $this->findChildNomenklatursForKegiatan($kegiatanId);
            $allNomenklaturIds = array_merge($allNomenklaturIds, $subKegiatanIds);
        }

        return array_unique($allNomenklaturIds);
    }

    /**
     * Find all child nomenklaturs for a Kegiatan
     * @param int $kegiatanId The Kegiatan ID
     * @return array Array of nomenklatur IDs
     */
    private function findChildNomenklatursForKegiatan($kegiatanId)
    {
        // Get all subkegiatan related to this kegiatan
        $subKegiatanIds = DB::table('kode_nomenklatur_detail')
            ->where('id_kegiatan', $kegiatanId)
            ->pluck('id_nomenklatur')
            ->toArray();

        return $subKegiatanIds;
    }

    /**
     * Find all SKPD tasks associated with given nomenklatur IDs
     * @param int $skpdId SKPD ID
     * @param array $nomenklaturIds Array of nomenklatur IDs
     * @return array Array of SKPD task IDs
     */
    private function findSkpdTasksWithNomenklaturs($skpdId, $nomenklaturIds)
    {
        if (empty($nomenklaturIds)) {
            return [];
        }

        return SkpdTugas::where('skpd_id', $skpdId)
            ->whereIn('kode_nomenklatur_id', $nomenklaturIds)
            ->pluck('id')
            ->toArray();
}
}
