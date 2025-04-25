<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Bantuan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class BantuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Bantuan::latest()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'jenis_bantuan' => 'required|string|max:255',
            'penerima' => 'nullable|string',
            'tanggal_disalurkan' => 'nullable|string',
            // 'status_bantuan' => 'nullable|string',
        ]);

        $bantuan = Bantuan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Bantuan created successfully',
            'data' => $bantuan
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bantuan = Bantuan::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $bantuan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bantuan = Bantuan::findOrFail($id);
        $bantuan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Bantuan updated successfully',
            'data' => $bantuan
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bantuan = Bantuan::findOrFail($id);
        $bantuan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bantuan deleted successfully',
        ]);
    }
}
