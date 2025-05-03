<?php

namespace App\Http\Controllers;

use App\Models\Bantuan;
use App\Models\BantuanFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BantuanController extends Controller
{
    public function index()
    {
        $bantuans = Bantuan::with('user', 'faqs')->latest()->get();

        return Inertia::render('Bantuan', [
            'bantuans' => $bantuans,
        ]);
    }

    public function create()
    {
        return Inertia::render('Bantuan/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'status' => 'required|integer|in:0,1,2',
        ]);

        $bantuan = Bantuan::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'status' => $request->status,
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/bantuan');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $filePath = 'uploads/bantuan/' . $fileName;
        }

        BantuanFaq::create([
            'user_id' => Auth::id(),
            'bantuan_id' => $bantuan->id,
            'deskripsi' => $request->deskripsi,
            'file' => $filePath,
        ]);

        return redirect()->route('bantuan.index')->with('success', 'Data bantuan berhasil ditambahkan.');
    }

    public function show(Bantuan $bantuan)
    {
        $bantuan->load('faqs.user');

        return Inertia::render('Bantuan/Show', [
            'bantuan' => $bantuan,
            'faqs' => $bantuan->faqs,
        ]);
    }

    public function chatForm(Bantuan $bantuan)
    {
        $bantuan->load(['faqs.user']); // penting! load user dari setiap faq    

        return Inertia::render('Bantuan/Chat', [
            'bantuan' => $bantuan,
            'faqs' => $bantuan->faqs,
        ]);
    }

    public function chatSend(Request $request, Bantuan $bantuan)
    {
        $request->validate([
            'balasan' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);
    
        $filePath = null;
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('public/images');
    
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
    
            $file->move($destinationPath, $fileName);
            $filePath = 'public/images/' . $fileName;
        }
    
        BantuanFaq::create([
            'bantuan_id' => $bantuan->id,
            'user_id' => Auth::id(),
            'balasan' => $request->balasan,
            'file' => $filePath,
        ]);
    
        return redirect()->back()->with('success', 'Pesan balasan berhasil dikirim.');
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
            'status' => 'required|integer|in:0,1,2',
        ]);

        $bantuan->update($request->only('judul', 'status'));

        return redirect()->route('bantuan.index')->with('success', 'Data bantuan berhasil diperbarui.');
    }

    public function destroy(Bantuan $bantuan)
    {
        $bantuan->delete();

        return redirect()->route('bantuan.index')->with('success', 'Data bantuan berhasil dihapus.');
    }
}
