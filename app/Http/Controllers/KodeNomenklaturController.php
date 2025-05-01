<?php
namespace App\Http\Controllers;

use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KodeNomenklaturController extends Controller
{
    /**
     * Menampilkan semua kode nomenklatur.
     */
    public function index()
    {
        $kodeNomenklatur = KodeNomenklatur::all();
        return Inertia::render('KodeNomenklatur/Index', [
            'kodeNomenklatur' => $kodeNomenklatur,
        ]);
    }

    /**
     * Menampilkan form untuk membuat kode nomenklatur baru.
     */
    public function create()
    {
        return Inertia::render('KodeNomenklatur/Create');
    }

    /**
     * Menyimpan kode nomenklatur yang baru dibuat.
     */
    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'nomor_kode' => 'required|unique:kode_nomenklatur',
        'nomenklatur' => 'required|string',
        'jenis_nomenklatur' => 'required|integer',
        'urusan' => 'required|string',
        'bidang_urusan' => 'nullable|string',
        'program' => 'nullable|string',
        'kegiatan' => 'nullable|string',
        'subkegiatan' => 'nullable|string',
    ]);

    // Menyimpan data kode nomenklatur
    $kodeNomenklatur = KodeNomenklatur::create([
        'nomor_kode' => $request->nomor_kode,
        'nomenklatur' => $request->nomenklatur,
        'jenis_nomenklatur' => $request->jenis_nomenklatur,
        'urusan' => $request->urusan,
        'bidang_urusan' => $request->bidang_urusan,
        'program' => $request->program,
        'kegiatan' => $request->kegiatan,
        'subkegiatan' => $request->subkegiatan,
    ]);

    return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur berhasil disimpan.');
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
