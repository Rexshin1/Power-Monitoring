<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Measurement;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Default Tariff: Rp 1.444,70 (PLN R1 Non-Subsidi)
        $tariff = Setting::where('key', 'electricity_tariff')->value('value') ?? 1444.70;

        // 1. Daily Consumption (Last 7 Days)
        $dailyLabels = [];
        $dailyData = [];
        $dailyCost = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = Carbon::now()->subDays($i)->format('D, d M');

            // Find min and max energy for this day
            $stats = Measurement::whereDate('created_at', $date)
                ->selectRaw('MIN(energy) as min_e, MAX(energy) as max_e')
                ->first();

            if ($stats && $stats->max_e > 0) {
                // If min_e is the same as max_e, maybe it didn't change, or only one record.
                // Improvement: Get Max energy of yesterday to compare start.
                // For simplicity: Max - Min of the day.
                $usage = $stats->max_e - $stats->min_e;
                
                // Correction: If yesterday's last data is available, use that as start?
                // Taking Max - Min is safer for 'usage within this day window'.
                
                $dailyData[] = round($usage, 3);
                $dailyCost[] = round($usage * $tariff, 2);
            } else {
                $dailyData[] = 0;
                $dailyCost[] = 0;
            }
        }

        // 2. Monthly Projection
        // usage so far this month
        $startOfMonth = Carbon::now()->startOfMonth();
        $monthlyStats = Measurement::where('created_at', '>=', $startOfMonth)
            ->selectRaw('MIN(energy) as min_e, MAX(energy) as max_e')
            ->first();
        
        $usageMonth = ($monthlyStats && $monthlyStats->max_e) ? ($monthlyStats->max_e - $monthlyStats->min_e) : 0;
        $costMonthSoFar = $usageMonth * $tariff;

        // Estimate end of month: (Current Usage / Days Passed) * Total Days in Month
        $daysPassed = Carbon::now()->day; // e.g. 25
        $daysInMonth = Carbon::now()->daysInMonth; // e.g. 31
        
        $estimatedMonthlyCost = ($daysPassed > 0) 
            ? ($costMonthSoFar / $daysPassed) * $daysInMonth 
            : 0;

        // 3. Today's Realtime Stats
        $todayCost = end($dailyCost);
        $todayKwh = end($dailyData);

        // 4. Peak Demand (Highest Power W recorded this month)
        $peakDemand = Measurement::where('created_at', '>=', $startOfMonth)->max('power') ?? 0;

        // 5. Monthly Comparison (This Month vs Last Month)
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $lastMonthStats = Measurement::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw('MIN(energy) as min_e, MAX(energy) as max_e')
            ->first();

        $usageLastMonth = ($lastMonthStats && $lastMonthStats->max_e) ? ($lastMonthStats->max_e - $lastMonthStats->min_e) : 0;
        
        // Avoid division by zero
        if ($usageLastMonth > 0) {
            $comparisonPercent = (($usageMonth - $usageLastMonth) / $usageLastMonth) * 100;
        } else {
            $comparisonPercent = $usageMonth > 0 ? 100 : 0;
        }

        return view('analytics', compact('tariff', 'dailyLabels', 'dailyData', 'dailyCost', 'todayCost', 'todayKwh', 'costMonthSoFar', 'estimatedMonthlyCost', 'peakDemand', 'usageMonth', 'usageLastMonth', 'comparisonPercent'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'electricity_tariff' => 'required|numeric|min:0'
        ]);

        Setting::updateOrCreate(
            ['key' => 'electricity_tariff'],
            ['value' => $request->electricity_tariff]
        );

        return redirect()->back()->with('success', 'Tariff updated successfully.');
    }
}
