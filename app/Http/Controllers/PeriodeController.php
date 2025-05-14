<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Models\PeriodeTahap;
use App\Models\PeriodeTahun;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PeriodeController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('searchQuery', '');
        $yearFilter = $request->input('yearFilter', null);

        $periode = Periode::with(['tahap', 'tahun'])
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->whereHas('tahap', function($q) use ($searchQuery) {
                    $q->where('tahap', 'like', '%' . $searchQuery . '%');
                });
            })
            ->when($yearFilter, function ($query) use ($yearFilter) {
                $query->where('tahun_id', $yearFilter);
            })
            ->get();

        $tahuns = PeriodeTahun::all();

        return Inertia::render('Periode', [
            'periode' => $periode,
            'tahuns' => $tahuns,
        ]);
    }

    public function show($id)
    {
        $periode = Periode::with(['tahun', 'tahap'])->findOrFail($id);

        return Inertia::render('Periode/Show', [
            'periode' => $periode
        ]);
    }
    public function storeTahun(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|unique:periode_tahun,tahun',
        ]);

        PeriodeTahun::create([
            'tahun' => $request->tahun
        ]);

        return redirect()->back()->with('status', 'Tahun berhasil ditambahkan');
    }

    public function create()
    {
        $tahuns = PeriodeTahun::all();
        return Inertia::render('periode/Create', [
            'tahuns' => $tahuns,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahap' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tahun_id' => 'required|exists:periode_tahun,id',
        ]);

        $tahap = PeriodeTahap::firstOrCreate([
            'tahap' => $request->tahap
        ]);

        $existing = Periode::where('tahap_id', $tahap->id)
            ->where('tahun_id', $request->tahun_id)
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors([
                'tahun_id' => 'Periode dengan tahap dan tahun ini sudah ada.',
            ])->withInput();
        }

        Periode::create([
            'tahap_id' => $tahap->id,
            'tahun_id' => $request->tahun_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 0,
        ]);

        return redirect()->route('periode.index')->with('status', 'Periode berhasil ditambahkan');
    }

    public function edit($id)
    {
        $periode = Periode::with(['tahap', 'tahun'])->findOrFail($id);
        $tahaps = PeriodeTahap::all();
        $tahuns = PeriodeTahun::all();

        return Inertia::render('periode/Edit', [
            'periode' => $periode,
            'tahaps' => $tahaps,
            'tahuns' => $tahuns,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tahap_id' => 'required|exists:periode_tahap,id',
            'tahun_id' => 'required|exists:periode_tahun,id',
        ]);

        $periode = Periode::findOrFail($id);
        $periode->update([
            'tahap_id' => $request->tahap_id,
            'tahun_id' => $request->tahun_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('periode.index')->with('status', 'Periode berhasil diperbarui');
    }

    public function updateStatus(Request $request, $id)
    {
        $periode = Periode::findOrFail($id);
        $periode->status = $request->status;
        $periode->save();

        return back()->with('message', 'Status diperbarui');
    }

    public function destroy($id)
    {
        $periode = Periode::findOrFail($id);
        $periode->delete();

        return redirect()->route('periode.index')->with('status', 'Periode berhasil dihapus');
    }

    public function generate(Request $request)
    {
        $tahunIni = now()->year;

        // Mendapatkan tahun yang sudah ada atau membuat tahun baru
        $periodeTahun = PeriodeTahun::firstOrCreate(
            ['tahun' => $tahunIni],
            ['status' => 0]
        );

        // Ambil semua tahap
        $tahaps = PeriodeTahap::all();
        $jumlahDitambahkan = 0;

        // Menyimpan tanggal selesai dari periode sebelumnya (gunakan DateTime)
        $lastEndDate = new \DateTime(); // Mulai dengan tanggal sekarang

        foreach ($tahaps as $tahap) {
            $sudahAda = Periode::where('tahap_id', $tahap->id)
                ->where('tahun_id', $periodeTahun->id)
                ->exists();

            if (!$sudahAda) {
                // Tentukan tanggal mulai dan tanggal selesai
                $tanggalMulai = $lastEndDate->modify('+1 day'); // Tanggal mulai adalah sehari setelah tanggal selesai sebelumnya
                $tanggalSelesai = clone $tanggalMulai; // Clone untuk menjaga referensi tanggal mulai
                $tanggalSelesai->add(new \DateInterval('P30D')); // Tanggal selesai adalah 7 hari setelah tanggal mulai

                // Buat periode baru
                Periode::create([
                    'tahap_id' => $tahap->id,
                    'tahun_id' => $periodeTahun->id,
                    'tanggal_mulai' => $tanggalMulai->format('Y-m-d H:i:s'),
                    'tanggal_selesai' => $tanggalSelesai->format('Y-m-d H:i:s'),
                    'status' => 0,
                ]);
                $jumlahDitambahkan++;

                // Simpan tanggal selesai untuk periode ini
                $lastEndDate = $tanggalSelesai;
            }
        }

        if ($jumlahDitambahkan === 0) {
            return redirect()->back()->with('info', 'Semua tahap sudah ada untuk tahun ini.');
        }

        return redirect()->back()->with('status', "{$jumlahDitambahkan} periode berhasil dibuat untuk tahun $tahunIni.");
    }


    public function lanjutkanKeTahunBerikutnya(Request $request)
    {
        // Ambil tahun terakhir yang ada
        $lastYear = PeriodeTahun::orderByDesc('tahun')->first();

        if (!$lastYear) {
            return response()->json(['message' => 'Tidak ada data tahun yang ada.'], 400);
        }

        // Tambahkan tahun baru (tahun + 1)
        $newYearValue = $lastYear->tahun + 1;
        $newTahun = PeriodeTahun::create(['tahun' => $newYearValue]);

        // Ambil semua tahapan
        $tahaps = PeriodeTahap::all();

        // Menyimpan tanggal selesai dari periode sebelumnya (gunakan DateTime)
        $lastEndDate = new \DateTime(); // Mulai dengan tanggal sekarang

        foreach ($tahaps as $tahap) {
            // Tentukan apakah periode untuk tahap ini sudah ada
            $sudahAda = Periode::where('tahap_id', $tahap->id)
                ->where('tahun_id', $newTahun->id)
                ->exists();

            if (!$sudahAda) {
                // Tentukan tanggal mulai dan tanggal selesai
                $tanggalMulai = $lastEndDate->modify('+1 day'); // Tanggal mulai adalah sehari setelah tanggal selesai periode sebelumnya
                $tanggalSelesai = clone $tanggalMulai; // Clone untuk menjaga referensi tanggal mulai
                $tanggalSelesai->add(new \DateInterval('P30D')); // Tanggal selesai adalah 7 hari setelah tanggal mulai

                // Buat periode baru untuk tahun baru
                Periode::create([
                    'tahap_id' => $tahap->id,
                    'tahun_id' => $newTahun->id,
                    'tanggal_mulai' => $tanggalMulai->format('Y-m-d H:i:s'),
                    'tanggal_selesai' => $tanggalSelesai->format('Y-m-d H:i:s'),
                    'status' => 0,
                ]);

                // Simpan tanggal selesai untuk periode ini
                $lastEndDate = $tanggalSelesai;
            }
        }
        return Inertia::location(route('periode.index'));

    }

    public function getPeriodeBelumSelesai()
    {
        try {
            $periode = Periode::with('tahap')
                ->whereNotIn('status', [0, 2])
                ->get();

            // Debug: Menambahkan log untuk memeriksa apakah data ada
            \Log::info('Data Periode:', ['periode' => $periode]);

            return response()->json($periode);
        } catch (\Exception $e) {
            // Log kesalahan jika terjadi error
            \Log::error('Gagal mengambil data periode: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data periode.'], 500);
        }
    }


}
