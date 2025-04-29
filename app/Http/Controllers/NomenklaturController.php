<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nomenklatur;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class NomenklaturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {     
          // Mengambil data dari tabel dengan memilih kolom yang dibutuhkan
    $nomenklatur = Nomenklatur::all();

    return Inertia::render('Nomenklatur', [
        'nomenklatur' => $nomenklatur,
    ]);
    }
         
    
        // This method is currently empty. You can implement the logic to display a list of resources.
        // return Inertia::render('Nomenklatur');
        // return Inertia::render('Nomenklatur/Index', [
        //     'nomenklaturItems' => $nomenklaturItems,
        // ]);

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('nomenklatur/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kode' => 'required|string|max:10',
            'nomenklatur' => 'required|string|max:255',
            'urusan' => 'required|string|max:255',
            'bidang_urusan' => 'required|string|max:255',
            'program' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'subkegiatan' => 'required|string|max:255',
        ]);

        Nomenklatur::create($request->all());

        return redirect()->route('nomenklatur.index')->with('success', 'Nomenklatur created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nomenklatur = Nomenklatur::findOrFail($id);
        return Inertia::render('Nomenklatur/Show', ['nomenklatur' => $nomenklatur]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        // $nomenklatur = Nomenklatur::findOrFail($id);
        // return Inertia::render('Nomenklatur/Edit', [
        //     'nomenklatur' => $nomenklatur,
        // ]);
        $nomenklatur = Nomenklatur::findOrFail($id); // Tambahkan log ini untuk debug
        return Inertia::render('nomenklatur/Edit', [
            'nomenklatur' => $nomenklatur,
        ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kode' => 'required|string|max:10',
            'nomenklatur' => 'required|string|max:255',
            'urusan' => 'required|string|max:255',
            'bidang_urusan' => 'required|string|max:255',
            'program' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'subkegiatan' => 'required|string|max:255',
        ]);

        $nomenklatur = Nomenklatur::findOrFail($id);
        $nomenklatur->update($request->all());

        return redirect()->route('nomenklatur.index')->with('success', 'Nomenklatur updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $nomenklatur = Nomenklatur::findOrFail($id);
        $nomenklatur->delete();

        return redirect()->route('nomenklatur.index')->with('success', 'Nomenklatur deleted successfully');
    }
}
