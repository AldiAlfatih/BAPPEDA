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
            $fileUrl = null;
            $sampulUrl = null;

            // Build URLs only if file exists on public disk
            if ($item->file && Storage::disk('public')->exists($item->file)) {
                $fileUrl = Storage::url($item->file);
            } else if ($item->file) {
                \Log::warning('Panduan file missing on disk', ['path' => $item->file]);
            }

            if ($item->sampul && Storage::disk('public')->exists($item->sampul)) {
                $sampulUrl = Storage::url($item->sampul);
            } else if ($item->sampul) {
                \Log::warning('Panduan sampul missing on disk', ['path' => $item->sampul]);
            }

            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'file_url' => $fileUrl,
                'sampul_url' => $sampulUrl,
            ];
        });

        return Inertia::render('Panduan', ['panduan' => $panduan]);
    }

    public function store(Request $request)
    {
        try {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            // Naikkan batas agar tidak mudah gagal upload di produksi
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB
            'sampul' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // 4MB
        ]);

        $panduan = new Panduan();
        $panduan->judul = $validated['judul'];
        $panduan->deskripsi = $validated['deskripsi'];

            // Handle file upload dengan error checking
        if ($request->hasFile('file')) {
                $file = $request->file('file');
                \Log::info('Uploading file: ' . $file->getClientOriginalName() . ' Size: ' . $file->getSize());
                
                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('panduan_files')) {
                    Storage::disk('public')->makeDirectory('panduan_files');
                }
                
                $filePath = $file->store('panduan_files', 'public');
                if ($filePath) {
            $panduan->file = $filePath;
                    \Log::info('File uploaded successfully to: ' . $filePath);
                } else {
                    \Log::error('Failed to upload file');
                    return back()->withErrors(['file' => 'Gagal mengunggah file.']);
                }
        } else {
            $panduan->file = null;
        }

            // Handle sampul upload dengan error checking
        if ($request->hasFile('sampul')) {
                $sampul = $request->file('sampul');
                \Log::info('Uploading sampul: ' . $sampul->getClientOriginalName() . ' Size: ' . $sampul->getSize());
                
                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('panduan_sampul')) {
                    Storage::disk('public')->makeDirectory('panduan_sampul');
                }
                
                $sampulPath = $sampul->store('panduan_sampul', 'public');
                if ($sampulPath) {
            $panduan->sampul = $sampulPath;
                    \Log::info('Sampul uploaded successfully to: ' . $sampulPath);
                } else {
                    \Log::error('Failed to upload sampul');
                    return back()->withErrors(['sampul' => 'Gagal mengunggah sampul.']);
                }
        } else {
            $panduan->sampul = null;
        }

        $panduan->save();
            \Log::info('Panduan saved successfully with ID: ' . $panduan->id);

        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Error in PanduanController@store: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        $panduan = Panduan::findOrFail($id);

        return Inertia::render('Panduan/Edit', ['panduan' => $panduan]);
    }

    public function update(Request $request, string $id)
    {
        try {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB, samakan dengan store()
            'sampul' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // 4MB, samakan dengan store()
        ]);

        $panduan = Panduan::findOrFail($id);

        $panduan->judul = $validated['judul'];
        $panduan->deskripsi = $validated['deskripsi'];

            // Handle file upload
        if ($request->hasFile('file')) {
                $file = $request->file('file');
                \Log::info('Updating file for panduan ID ' . $id . ': ' . $file->getClientOriginalName());
                
            // Hapus file lama jika ada
                if ($panduan->file && Storage::disk('public')->exists($panduan->file)) {
                Storage::disk('public')->delete($panduan->file);
                    \Log::info('Deleted old file: ' . $panduan->file);
                }
                
                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('panduan_files')) {
                    Storage::disk('public')->makeDirectory('panduan_files');
                }
                
                $filePath = $file->store('panduan_files', 'public');
                if ($filePath) {
            $panduan->file = $filePath;
                    \Log::info('File updated successfully to: ' . $filePath);
                } else {
                    \Log::error('Failed to update file');
                    return back()->withErrors(['file' => 'Gagal mengunggah file.']);
                }
            }

            // Handle sampul upload
        if ($request->hasFile('sampul')) {
                $sampul = $request->file('sampul');
                \Log::info('Updating sampul for panduan ID ' . $id . ': ' . $sampul->getClientOriginalName());
                
            // Hapus sampul lama jika ada
                if ($panduan->sampul && Storage::disk('public')->exists($panduan->sampul)) {
                Storage::disk('public')->delete($panduan->sampul);
                    \Log::info('Deleted old sampul: ' . $panduan->sampul);
                }
                
                // Pastikan direktori ada
                if (!Storage::disk('public')->exists('panduan_sampul')) {
                    Storage::disk('public')->makeDirectory('panduan_sampul');
                }
                
                $sampulPath = $sampul->store('panduan_sampul', 'public');
                if ($sampulPath) {
                    $panduan->sampul = $sampulPath;
                    \Log::info('Sampul updated successfully to: ' . $sampulPath);
                } else {
                    \Log::error('Failed to update sampul');
                    return back()->withErrors(['sampul' => 'Gagal mengunggah sampul.']);
                }
        }

        $panduan->save();
            \Log::info('Panduan updated successfully with ID: ' . $panduan->id);

        return redirect()->route('panduan.index')->with('success', 'Panduan berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error in PanduanController@update: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
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
