<?php

namespace App\Http\Controllers;

use App\Models\Bantuan;
use App\Models\BantuanFaq;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class BantuanController extends Controller
{
    public function index()
    {
        $bantuans = Bantuan::with('user')->latest()->get();
        return Inertia::render('Bantuan', [
            'bantuans' => $bantuans,
        ]);
    }

    public function create()
    {
        return Inertia::render('Bantuan/Create');
    }

    public function show(Bantuan $bantuan)
    {
        $faqs = $bantuan->faqs();
        // return Inertia::render('bantuan', 
        dd($faqs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);


        
        $data = Bantuan::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'tgl_dibuat' => $request->tgl_dibuat,
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $filePath = 'images/' . $fileName;
        }

        BantuanFaq::create([
            'user_id' => Auth::id(),
            'bantuan_id' => $request->bantuan_id,
            'deskripsi' => $request->deskripsi,
            'file' => $filePath,
            'tgl' => $request->tgl,
        ]);

        return redirect()->route('bantuan.index')->with('success', 'Data bantuan berhasil ditambahkan.');
    }

    public function edit(Bantuan $bantuan)
    {
        return Inertia::render('Bantuan/Edit', [
            'bantuan' => $bantuan,
        ]);
    }

    public function update(Request $request, Bantuan $bantuan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
        ]);

        $bantuan->update($request->only('judul', 'tgl_dibuat'));

        return redirect()->route('bantuan.index')->with('success', 'Data bantuan berhasil diperbarui.');
    }

    public function destroy(Bantuan $bantuan)
    {
        $bantuan->delete();
        return redirect()->route('bantuan.index')->with('success', 'Data bantuan berhasil dihapus.');
    }
}
