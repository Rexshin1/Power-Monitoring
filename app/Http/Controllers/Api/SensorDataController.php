<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'voltage' => 'required|numeric',
            'current' => 'required|numeric',
            'power' => 'required|numeric',
            'energy' => 'required|numeric',
            'power_factor' => 'required|numeric',
            'frequency' => 'required|numeric',
        ]);

        // --- SMART CONTROL LOGIC ---
        
        // 1. Schedule Check (Asia/Jakarta)
        $scheduleEnabled = \App\Models\Setting::where('key', 'schedule_enabled')->value('value');
        if ($scheduleEnabled) {
            // Get current time in Jakarta
            $now = now('Asia/Jakarta')->format('H:i');
            
            $onTime = \App\Models\Setting::where('key', 'schedule_time_on')->value('value');
            $offTime = \App\Models\Setting::where('key', 'schedule_time_off')->value('value');
            
            // Logic: Trigger ONLY if matches exact minute (assumed data comes frequently)
            if ($onTime && $now == $onTime) {
                $currentState = \App\Models\Setting::where('key', 'relay_state')->value('value');
                if ($currentState != 1) {
                    \App\Models\Setting::updateOrCreate(['key' => 'relay_state'], ['value' => 1]);
                    \Illuminate\Support\Facades\Log::info("SCHEDULER: Power Automatically Turned ON at $now");
                }
            }
            
            if ($offTime && $now == $offTime) {
                $currentState = \App\Models\Setting::where('key', 'relay_state')->value('value');
                if ($currentState != 0) {
                    \App\Models\Setting::updateOrCreate(['key' => 'relay_state'], ['value' => 0]);
                    \Illuminate\Support\Facades\Log::info("SCHEDULER: Power Automatically Turned OFF at $now");
                }
            }
        }

        // 2. Enforce Relay State (Simulate Cut-Off)
        // If Relay is OFF (0) in database, we simulate that the device physically cuts power.
        // So incoming reading should realistically be 0 for Current & Power.
        $relayState = \App\Models\Setting::where('key', 'relay_state')->value('value');
        if ($relayState !== null && $relayState == 0) {
            $validated['current'] = 0;
            $validated['power'] = 0;
            $validated['power_factor'] = 0;
            // Voltage usually remains if connected to grid, but load is disconnected.
        }
        // --- END LOGIC ---

        $measurement = \App\Models\Measurement::create($validated);

        // Check for Alarms
        $this->checkAlarms($measurement);

        return response()->json([
            'message' => 'Data stored successfully',
            'data' => $measurement
        ], 201);
    }

    private function checkAlarms($data)
    {
        // Get Thresholds from Settings (or defaults)
        $maxVoltage = \App\Models\Setting::where('key', 'alarm_max_voltage')->value('value') ?? 240;
        $minVoltage = \App\Models\Setting::where('key', 'alarm_min_voltage')->value('value') ?? 200;
        $maxCurrent = \App\Models\Setting::where('key', 'alarm_max_current')->value('value') ?? 15;
        $maxPower   = \App\Models\Setting::where('key', 'alarm_max_power')->value('value') ?? 3000;
        $minPF      = \App\Models\Setting::where('key', 'alarm_min_pf')->value('value') ?? 0.85;

        // Check Voltage
        if ($data->voltage > $maxVoltage) {
            $this->triggerAlarm('Tegangan Berlebih (Over Voltage)', "Tegangan terdeteksi terlalu tinggi: {$data->voltage}V (Batas: {$maxVoltage}V). Berpotensi merusak alat elektronik!");
        }
        if ($data->voltage < $minVoltage) {
            $this->triggerAlarm('Tegangan Drop (Under Voltage)', "Tegangan drop terlalu rendah: {$data->voltage}V (Batas: {$minVoltage}V). Periksa supply daya PLN.");
        }

        // Check Current
        if ($data->current > $maxCurrent) {
            $this->triggerAlarm('Arus Berlebih (Over Current)', "Arus beban terlalu besar: {$data->current}A (Batas: {$maxCurrent}A). Risiko kabel panas/terbakar!");
        }

        // Check Power (Overload)
        if ($data->power > $maxPower) {
            $this->triggerAlarm('Beban Berlebih (Overload)', "Daya beban kritis: {$data->power}W (Batas: {$maxPower}W). Segera kurangi pemakaian beban!");
            
            // AUTO CUT-OFF LOGIC
            $autoCutoff = \App\Models\Setting::where('key', 'auto_cutoff_enabled')->value('value');
            if ($autoCutoff) {
                \App\Models\Setting::updateOrCreate(['key' => 'relay_state'], ['value' => 0]);
                $this->triggerAlarm('PROTEKSI AKTIF', "Sistem otomatis memutus Relay karena Overload (> {$maxPower}W)!");
            }
        }

        // Check Power Factor
        if ($data->power_factor < $minPF) {
            $this->triggerAlarm('Power Factor Rendah', "Efisiensi daya buruk (PF): {$data->power_factor} (Batas: {$minPF}). Cek beban induktif.");
        }
    }

    private function triggerAlarm($type, $message)
    {
        // Simple Logic: Only create new alarm if no ACTIVE alarm of same type exists created in last 1 hour
        // (Prevents spamming DB with alarms every second)
        $existing = \App\Models\Alarm::where('type', $type)
            ->where('status', 'active')
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$existing) {
            \App\Models\Alarm::create([
                'type' => $type,
                'message' => $message,
                'status' => 'active'
            ]);
            
            \Illuminate\Support\Facades\Log::warning("ALARM TRIGGERED: $type - $message");

            // Check Email Notification Settings
            $emailEnabled = \App\Models\Setting::where('key', 'notify_email')->value('value');
            $targetEmail = \App\Models\Setting::where('key', 'target_email')->value('value');

            if ($emailEnabled && $targetEmail) {
                try {
                    $emailData = [
                        'type' => $type,
                        'message' => $message,
                        'time' => now()->toDateTimeString()
                    ];
                    \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\AlarmTriggered($emailData));
                    \Illuminate\Support\Facades\Log::info("Alarm Email sent to $targetEmail");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send alarm email: " . $e->getMessage());
                }
            }

            // Check WhatsApp Notification Settings
            $waEnabled = \App\Models\Setting::where('key', 'notify_whatsapp')->value('value');
            $targetWA = \App\Models\Setting::where('key', 'target_whatsapp')->value('value');
            
            // Fonnte Token not needed anymore for local gateway
            // $token = env('FONNTE_TOKEN'); 

            if ($waEnabled && $targetWA) {
                try {
                    // Send to Local WA Gateway with Timeout (5 seconds max)
                    $response = \Illuminate\Support\Facades\Http::timeout(5)->post('http://localhost:3000/send-message', [
                        'number' => $targetWA, // Gateway will handle formatting
                        'message' => "*PERINGATAN SISTEM LISTRIK!* ⚠️\n\n🔴 *Jenis:* $type\n📝 *Pesan:* $message\n⏰ *Waktu:* " . now()->format('d M Y H:i:s') . "\n\nMohon segera cek dashboard untuk tindakan lebih lanjut.",
                    ]);

                    \Illuminate\Support\Facades\Log::info("Alarm WA sent to $targetWA via Local Gateway: " . $response->body());
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send alarm WA via Gateway: " . $e->getMessage());
                }
            }

            // Check Telegram Notification Settings
            $tgEnabled = \App\Models\Setting::where('key', 'notify_telegram')->value('value');
            $botToken = \App\Models\Setting::where('key', 'telegram_bot_token')->value('value');
            $chatId = \App\Models\Setting::where('key', 'telegram_chat_id')->value('value');

            if ($tgEnabled && $botToken && $chatId) {
                try {
                    $tgMessage = "🚨 *PERINGATAN SISTEM LISTRIK!* 🚨\n\n🔴 *JENIS:* $type\n📝 *PESAN:* $message\n⏰ *WAKTU:* " . now()->format('d M Y H:i:s');
                    
                    \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $tgMessage,
                        'parse_mode' => 'Markdown'
                    ]);

                    \Illuminate\Support\Facades\Log::info("Alarm Telegram sent to $chatId");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to send alarm Telegram: " . $e->getMessage());
                }
            }
        }
    }
    public function history()
    {
        $history = \App\Models\Measurement::latest()->take(20)->get()->reverse()->values();
        
        // Determine status
        $lastUpdate = $history->last() ? $history->last()->created_at : null;
        $isOnline = $lastUpdate && $lastUpdate->diffInSeconds(now()) < 60;

        $statusText = 'Offline';
        if ($isOnline) {
            $relayState = \App\Models\Setting::where('key', 'relay_state')->value('value');
            if ($relayState !== null && $relayState == 0) {
                 $statusText = 'Relay OFF';
            } else {
                 $statusText = 'Online';
            }
        }

        // Heatmap Data: Group by DAY for Current Month (to match Dashboard Calendar)
        $start = now()->startOfMonth(); 
        $end = now()->endOfMonth();
        $daysInMonth = now()->daysInMonth;

        $monthMeasurements = \App\Models\Measurement::whereBetween('created_at', [$start, $end])
            ->selectRaw('DAY(created_at) as day, SUM(energy) as total_energy, MAX(energy) - MIN(energy) as usage_kwh') // Approximate usage per day
            ->groupBy('day')
            ->pluck('usage_kwh', 'day');

        $heatmap = [];
        // Map Days 1..31 to Index 0..30
        for ($i = 1; $i <= $daysInMonth; $i++) {
            // If we have data for day $i, use it. Else 0.
            // Note: usage_kwh logic here is simplified. Better to use the same logic as DashboardController if possible.
            // DashboardController uses max-min for each day.
            // The query above approximates it.
             
            // Refined Query Logic to match DashboardController loop efficiently:
            // Doing a loop query is slow. The selectRaw above is faster but approximation.
            // Let's stick to the selectRaw logic but refine if needed. 
            // Actually, if 'energy' column is cumulative kWh, MAX-MIN is correct for usage.
            
            $val = isset($monthMeasurements[$i]) ? round($monthMeasurements[$i], 2) : 0;
            $heatmap[] = $val; // Push to array (index 0 for Day 1)
        }

        return response()->json([
            'history' => $history,
            'status' => $statusText,
            'last_update' => $lastUpdate ? $lastUpdate->diffForHumans() : 'Never',
            'heatmap' => $heatmap
        ]);
    }
}
