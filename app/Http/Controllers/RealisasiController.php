<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RealisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $realisasi = Realisasi::all();

        return Inertia::render('Realisasi', [
            'realisasi' => $realisasi,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Realisasi/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'anggaran' => 'required|numeric',
            'kinerja' => 'required|string|max:255',
            'triwulan' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        Realisasi::create($request->all());

        return redirect()->route('realisasi.index')->with('success', 'Realisasi created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        return Inertia::render('Realisasi/Show', ['realisasi' => $realisasi]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        return Inertia::render('Realisasi/Edit', [
            'realisasi' => $realisasi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'anggaran' => 'required|numeric',
            'kinerja' => 'required|string|max:255',
            'triwulan' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        $realisasi = Realisasi::findOrFail($id);
        $realisasi->update($request->all());

        return redirect()->route('realisasi.index')->with('success', 'Realisasi updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $realisasi = Realisasi::findOrFail($id);
        $realisasi->delete();

        return redirect()->route('realisasi.index')->with('success', 'Realisasi deleted successfully');
    }
}
