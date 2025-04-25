<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Bantuan;
use App\Models\StatusBantuan;
use App\Http\Requests\BantuanRequest;

class BantuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Bantuan');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statusBantuanOptions = StatusBantuan::all();
        return Inertia::render('Bantuan/Create', [
            'statusBantuanOptions' => $statusBantuanOptions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_bantuan' => 'required|string|max:255',
            'penerima' => 'nullable|string',
            'tanggal_disalurkan' => 'nullable|string',
            'status_bantuan_id' => 'nullable|exists:status,id',
        ]);

        $bantuan = Bantuan::create($request->all());

        // return response()->route('bantuan.index')->with('success', 'Bantuan created successfully');
        return redirect()->route('bantuan.index')->with('success', 'Bantuan created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bantuan = Bantuan::findOrFail($id);
        return Inertia::render('Bantuan/Show', [
            'bantuan' => $bantuan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bantuan = Bantuan::findOrFail($id);
        return Inertia::render('Bantuan/Edit', [
            'bantuan' => $bantuan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'jenis_bantuan' => 'required|string|max:255',
            'penerima' => 'nullable|string',
            'tanggal_disalurkan' => 'nullable|string',
            'status_bantuan_id' => 'nullable|exists:status,id',
        ]);
        $bantuan = Bantuan::findOrFail($id);
        $bantuan->update($request->all());

        // return response()->route('bantuan.index')->with('success', 'Bantuan updated successfully');
        return redirect()->route('bantuan.index')->with('success', 'Bantuan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bantuan = Bantuan::findOrFail($id);
        $bantuan->delete();

        // return response()->route('bantuan.index')->with('success', 'Bantuan deleted successfully');
        return redirect()->route('bantuan.index')->with('success', 'Bantuan deleted successfully');
    }
}
