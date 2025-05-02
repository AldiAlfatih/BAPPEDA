<?php

namespace App\Http\Controllers;

use App\Models\KodeNomenklatur;
use App\Models\KodeNomenklaturDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class KodeNomenklaturController extends Controller
{
    public function index(): Response
    {
        $data = KodeNomenklatur::with(['details', 'bidangUrusan', 'program', 'kegiatan', 'subkegiatan'])->get();

        return Inertia::render('KodeNomenklatur', [
            'kodeNomenklatur' => $data
        ]);
    }

    public function create(): Response
{
    return Inertia::render('KodeNomenklatur/Create', [
        'urusanList' => KodeNomenklatur::where('jenis_nomenklatur', 0)->get(),
        'bidangUrusanList' => KodeNomenklatur::where('jenis_nomenklatur', 1)->get(),
        'programList' => KodeNomenklatur::where('jenis_nomenklatur', 2)->get(),
        'kegiatanList' => KodeNomenklatur::where('jenis_nomenklatur', 3)->get(),
    ]);
}




public function store(Request $request)
{
    $validated = $request->validate([
        'jenis_nomenklatur' => 'required|integer',
        'nomor_kode' => 'required|string',
        'nomenklatur' => 'nullable|string',
        'urusan' => 'nullable|exists:kode_nomenklatur,id',
        'bidang_urusan' => 'nullable|exists:kode_nomenklatur,id',
        'program' => 'nullable|exists:kode_nomenklatur,id',
        'kegiatan' => 'nullable|exists:kode_nomenklatur,id',
        'subkegiatan' => 'nullable|exists:kode_nomenklatur,id',
    ]);

    DB::beginTransaction();

    try {
        $nomenklatur = KodeNomenklatur::create([
            'jenis_nomenklatur' => $validated['jenis_nomenklatur'],
            'nomor_kode' => $validated['nomor_kode'],
            'nomenklatur' => $validated['nomenklatur'] ?? null,
            'id_bidang_urusan' => $request->bidang_urusan,
            'id_program' => $request->program,
            'id_kegiatan' => $request->kegiatan,
            'id_subkegiatan' => $request->subkegiatan,
        ]);

        $detailData = [
            'id_nomenklatur' => $nomenklatur->id,
        ];

        switch ((int) $validated['jenis_nomenklatur']) {
            case 0:
                $detailData['urusan'] = $validated['nomenklatur'];
                break;
            case 1:
                $detailData['bidang_urusan'] = $validated['nomenklatur'];
                break;
            case 2:
                $detailData['program'] = $validated['nomenklatur'];
                break;
            case 3:
                $detailData['kegiatan'] = $validated['nomenklatur'];
                break;
            case 4:
                $detailData['subkegiatan'] = $validated['nomenklatur'];
                break;
        }

        KodeNomenklaturDetail::create($detailData);

        DB::commit();

        return redirect()->route('kodenomenklatur.index')->with('success', 'Data berhasil ditambahkan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
    }
}


    public function edit($id): Response
    {
        $data = KodeNomenklatur::findOrFail($id);

        return Inertia::render('KodeNomenklatur/Edit', [
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_nomenklatur' => 'sometimes|integer',
            'nomor_kode' => 'sometimes|string',
            'nomenklatur' => 'nullable|string',
        ]);

        $nomenklatur = KodeNomenklatur::findOrFail($id);
        $nomenklatur->update($validated);

        return redirect()->route('kodenomenklatur.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $nomenklatur = KodeNomenklatur::findOrFail($id);
        $nomenklatur->delete();

        return redirect()->route('kodenomenklatur.index')->with('success', 'Data berhasil dihapus.');
    }
}
