<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Measurement;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // Prune old data
        $this->pruneOldData();

        // Get Retention setting
        $retention = Setting::where('key', 'retention_days')->value('value') ?? 30;

        // Handle Input
        $interval = $request->input('interval', '1s'); // 1s, 5s, 1m
        $limit = $request->input('limit', 20); // 10, 20, 50, 100

        $query = Measurement::query();

        if ($interval === '5s') {
            // Use time-based grouping logic
            $query->selectRaw('
                AVG(voltage) as voltage,
                AVG(current) as current,
                AVG(power) as power,
                MAX(energy) as energy,
                AVG(power_factor) as power_factor,
                AVG(frequency) as frequency,
                FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(created_at)/5)*5) as period_time
            ')
            ->groupBy('period_time')
            ->orderBy('period_time', 'desc');
        } elseif ($interval === '1m') {
            $query->selectRaw('
                AVG(voltage) as voltage,
                AVG(current) as current,
                AVG(power) as power,
                MAX(energy) as energy,
                AVG(power_factor) as power_factor,
                AVG(frequency) as frequency,
                FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(created_at)/60)*60) as period_time
            ')
            ->groupBy('period_time')
            ->orderBy('period_time', 'desc');
        } else {
            // Default 1s (Raw Data)
            $query->orderBy('created_at', 'desc');
        }

        $measurements = $query->paginate($limit)->appends(['interval' => $interval, 'limit' => $limit]);
        
        // Transform aggregated results to have 'created_at' attribute for view compatibility
        if ($interval !== '1s') {
            $measurements->getCollection()->transform(function ($item) {
                $item->created_at = \Carbon\Carbon::parse($item->period_time);
                return $item;
            });
        }

        return view('history', compact('measurements', 'retention', 'interval', 'limit'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'retention_days' => 'required|integer|min:1|max:3650',
        ]);

        Setting::updateOrCreate(
            ['key' => 'retention_days'],
            ['value' => $request->retention_days]
        );

        return redirect()->route('history')->with('success', 'Data retention settings updated successfully.');
    }

    public function export(Request $request)
    {
        $format = $request->query('format', 'csv');
        $fileName = 'measurements_history_' . date('Y-m-d_H-i-s') . '.' . ($format === 'excel' ? 'xls' : 'csv');
        
        // We use latest() for raw export usually, but maybe we should respect interval? 
        // User asked for "Export", usually implies raw data. I'll stick to raw for now.
        $measurements = Measurement::latest()->get();

        if ($format === 'excel') {
            // Styled HTML Table export for Excel
            $headers = [
                "Content-Type" => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=\"$fileName\"",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            return response()->stream(function() use ($measurements) {
                // EXCEL XML HEADER FOR STYLING
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
                echo '<head>';
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
                echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
                echo '
                <style>
                    body { font-family: Arial, sans-serif; }
                    .title { font-size: 20px; font-weight: bold; text-align: center; height: 50px; vertical-align: middle; border: none; }
                    .meta { font-size: 11px; color: #555; text-align: right; border: none; font-style: italic; }
                    table { border-collapse: collapse; width: 100%; }
                    th { 
                        background-color: #2c3e50; 
                        color: #ffffff; 
                        font-weight: bold; 
                        border: 1px solid #000000; 
                        padding: 10px; 
                        text-align: center;
                        height: 30px;
                    }
                    td { 
                        border: 1px solid #cccccc; 
                        padding: 5px; 
                        text-align: center;
                        vertical-align: middle;
                        font-size: 10pt;
                        white-space: nowrap; /* Prevent wrapping */
                    }
                    .odd { background-color: #f2f2f2; }
                    .num { mso-number-format:"0\.00"; } /* Force Excel Decimal Format */
                    .power { mso-number-format:"0"; font-weight: bold; color: #d35400; }
                    .energy { mso-number-format:"0\.000"; font-weight: bold; }
                </style>
                ';
                echo '</head><body>';

                echo '<table>';
                
                // Title Row
                echo '<tr><td colspan="7" class="title">POWER MONITORING REPORT</td></tr>';
                echo '<tr><td colspan="7" class="meta">Generated: ' . date('d F Y H:i:s') . ' | Total Records: ' . $measurements->count() . '</td></tr>';
                echo '<tr><td colspan="7" style="height: 10px; border:none;"></td></tr>'; // Spacer

                // Table Header
                echo '<thead><tr>
                        <th style="width: 210px;">Timestamp</th>
                        <th style="width: 100px;">Voltage (V)</th>
                        <th style="width: 100px;">Current (A)</th>
                        <th style="width: 100px;">Power (W)</th>
                        <th style="width: 120px;">Energy (kWh)</th>
                        <th style="width: 80px;">PF</th>
                        <th style="width: 100px;">Freq (Hz)</th>
                      </tr></thead>';
                
                echo '<tbody>';
                $i = 0;
                foreach ($measurements as $data) {
                    $bgClass = ($i++ % 2 == 0) ? 'even' : 'odd';
                    echo "<tr class='$bgClass'>";
                    echo '<td>' . $data->created_at . '</td>';
                    echo '<td class="num">' . str_replace('.', ',', number_format($data->voltage, 1)) . '</td>'; // Indo Excel uses comma? Let's stick to dot for mso-format
                    echo '<td class="num">' . number_format($data->current, 2) . '</td>';
                    echo '<td class="power">' . number_format($data->power, 0) . '</td>';
                    echo '<td class="energy">' . number_format($data->energy, 3) . '</td>';
                    echo '<td class="num">' . number_format($data->power_factor, 2) . '</td>';
                    echo '<td class="num">' . number_format($data->frequency, 1) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
                echo '</body></html>';
            }, 200, $headers);
        }

        // CSV Export
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Timestamp', 'Voltage (V)', 'Current (A)', 'Power (W)', 'Energy (kWh)', 'Power Factor', 'Frequency (Hz)');

        $callback = function() use($measurements, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Use SEMICOLON (;) as delimiter which is safer for Regional Settings (Indonesia/Europe)
            fputcsv($file, $columns, ';');

            foreach ($measurements as $data) {
                $row['Timestamp']  = $data->created_at;
                
                // For CSV, we format numbers with dots (standard) or comma?
                // Let's stick to raw number but maybe replace dot with comma specifically for CSV if we want Excel to treat it as number immediately?
                // Actually, standard CSV uses dot. Let's keep dot.
                $row['Voltage']    = $data->voltage;
                $row['Current']    = $data->current;
                $row['Power']      = $data->power;
                $row['Energy']     = $data->energy;
                $row['PF']         = $data->power_factor;
                $row['Frequency']  = $data->frequency;

                fputcsv($file, array(
                    $row['Timestamp'], 
                    $row['Voltage'], 
                    $row['Current'], 
                    $row['Power'], 
                    $row['Energy'], 
                    $row['PF'], 
                    $row['Frequency']
                ), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function pruneOldData()
    {
        try {
            $days = Setting::where('key', 'retention_days')->value('value') ?? 30;
            Measurement::where('created_at', '<', now()->subDays($days))->delete();
        } catch (\Exception $e) {
            // Log error or ignore if settings table doesn't exist yet (shouldn't happen)
        }
    }
}
