<?php

namespace App\Http\Controllers;

use App\Models\Pemberitahuan;
use Illuminate\Http\Request;

class PemberitahuanController extends Controller
{
    public function index()
    {
        $pemberitahuan = Pemberitahuan::latest()->get();
        return inertia('Pemberitahuan', [
            'pemberitahuan' => $pemberitahuan,
        ]);
    }

    public function create()
    {
        return inertia('Pemberitahuan/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_dibuat' => 'required|date',
        ]);

        Pemberitahuan::create($request->only(['judul', 'isi', 'tanggal_dibuat']));

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dibuat.');
    }

    public function show(Pemberitahuan $pemberitahuan)
    {
        return inertia('Pemberitahuan/Show', [
            'pemberitahuan' => $pemberitahuan,
        ]);
    }

    public function edit(Pemberitahuan $pemberitahuan)
    {
        return inertia('Pemberitahuan/Edit', [
            'pemberitahuan' => $pemberitahuan,
        ]);
    }

    public function update(Request $request, Pemberitahuan $pemberitahuan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_dibuat' => 'required|date',
        ]);

        $pemberitahuan->update($request->only(['judul', 'isi', 'tanggal_dibuat']));

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil diperbarui.');
    }

    public function destroy(Pemberitahuan $pemberitahuan)
    {
        $pemberitahuan->delete();

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dihapus.');
    }
}
