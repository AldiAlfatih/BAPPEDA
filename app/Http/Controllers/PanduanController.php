<?php

namespace App\Http\Controllers;

use App\Models\Panduan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PanduanController extends Controller
{
    /**
     * Menampilkan daftar panduan.
     */
    public function index()
    {
        $panduan = Panduan::all();
    
        // Tambahkan file_url dan sampul_url secara manual
        $panduan->transform(function ($p) {
            $p->file_url = $p->file ? Storage::url($p->file) : null;
            $p->sampul_url = $p->sampul ? Storage::url($p->sampul) : null;
            return $p;
        });
    
        return Inertia::render('Panduan', [
            'panduan' => $panduan,
        ]);
    }
    

    /**
     * Menampilkan formulir untuk membuat panduan baru.
     */
    public function create()
    {
        return Inertia::render('Panduan/Create');
    }

    /**
     * Menyimpan panduan baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'sampul' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Menyimpan data panduan
        $panduan = new Panduan();
        $panduan->judul = $request->judul;
        $panduan->deskripsi = $request->deskripsi;

        // Jika file panduan ada, simpan ke storage
        if ($request->hasFile('file')) {
            $panduan->file = $request->file('file')->store('panduan_files', 'public');
        }

        // Jika gambar sampul ada, simpan ke storage
        if ($request->hasFile('sampul')) {
            $panduan->sampul = $request->file('sampul')->store('panduan_sampul', 'public');
        }

        // Menyimpan data panduan ke database
        $panduan->save();

        // Mengirimkan data terbaru setelah disimpan ke Inertia
        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil ditambahkan.');
    }

    /**
     * Menampilkan panduan yang dipilih.
     */
    public function show(string $id)
    {
        $panduan = Panduan::findOrFail($id);  // Menampilkan panduan berdasarkan ID
        return Inertia::render('Panduan/Show', ['panduan' => $panduan]);
    }

    /**
     * Menampilkan formulir untuk mengedit panduan.
     */
    public function edit(string $id)
    {
        $panduan = Panduan::findOrFail($id);  // Menampilkan panduan berdasarkan ID
        return Inertia::render('panduan/Edit', ['panduan' => $panduan]);
    }

    /**
     * Memperbarui panduan yang dipilih.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'sampul' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $panduan = Panduan::findOrFail($id);

        // Tentukan file path default menggunakan file lama
        $filePath = $panduan->file;
        $sampulPath = $panduan->sampul;

        // Jika ada file baru, simpan file baru dan hapus file lama jika ada
        if ($request->hasFile('file')) {
            if ($panduan->file) {
                Storage::disk('public')->delete($panduan->file);  // Hapus file lama
            }
            $filePath = $request->file('file')->store('panduan_files', 'public');
        }

        // Jika ada gambar sampul baru, simpan gambar baru dan hapus gambar lama jika ada
        if ($request->hasFile('sampul')) {
            if ($panduan->sampul) {
                Storage::disk('public')->delete($panduan->sampul);  // Hapus sampul lama
            }
            $sampulPath = $request->file('sampul')->store('panduan_sampul', 'public');
        }

        // Update data panduan
        $panduan->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file' => $filePath,
            'sampul' => $sampulPath,
        ]);

        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil diperbarui');
    }

    /**
     * Menghapus panduan yang dipilih.
     */
    public function destroy(string $id)
    {
        $panduan = Panduan::findOrFail($id);

        // Menghapus file terkait jika ada
        if ($panduan->file) {
            Storage::disk('public')->delete($panduan->file);
        }

        // Menghapus sampul terkait jika ada
        if ($panduan->sampul) {
            Storage::disk('public')->delete($panduan->sampul);
        }

        // Menghapus panduan
        $panduan->delete();

        // Mengarahkan ulang ke halaman index Panduan dan mengirimkan data terbaru
        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil dihapus.');
    }
}
