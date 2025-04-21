<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
<<<<<<< HEAD
=======
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
>>>>>>> bd447f1 (database pertama)

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
<<<<<<< HEAD
        return response()->jsonRequest([
=======
        return response()->json([
>>>>>>> bd447f1 (database pertama)
            'success' => true,
            'data' => Program::latest()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
<<<<<<< HEAD
            'kode' => 'required|string|max:5',
            'nama' => 'required|string',
=======
            'name' => 'required|string|max:255',
            'email' => 'required|string',
>>>>>>> bd447f1 (database pertama)
        ]);

        $program = Program::create($request->all());

<<<<<<< HEAD
        return response()->jsonRequest([
=======
        return response()->json([
>>>>>>> bd447f1 (database pertama)
            'success' => true,
            'message' => 'Program created successfully',
            'data' => $program
        ], 201);
    }
<<<<<<< HEAD
=======
    
>>>>>>> bd447f1 (database pertama)

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program = Program::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $program
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::findOrFail($id);
        $program->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'data' => $program
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully',
        ]);
    }
}
