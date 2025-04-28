<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::with('admin')->get(); // Menampilkan periode dengan informasi admin
        return Inertia::render('Periode/Index', [
            'periodes' => $periodes
        ]);
    }

    public function create()
    {
        return Inertia::render('Periode/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_admin' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        Periode::create($request->all());

        return redirect()->route('periode.index')->with('success', 'Periode created successfully');
    }

    public function edit($id)
    {
        $periode = Periode::findOrFail($id);
        return Inertia::render('Periode/Edit', [
            'periode' => $periode
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_admin' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $periode = Periode::findOrFail($id);
        $periode->update($request->all());

        return redirect()->route('periode.index')->with('success', 'Periode updated successfully');
    }

    public function destroy($id)
    {
        $periode = Periode::findOrFail($id);
        $periode->delete();

        return redirect()->route('periode.index')->with('success', 'Periode deleted successfully');
    }
}
