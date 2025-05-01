<?php

namespace App\Http\Controllers;

use App\Models\KodeNomenklatur;
use App\Models\Urusan;
use App\Models\BidangUrusan;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KodeNomenklaturController extends Controller
{
    /**
     * Menampilkan semua kode nomenklatur.
     */
    public function index()
    {
        $kodeNomenklatur = KodeNomenklatur::with('urusan', 'bidangUrusan', 'program', 'kegiatan', 'subKegiatan')->get();
        return Inertia::render('KodeNomenklatur', [
            'kodeNomenklatur' => $kodeNomenklatur
        ]);
    }

    /**
     * Menampilkan form untuk membuat kode nomenklatur baru.
     */
    public function create()
    {
        return Inertia::render('KodeNomenklatur/Create', [
            'kodenomenklatur' => KodeNomenklatur::all() // Menyediakan semua kode nomenklatur
        ]);
    }

 
    public function store(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'nomor_kode' => 'required|unique:kode_nomenklatur',
        'nomenklatur' => 'required|string',
        'jenis_kode' => 'required|integer',
        'urusan' => 'required|string',
        'bidang_urusan' => 'nullable|string',
        'program' => 'nullable|string',
        'kegiatan' => 'nullable|string',
        'subkegiatan' => 'nullable|string',
    ]);

    // Debug: Menampilkan data yang diterima
    // dd($validated); 

    // Simpan kode nomenklatur
    $kodeNomenklatur = KodeNomenklatur::create([
        'nomor_kode' => $validated['nomor_kode'],
        'nomenklatur' => $validated['nomenklatur'],
        'jenis_nomenklatur' => $validated['jenis_kode'],
        'parent_id' => $validated['parent_id'] ?? null, // Menyimpan parent_id jika ada
    ]);

    // Menyimpan urusan yang terkait dengan kode_nomenklatur
    $urusan = Urusan::create([
        'kode_nomenklatur_id' => $kodeNomenklatur->id,
        'nama' => $validated['urusan']
    ]);

    // Menyimpan bidang urusan yang terkait dengan urusan
    if (!empty($validated['bidang_urusan'])) {
        $bidangUrusan = BidangUrusan::create([
            'id_urusan' => $urusan->id,
            'nama' => $validated['bidang_urusan']
        ]);
    }

    // Menyimpan program yang terkait dengan bidang urusan
    if (!empty($validated['program'])) {
        $program = Program::create([
            'id_bid_urusan' => $bidangUrusan->id ?? null,
            'nama' => $validated['program']
        ]);
    }

    // Menyimpan kegiatan yang terkait dengan program
    if (!empty($validated['kegiatan'])) {
        $kegiatan = Kegiatan::create([
            'id_program' => $program->id ?? null,
            'nama' => $validated['kegiatan']
        ]);
    }

    // Menyimpan sub kegiatan yang terkait dengan kegiatan
    if (!empty($validated['subkegiatan'])) {
        SubKegiatan::create([
            'id_kegiatan' => $kegiatan->id ?? null,
            'nama' => $validated['subkegiatan']
        ]);
    }

    return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur dan data terkait berhasil disimpan.');
}


    /**
     * Menampilkan form untuk mengedit kode nomenklatur yang ada.
     */
    public function edit($id)
    {
        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        return Inertia::render('KodeNomenklatur/Edit', [
            'kodeNomenklatur' => $kodeNomenklatur
        ]);
    }

    /**
     * Memperbarui data kode nomenklatur yang ada.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nomor_kode' => 'required|unique:kode_nomenklatur,nomor_kode,' . $id,
            'nomenklatur' => 'required|string',
            'jenis_nomenklatur' => 'required|integer',
            'urusan' => 'required|string',
            'bidang_urusan' => 'nullable|string',
            'program' => 'nullable|string',
            'kegiatan' => 'nullable|string',
            'subkegiatan' => 'nullable|string',
        ]);

        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        $kodeNomenklatur->update([
            'nomor_kode' => $request->nomor_kode,
            'nomenklatur' => $request->nomenklatur,
            'jenis_nomenklatur' => $request->jenis_nomenklatur,
            'urusan' => $request->urusan,
            'bidang_urusan' => $request->bidang_urusan,
            'program' => $request->program,
            'kegiatan' => $request->kegiatan,
            'subkegiatan' => $request->subkegiatan,
        ]);

        return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur berhasil diperbarui.');
    }

    /**
     * Menghapus kode nomenklatur yang ada.
     */
    public function destroy($id)
    {
        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        $kodeNomenklatur->delete();

        return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur berhasil dihapus.');
    }
}
