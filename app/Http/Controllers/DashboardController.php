<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $latest = \App\Models\Measurement::latest()->first();

        // HEATMAP LOGIC: Daily Usage (kWh) for Current Month
        $startDate = \Carbon\Carbon::now()->startOfMonth();
        $endDate = \Carbon\Carbon::now()->endOfMonth();
        $heatmapData = [];

        // Loop through each day of the month
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            // Get average power for the day to represent intensity
            $avgPower = \App\Models\Measurement::whereBetween('created_at', [$dayStart, $dayEnd])->avg('power');
            
            // If no data, usage is 0. If data exists, use avg power.
            $usage = $avgPower ? round($avgPower, 0) : 0;

            $heatmapData[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('j'), // 1, 2, 3...
                'usage' => $usage,
                'weekday' => $date->dayOfWeek, // 0 (Sun) - 6 (Sat)
            ];
        }

        if (!$latest) {
            $latest = new \App\Models\Measurement([
                'voltage' => 0,
                'current' => 0,
                'power' => 0,
                'energy' => 0,
                'power_factor' => 0,
                'frequency' => 0,
            ]);
        }

        return view('dashboard', compact('latest', 'heatmapData'));
    }
}
