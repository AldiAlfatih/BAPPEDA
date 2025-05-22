<?php

namespace App\Http\Controllers;

use App\Models\KodeNomenklatur;
use App\Models\KodeNomenklaturDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KodeNomenklaturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('perangkat_daerah')) {
            return redirect()->route('kodenomenklatur.show', $user->id);
        }

        $kodeNomenklatur = KodeNomenklatur::all();
        
        return Inertia::render('KodeNomenklatur', [
            'kodeNomenklatur' => $kodeNomenklatur,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan');
            }]) 
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null
                ];
            });
        
        $bidangUrusanList = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null
                ];
            });
        
        $programList = KodeNomenklatur::where('jenis_nomenklatur', 2)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan', 'id_program');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null
                ];
            });
        
        $kegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 3)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan', 'id_program', 'id_kegiatan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null,
                    'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null
                ];
            });
        
        $subkegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 4)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan', 'id_program', 'id_kegiatan', 'id_sub_kegiatan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null,
                    'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null,
                    'subkegiatan_id' => $item->details->first() ? $item->details->first()->id_sub_kegiatan : null
                ];
            });
        
        return Inertia::render('KodeNomenklatur/Create', [
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi request
        $validated = $request->validate([
            'jenis_nomenklatur' => 'required|integer|min:0|max:4',
            'nomor_kode' => 'required|string',
            'nomenklatur' => 'required|string',
            'urusan' => 'nullable|integer|exists:kode_nomenklatur,id',
            'bidang_urusan' => 'nullable|integer|exists:kode_nomenklatur,id',
            'program' => 'nullable|integer|exists:kode_nomenklatur,id',
            'kegiatan' => 'nullable|integer|exists:kode_nomenklatur,id',
            'subkegiatan' => 'nullable|integer|exists:kode_nomenklatur,id',
        ]);

        // Buat kode nomenklatur baru
        $kodeNomenklatur = KodeNomenklatur::create([
            'jenis_nomenklatur' => $validated['jenis_nomenklatur'],
            'nomor_kode' => $validated['nomor_kode'],
            'nomenklatur' => $validated['nomenklatur'],
        ]);

        // Siapkan data detail dasar
        $detailData = [
            'id_nomenklatur' => $kodeNomenklatur->id,
        ];

        // Isi data detail sesuai dengan jenis nomenklatur
        switch ($validated['jenis_nomenklatur']) {
            case 0: // Urusan
                // Untuk urusan, id_urusan merujuk ke dirinya sendiri
                $detailData['id_urusan'] = $kodeNomenklatur->id;
                break;
            
            case 1: // Bidang Urusan
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $kodeNomenklatur->id;
                break;
            
            case 2: // Program
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $validated['bidang_urusan'];
                $detailData['id_program'] = $kodeNomenklatur->id;
                break;
            
            case 3: // Kegiatan
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $validated['bidang_urusan'];
                $detailData['id_program'] = $validated['program'];
                $detailData['id_kegiatan'] = $kodeNomenklatur->id;
                break;
            
            case 4: // Sub Kegiatan
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $validated['bidang_urusan'];
                $detailData['id_program'] = $validated['program'];
                $detailData['id_kegiatan'] = $validated['kegiatan'];
                $detailData['id_sub_kegiatan'] = $kodeNomenklatur->id;
                break;
        }
        KodeNomenklaturDetail::create($detailData);

        return redirect()->route('kodenomenklatur.index')->with('message', 'Kode Nomenklatur berhasil ditambahkan!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        
        return Inertia::render('KodeNomenklatur/Show', [
            'kodeNomenklatur' => $kodeNomenklatur,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Get the main record
        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        
        // Get associated detail record
        $detail = KodeNomenklaturDetail::where('id_nomenklatur', $id)->first();
        
        // Get data for dropdowns
        $urusanList = KodeNomenklatur::where('jenis_nomenklatur', 0)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->get();
        
        $bidangUrusanList = KodeNomenklatur::where('jenis_nomenklatur', 1)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null
                ];
            });
        
        $programList = KodeNomenklatur::where('jenis_nomenklatur', 2)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null
                ];
            });
        
        $kegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 3)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan', 'id_program');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null
                ];
            });
        
        $subkegiatanList = KodeNomenklatur::where('jenis_nomenklatur', 4)
            ->select('id', 'nomor_kode', 'nomenklatur')
            ->with(['details' => function($query) {
                $query->select('id', 'id_nomenklatur', 'id_urusan', 'id_bidang_urusan', 'id_program', 'id_kegiatan');
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'nomor_kode' => $item->nomor_kode,
                    'nomenklatur' => $item->nomenklatur,
                    'urusan_id' => $item->details->first() ? $item->details->first()->id_urusan : null,
                    'bidang_urusan_id' => $item->details->first() ? $item->details->first()->id_bidang_urusan : null,
                    'program_id' => $item->details->first() ? $item->details->first()->id_program : null,
                    'kegiatan_id' => $item->details->first() ? $item->details->first()->id_kegiatan : null
                ];
            });
        
        return Inertia::render('KodeNomenklatur/Edit', [
            'kodeNomenklatur' => $kodeNomenklatur,
            'detail' => $detail,
            'urusanList' => $urusanList,
            'bidangUrusanList' => $bidangUrusanList,
            'programList' => $programList,
            'kegiatanList' => $kegiatanList,
            'subkegiatanList' => $subkegiatanList
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request
        $validated = $request->validate([
            'jenis_nomenklatur' => 'required|integer|min:0|max:4',
            'nomor_kode' => 'required|string',
            'nomenklatur' => 'required|string',
            'urusan' => 'nullable|integer|exists:kode_nomenklatur,id',
            'bidang_urusan' => 'nullable|integer|exists:kode_nomenklatur,id',
            'program' => 'nullable|integer|exists:kode_nomenklatur,id',
            'kegiatan' => 'nullable|integer|exists:kode_nomenklatur,id',
            'subkegiatan' => 'nullable|integer|exists:kode_nomenklatur,id',
        ]);

        // Update kode nomenklatur
        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        $kodeNomenklatur->update([
            'jenis_nomenklatur' => $validated['jenis_nomenklatur'],
            'nomor_kode' => $validated['nomor_kode'],
            'nomenklatur' => $validated['nomenklatur'],
        ]);

        // Prepare detail data based on the jenis_nomenklatur
        $detailData = [];
        
        switch ($validated['jenis_nomenklatur']) {
            case 0: // Urusan
                // For Urusan, id_urusan references itself
                $detailData['id_urusan'] = $kodeNomenklatur->id;
                break;
                
            case 1: // Bidang Urusan
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $kodeNomenklatur->id;
                break;
                
            case 2: // Program
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $validated['bidang_urusan'];
                $detailData['id_program'] = $kodeNomenklatur->id;
                break;
                
            case 3: // Kegiatan
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $validated['bidang_urusan'];
                $detailData['id_program'] = $validated['program'];
                $detailData['id_kegiatan'] = $kodeNomenklatur->id;
                break;
                
            case 4: // Sub Kegiatan
                $detailData['id_urusan'] = $validated['urusan'];
                $detailData['id_bidang_urusan'] = $validated['bidang_urusan'];
                $detailData['id_program'] = $validated['program'];
                $detailData['id_kegiatan'] = $validated['kegiatan'];
                $detailData['id_sub_kegiatan'] = $kodeNomenklatur->id;
                break;
        }
        
        // Update or create the detail record
        if (!empty($detailData)) {
            KodeNomenklaturDetail::updateOrCreate(
                ['id_nomenklatur' => $id],
                $detailData
            );
        }

        return redirect()->route('kodenomenklatur.index')
            ->with('message', 'Kode Nomenklatur berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kodeNomenklatur = KodeNomenklatur::findOrFail($id);
        $kodeNomenklatur->delete();
        
        return redirect()->route('kodenomenklatur.index')
            ->with('message', 'Kode Nomenklatur berhasil dihapus!');
    }
}

