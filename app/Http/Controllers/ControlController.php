<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class ControlController extends Controller
{
    public function index()
    {
        // Get Relay State (1 = ON, 0 = OFF) - Default ON
        $relayState = Setting::where('key', 'relay_state')->value('value') ?? 1;

        // Get Auto Cutoff Settings
        $autoCutoff = Setting::where('key', 'auto_cutoff_enabled')->value('value') ?? 0;
        $maxPower = Setting::where('key', 'alarm_max_power')->value('value') ?? 3000;

        // Get Schedule Settings
        $scheduleEnabled = Setting::where('key', 'schedule_enabled')->value('value') ?? 0;
        $scheduleOn = Setting::where('key', 'schedule_time_on')->value('value');
        $scheduleOff = Setting::where('key', 'schedule_time_off')->value('value');

        return view('control', compact('relayState', 'autoCutoff', 'maxPower', 'scheduleEnabled', 'scheduleOn', 'scheduleOff'));
    }

    public function toggleRelay(Request $request)
    {
        $currentState = Setting::where('key', 'relay_state')->value('value') ?? 1;
        $newState = $currentState == 1 ? 0 : 1;
        
        Setting::updateOrCreate(['key' => 'relay_state'], ['value' => $newState]);

        $statusMsg = $newState == 1 ? 'Relay turned ON (Power Connected).' : 'Relay turned OFF (Power Cut).';
        return redirect()->back()->with($newState == 1 ? 'success' : 'warning', $statusMsg);
    }

    public function updateSettings(Request $request)
    {
        // Update Auto Cutoff
        if ($request->has('update_cutoff')) {
            Setting::updateOrCreate(['key' => 'auto_cutoff_enabled'], ['value' => $request->has('auto_cutoff_enabled') ? 1 : 0]);
            // Max Power logic is shared with Alarms settings, but we can update it here too if needed.
             if ($request->has('max_power_cutoff')) {
                  Setting::updateOrCreate(['key' => 'alarm_max_power'], ['value' => $request->max_power_cutoff]);
             }
        }

        // Update Schedule
        if ($request->has('update_schedule')) {
            Setting::updateOrCreate(['key' => 'schedule_enabled'], ['value' => $request->has('schedule_enabled') ? 1 : 0]);
            Setting::updateOrCreate(['key' => 'schedule_time_on'], ['value' => $request->schedule_time_on]);
            Setting::updateOrCreate(['key' => 'schedule_time_off'], ['value' => $request->schedule_time_off]);
        }

        return redirect()->back()->with('success', 'Control settings updated.');
    }
}
