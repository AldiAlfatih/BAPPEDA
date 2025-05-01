<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KodeNomenklatur;
use App\Models\KodeNomenklaturDetail;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class KodeNomenklaturController extends Controller
{
    public function index()
    {
        $kodenomenklatur = KodeNomenklatur::with('detail')->where('jenis_kode', 0)->get();
        return Inertia::render('KodeNomenklatur/Index', [
            'kodenomenklatur' => $kodenomenklatur,
        ]);
    }

    public function getChildren(Request $request)
    {
        $type = $request->input('type');
        $parentId = $request->input('parent_id');

        $jenisKodeMap = [
            'bidang' => 1,
            'program' => 2,
            'kegiatan' => 3,
            'subkegiatan' => 4,
        ];

        $columnMap = [
            'bidang' => 'urusan',
            'program' => 'bidang_urusan',
            'kegiatan' => 'program',
            'subkegiatan' => 'kegiatan',
        ];

        if (!array_key_exists($type, $jenisKodeMap)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $children = KodeNomenklatur::where('jenis_kode', $jenisKodeMap[$type])
            ->whereHas('detail', function ($query) use ($columnMap, $type, $parentId) {
                $query->where($columnMap[$type], $parentId);
            })
            ->with('detail')
            ->get();

        return response()->json($children);
    }

    public function create()
    {
        return Inertia::render('kode_nomenklatur/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_kode' => 'required|string|max:255',
            'nomenklatur' => 'required|string|max:255',
            'jenis_kode' => 'required|integer|in:0,1,2,3,4',
            'urusan' => 'required|string|max:255',
            'bidang_urusan' => 'required|string|max:255',
            'program' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'subkegiatan' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $kodeNomenklatur = KodeNomenklatur::create([
                'nomor_kode' => $request->nomor_kode,
                'nomenklatur' => $request->nomenklatur,
                'jenis_kode' => $request->jenis_kode,
            ]);

            KodeNomenklaturDetail::create([
                'id_nomenklatur' => $kodeNomenklatur->id,
                'urusan' => $request->urusan,
                'bidang_urusan' => $request->bidang_urusan,
                'program' => $request->program,
                'kegiatan' => $request->kegiatan,
                'subkegiatan' => $request->subkegiatan,
            ]);

            DB::commit();
            return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $kode = KodeNomenklatur::with('detail')->findOrFail($id);

        return Inertia::render('KodeNomenklatur/Show', [
            'kodenomenklatur' => $kode,
        ]);
    }

    public function edit($id)
    {
        $kode = KodeNomenklatur::with('detail')->findOrFail($id);

        return Inertia::render('KodeNomenklatur/Edit', [
            'kodenomenklatur' => $kode,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_kode' => 'required|string|max:255',
            'nomenklatur' => 'required|string|max:255',
            'jenis_kode' => 'required|integer|in:0,1,2,3,4',
            'urusan' => 'required|string|max:255',
            'bidang_urusan' => 'required|string|max:255',
            'program' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'subkegiatan' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $kode = KodeNomenklatur::findOrFail($id);
            $kode->update([
                'nomor_kode' => $request->nomor_kode,
                'nomenklatur' => $request->nomenklatur,
                'jenis_kode' => $request->jenis_kode,
            ]);

            if ($kode->detail) {
                $kode->detail->update([
                    'urusan' => $request->urusan,
                    'bidang_urusan' => $request->bidang_urusan,
                    'program' => $request->program,
                    'kegiatan' => $request->kegiatan,
                    'subkegiatan' => $request->subkegiatan,
                ]);
            } else {
                $kode->detail()->create([
                    'urusan' => $request->urusan,
                    'bidang_urusan' => $request->bidang_urusan,
                    'program' => $request->program,
                    'kegiatan' => $request->kegiatan,
                    'subkegiatan' => $request->subkegiatan,
                ]);
            }

            DB::commit();
            return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $kode = KodeNomenklatur::findOrFail($id);

        DB::transaction(function () use ($kode) {
            $kode->detail()->delete();
            $kode->delete();
        });

        return redirect()->route('kodenomenklatur.index')->with('success', 'Kode Nomenklatur deleted successfully');
    }
}
