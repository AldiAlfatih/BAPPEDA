<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $panduan = Panduan::all();
        return Inertia::render('Panduan', [
            'panduan' => $panduan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Panduan/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);
        
        Panduan::create($requests->all());
        return redirect()->route('panduan.index')->with('success', 'Panduan created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $panduan = Panduan::findOrFail($id);
        return Inertia::render('Panduan/Show', ['panduan' => $panduan]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $panduan = Panduan::findOrFail($id);
        return Inertia::render('Panduan/Edit', ['panduan' => $panduan]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $panduan->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $panduan = Panduan::findOrFail($id);
        $panduan->update($request->all());
        return redirect()->route('panduan.index')->with('success', 'Panduan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $panduan = Panduan::findOrFail($id);
        $panduan->delete();
        return redirect()->route('panduan.index')->with('success', 'Panduan deleted successfully');
    }
}
