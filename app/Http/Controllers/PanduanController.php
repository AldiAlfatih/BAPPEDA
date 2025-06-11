<?php

namespace App\Http\Controllers;

use App\Models\Panduan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PanduanController extends Controller
{
    public function index()
    {
        $panduan = Panduan::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'file_url' => $item->file ? asset('storage/' . $item->file) : null,
                'sampul_url' => $item->sampul ? asset('storage/' . $item->sampul) : null,
            ];
        });

        return Inertia::render('Panduan', ['panduan' => $panduan]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'sampul' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $panduan = new Panduan();
        $panduan->judul = $validated['judul'];
        $panduan->deskripsi = $validated['deskripsi'];

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('panduan_files', 'public');
            $panduan->file = $filePath;
        } else {
            $panduan->file = null;
        }

        if ($request->hasFile('sampul')) {
            $sampulPath = $request->file('sampul')->store('panduan_sampul', 'public');
            $panduan->sampul = $sampulPath;
        } else {
            $panduan->sampul = null;
        }

        $panduan->save();

        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $panduan = Panduan::findOrFail($id);

        return Inertia::render('Panduan/Edit', ['panduan' => $panduan]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'sampul' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $panduan = Panduan::findOrFail($id);

        $panduan->judul = $validated['judul'];
        $panduan->deskripsi = $validated['deskripsi'];

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($panduan->file) {
                Storage::disk('public')->delete($panduan->file);
            }
            $filePath = $request->file('file')->store('panduan_files', 'public');
            $panduan->file = $filePath;
        }

        if ($request->hasFile('sampul')) {
            // Hapus sampul lama jika ada
            if ($panduan->sampul) {
                Storage::disk('public')->delete($panduan->sampul);
            }
            $sampulPath = $request->file('sampul')->store('panduan_sampul', 'public');
            $panduan->sampul = $sampulPath;
        }

        $panduan->save();

        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $panduan = Panduan::findOrFail($id);

        // Hapus file dan sampul dari storage jika ada
        if ($panduan->file) {
            Storage::disk('public')->delete($panduan->file);
        }
        if ($panduan->sampul) {
            Storage::disk('public')->delete($panduan->sampul);
        }

        $panduan->delete();

        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil dihapus.');
    }
}
