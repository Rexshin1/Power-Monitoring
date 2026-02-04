<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alarm;
use App\Models\Setting;

class AlarmController extends Controller
{
    public function index()
    {
        $alarms = Alarm::latest()->paginate(10);
        $settings = Setting::all()->pluck('value', 'key');
        
        return view('alarms', compact('alarms', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        // Save Thresholds
        $keys = ['alarm_max_voltage', 'alarm_min_voltage', 'alarm_max_current', 'alarm_max_power', 'alarm_min_pf'];
        foreach ($keys as $key) {
           if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
           }
        }

        // Save Notifications
        if ($request->has('target_email')) {
             Setting::updateOrCreate(['key' => 'target_email'], ['value' => $request->input('target_email')]);
        }
        if ($request->has('target_whatsapp')) {
             Setting::updateOrCreate(['key' => 'target_whatsapp'], ['value' => $request->input('target_whatsapp')]);
        }
        
        // Save Telegram Settings
        if ($request->has('telegram_bot_token')) {
             Setting::updateOrCreate(['key' => 'telegram_bot_token'], ['value' => $request->input('telegram_bot_token')]);
        }
        if ($request->has('telegram_chat_id')) {
             Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['value' => $request->input('telegram_chat_id')]);
        }

        Setting::updateOrCreate(['key' => 'notify_email'], ['value' => $request->has('notify_email') ? 1 : 0]);
        Setting::updateOrCreate(['key' => 'notify_whatsapp'], ['value' => $request->has('notify_whatsapp') ? 1 : 0]);
        Setting::updateOrCreate(['key' => 'notify_telegram'], ['value' => $request->has('notify_telegram') ? 1 : 0]);

        return redirect()->back()->with('success', 'Alarm settings updated successfully.');
    }

    public function resolve($id)
    {
        $alarm = Alarm::findOrFail($id);
        $alarm->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Alarm marked as resolved.');
    }

    public function testNotification(Request $request)
    {
        $type = $request->input('type', 'telegram');
        
        if ($type === 'telegram') {
            $botToken = Setting::where('key', 'telegram_bot_token')->value('value');
            $chatId = Setting::where('key', 'telegram_chat_id')->value('value');

            if (!$botToken || !$chatId) {
                return redirect()->back()->with('error', 'Bot Token or Chat ID is missing.');
            }

            try {
                $message = "🔔 *TEST NOTIFIKASI* 🔔\n\nHalo! Ini adalah pesan tes dari dashboard Monitoring Listrik.\nJika kamu menerima ini, berarti setelan bot sudah BENAR! ✅";
                
                $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'Markdown'
                ]);

                if ($response->successful()) {
                    return redirect()->back()->with('success', 'Test message sent to Telegram successfully!');
                } else {
                    return redirect()->back()->with('error', 'Failed to send Telegram message. Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('info', 'Test initiated.');
    }
}
