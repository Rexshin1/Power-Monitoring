<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->get();
        // $powerSources = \App\Models\PowerSource::latest()->get(); // Removed as moved to PowerSourceController
        
        return view('master_data.index', compact('locations'));
    }

    public function store(Request $request)
    {
         // Logic Location Store (Existing)
         // ...
         // Note: Since user wants a separate store for device, I should create a separate method BUT route resource maps store() to one. 
         // I will create custom routes for devices or check request input?
         // Better: Create separate routes in web.php later.
         // For now, I'll stick to Location store here.
         // I will add storeDevice separately.
         
         // Re-implementing existing store logic briefly to respect replace_block
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:locations,code',
            'floor' => 'required|integer|min:1',
            'tariff_data' => 'required|string', 
        ]);

        DB::beginTransaction();
        try {
            $tariffParts = explode('|', $request->tariff_data);
            $category = $tariffParts[0] ?? 'Unknown';
            $power    = intval($tariffParts[1] ?? 0);
            $cost     = $tariffParts[2] ?? 0;

            Location::create([
                'name' => $request->name,
                'code' => $request->code,
                'floor' => $request->floor,
                'power_category' => $category,
                'installed_power' => $power,
                'tariff_per_kwh' => $cost,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Gedung berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal tambah gedung: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
    
    // ... (Update/Destroy Location methods remain unchanged, I'll skip replacing them if I can target correctly)
    // Actually, I need to ADD methods. I cannot easily ADD methods with replace_file_content unless I target the end of class.
    
    // Methods storeDevice, updateDevice, destroyDevice removed as per user request.

    /**
     * Update the specified location in database.
     */
    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:locations,code,' . $id,
            'floor' => 'required|integer|min:1',
            'tariff_data' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $tariffParts = explode('|', $request->tariff_data);
            
            $category = $tariffParts[0] ?? 'Unknown';
            $power    = intval($tariffParts[1] ?? 0);
            $cost     = $tariffParts[2] ?? 0;

            $location->update([
                'name' => $request->name,
                'code' => $request->code,
                'floor' => $request->floor,
                'power_category' => $category,
                'installed_power' => $power,
                'tariff_per_kwh' => $cost,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Gedung berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update data.');
        }
    }

    /**
     * Remove the specified location from database.
     */
    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}
