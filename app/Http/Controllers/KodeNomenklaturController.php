<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KodeNomenklatur;
use App\Models\KodeNomenklaturDetail;
use Inertia\Inertia;

class KodeNomenklaturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil data dari tabel KodeNomenklatur dan relasinya
        $kodenomenklatur = KodeNomenklatur::with('detail')->get();

        return Inertia::render('Kodenomenklatur', [
            'kodenomenklatur' => $kodenomenklatur,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('kode_nomenklatur/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nomor_kode' => 'required|string|max:10',
            'nomenklatur' => 'required|string|max:255',
            'jenis_kode' => 'required|string|max:255',
            'urusan' => 'nullable|string|max:255',
            'bidang_urusan' => 'nullable|string|max:255',
            'program' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'subkegiatan' => 'nullable|string|max:255',
        ]);

        // Membuat data KodeNomenklatur
        $kode = KodeNomenklatur::create([
            'nomor_kode' => $request->nomor_kode,
            'nomenklatur' => $request->nomenklatur,
            'jenis_kode' => $request->jenis_kode,
        ]);

        // Menambahkan detail relasi ke KodeNomenklaturDetail
        $kode->detail()->create([
            'urusan' => $request->urusan,
            'bidang_urusan' => $request->bidang_urusan,
            'program' => $request->program,
            'kegiatan' => $request->kegiatan,
            'subkegiatan' => $request->subkegiatan,
        ]);

        return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kode = KodeNomenklatur::with('detail')->findOrFail($id);

        return Inertia::render('KodeNomenklatur/Show', [
            'kodenomenklatur' => $kode,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $kode = KodeNomenklatur::with('detail')->findOrFail($id);

        return Inertia::render('KodeNomenklatur/Edit', [
            'kodenomenklatur' => $kode,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nomor_kode' => 'required|string|max:10',
            'nomenklatur' => 'required|string|max:255',
            'jenis_kode' => 'required|string|max:255',
            'urusan' => 'nullable|string|max:255',
            'bidang_urusan' => 'nullable|string|max:255',
            'program' => 'nullable|string|max:255',
            'kegiatan' => 'nullable|string|max:255',
            'subkegiatan' => 'nullable|string|max:255',
        ]);

        $kode = KodeNomenklatur::findOrFail($id);
        $kode->update([
            'nomor_kode' => $request->nomor_kode,
            'nomenklatur' => $request->nomenklatur,
            'jenis_kode' => $request->jenis_kode,
        ]);

        if ($kode->detail) {
            $kode->detail->update([
                'urusan' => $request->urusan,
                'bidang_urusan' => $request->bidang_urusan,
                'program' => $request->program,
                'kegiatan' => $request->kegiatan,
                'subkegiatan' => $request->subkegiatan,
            ]);
        } else {
            $kode->detail()->create([
                'urusan' => $request->urusan,
                'bidang_urusan' => $request->bidang_urusan,
                'program' => $request->program,
                'kegiatan' => $request->kegiatan,
                'subkegiatan' => $request->subkegiatan,
            ]);
        }

        return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kode = KodeNomenklatur::findOrFail($id);
        $kode->detail()->delete(); // Hapus detail jika ada
        $kode->delete(); // Hapus data utama

        return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur deleted successfully');
    }
}
