<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PowerSource;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PowerSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::all(); // For dropdowns if needed
        $powerSources = PowerSource::latest()->get();
        return view('pages.power_sources.index', compact('powerSources', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'type' => 'required|in:grid,battery',
            'nominal_voltage' => 'required|numeric',
        ]);

        try {
            PowerSource::create([
                'name' => $request->name,
                'area' => $request->area,
                'type' => $request->type,
                'nominal_voltage' => $request->nominal_voltage,
                'capacity' => $request->capacity ?? 0,
                'cost_per_kwh' => $request->cost_per_kwh ?? 0,
                'description' => $request->description,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'Power Source added successfully.');
        } catch (\Exception $e) {
            Log::error('Add Source Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add source.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $source = PowerSource::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'type' => 'required|in:grid,battery',
            'nominal_voltage' => 'required|numeric',
        ]);

        try {
            $source->update([
                'name' => $request->name,
                'area' => $request->area,
                'type' => $request->type,
                'nominal_voltage' => $request->nominal_voltage,
                'capacity' => $request->capacity,
                'cost_per_kwh' => $request->cost_per_kwh,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Power Source updated successfully.');
        } catch (\Exception $e) {
            Log::error('Update Source Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update source.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $source = PowerSource::findOrFail($id);
            $source->delete();
            return redirect()->back()->with('success', 'Data source berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data source.');
        }
    }
}
