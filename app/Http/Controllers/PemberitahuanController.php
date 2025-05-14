<?php

namespace App\Http\Controllers;

use App\Models\Pemberitahuan;
use Illuminate\Http\Request;

class PemberitahuanController extends Controller
{
    /**
     * Menampilkan daftar pemberitahuan.
     */
    public function index()
    {
        $pemberitahuan = Pemberitahuan::all();
        return view('Pemberitahuan', compact('pemberitahuan'));
    }

    /**
     * Menampilkan pemberitahuan berdasarkan ID.
     */
    public function show($id)
    {
        $pemberitahuan = Pemberitahuan::find($id);

        if (!$pemberitahuan) {
            return redirect()->route('pemberitahuan.index')->with('error', 'Pemberitahuan tidak ditemukan');
        }

        return view('pemberitahuan.show', compact('pemberitahuan'));
    }

    /**
     * Menyimpan pemberitahuan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_kirim' => 'required|date',
        ]);

        Pemberitahuan::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'tanggal_kirim' => $request->tanggal_kirim,
        ]);

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dibuat');
    }

    /**
     * Menampilkan form untuk membuat pemberitahuan baru.
     */
    public function create()
    {
        return view('pemberitahuan.create');
    }

    /**
     * Menampilkan form untuk mengedit pemberitahuan.
     */
    public function edit($id)
    {
        $pemberitahuan = Pemberitahuan::find($id);

        if (!$pemberitahuan) {
            return redirect()->route('pemberitahuan.index')->with('error', 'Pemberitahuan tidak ditemukan');
        }

        return view('pemberitahuan.edit', compact('pemberitahuan'));
    }

    /**
     * Mengupdate pemberitahuan yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_kirim' => 'required|date',
        ]);

        $pemberitahuan = Pemberitahuan::find($id);

        if (!$pemberitahuan) {
            return redirect()->route('pemberitahuan.index')->with('error', 'Pemberitahuan tidak ditemukan');
        }

        $pemberitahuan->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'tanggal_kirim' => $request->tanggal_kirim,
        ]);

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil diperbarui');
    }

    /**
     * Menghapus pemberitahuan.
     */
    public function destroy($id)
    {
        $pemberitahuan = Pemberitahuan::find($id);

        if (!$pemberitahuan) {
            return redirect()->route('pemberitahuan.index')->with('error', 'Pemberitahuan tidak ditemukan');
        }

        $pemberitahuan->delete();

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dihapus');
    }
}
