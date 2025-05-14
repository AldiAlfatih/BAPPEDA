<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Nomenklatur;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class NomenklaturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Nomenklatur::latest()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_kode' => 'required|string|max:255',
            'nomenklatur' => 'nullable|string',
            'urusan' => 'nullable|string',
            'bidang_urusan' => 'nullable|string',
            'program' => 'nullable|string',
            'kegiatan' => 'nullable|string',
            'subkegiatan' => 'nullable|string',
        ]);

        $nomenklatur = Nomenklatur::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Nomenklatur created successfully',
            'data' => $nomenklatur
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nomenklatur = Nomenklatur::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $nomenklatur
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $nomenklatur = Nomenklatur::findOrFail($id);
        $nomenklatur->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Nomenklatur updated successfully',
            'data' => $nomenklatur
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $nomenklatur = Nomenklatur::findOrFail($id);
        $nomenklatur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nomenklatur deleted successfully',
        ]);
    }
}
