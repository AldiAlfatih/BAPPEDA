<?php

namespace App\Http\Controllers;

use App\Models\SkpdTugas;
use App\Models\KodeNomenklatur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RencanaAwalController extends Controller
{
    public function show($id)
    {
        $tugas = SkpdTugas::with(['kodeNomenklatur', 'skpd',])->findOrFail($id);

        return Inertia::render('Monitoring/RencanaAwal', [
            'tugas' => $tugas,
        ]);
    }

    public function create()
    {
        $kodeNomenklatur = KodeNomenklatur::all();
        return Inertia::render('Monitoring/Create', [
            'kodeNomenklatur' => $kodeNomenklatur,
        ]);
    }

}
