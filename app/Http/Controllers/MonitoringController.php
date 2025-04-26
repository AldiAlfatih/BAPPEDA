<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Monitoring;;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Monitoring');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Monitoring/Create');    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status_monitoring' => 'required|string|max:255',
            'catatan_monitoring' => 'nullable|text|max:255',
            'rekomendasi' => 'nullable|text|max:255',
        ]);

        Monitoring::create($request->all());

        return redirect()->route('monitoring.index')->with('success', 'Monitoring created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        return Inertia::render('Monitoring/Show', [
            'monitoring' => $monitoring,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        return Inertia::render('Monitoring/Edit', [
            'monitoring' => $monitoring,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status_monitoring' => 'required|string|max:255',
            'catatan_monitoring' => 'nullable|text|max:255',
            'rekomendasi' => 'nullable|text|max:255',
        ]);

        $monitoring = Monitoring::findOrFail($id);
        $monitoring->update($request->all());

        return redirect()->route('monitoring.index')->with('success', 'Monitoring updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $monitoring = Monitoring::findOrFail($id);
        $monitoring->delete();

        return redirect()->route('monitoring.index')->with('success', 'Monitoring deleted successfully');
    }
}
