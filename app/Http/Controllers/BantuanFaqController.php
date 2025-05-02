<?php

namespace App\Http\Controllers;

use App\Models\BantuanFaq;
use App\Models\Bantuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BantuanFaqController extends Controller
{
    public function index()
    {
        $faqs = BantuanFaq::with(['user', 'bantuan'])->latest()->get();
        return Inertia::render('BantuanFaq', [
            'faqs' => $faqs,
        ]);
    }

    public function create()
    {
        return Inertia::render('BantuanFaq/Create', [
            'bantuans' => Bantuan::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bantuan_id' => 'required|exists:bantuan,id',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'tgl' => 'required|date',
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

        return redirect()->route('bantuanfaq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function edit(BantuanFaq $bantuanfaq)
    {
        return Inertia::render('BantuanFaq/Edit', [
            'faq' => $bantuanfaq,
            'bantuans' => Bantuan::all(),
        ]);
    }

    public function update(Request $request, BantuanFaq $bantuanfaq)
    {
        $request->validate([
            'bantuan_id' => 'required|exists:bantuan,id',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'tgl' => 'required|date',
        ]);

        $filePath = $bantuanfaq->file;
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

        $bantuanfaq->update([
            'bantuan_id' => $request->bantuan_id,
            'deskripsi' => $request->deskripsi,
            'file' => $filePath,
            'tgl' => $request->tgl,
        ]);

        return redirect()->route('bantuanfaq.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(BantuanFaq $bantuanfaq)
    {
        $bantuanfaq->delete();
        return redirect()->route('bantuanfaq.index')->with('success', 'FAQ berhasil dihapus.');
    }
}
