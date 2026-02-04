<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PowerSourceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics');

Route::get('/history', [App\Http\Controllers\HistoryController::class, 'index'])->name('history');
Route::get('/history/export', [App\Http\Controllers\HistoryController::class, 'export'])->name('history.export');
Route::post('/history/settings', [App\Http\Controllers\HistoryController::class, 'updateSettings'])->name('history.settings');

Route::get('/alarms', function () {
    // Fetch REAL data from Alarm Model
    $alarms = \App\Models\Alarm::latest()->paginate(10);
    
    // Fetch REAL Settings from Database
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

    return view('alarms', ['alarms' => $alarms, 'settings' => $settings]);
})->name('alarms');

Route::post('/alarms/settings', function (\Illuminate\Http\Request $request) {
    // Keys to save (same as before)
    $keys = [
        'alarm_max_voltage', 'alarm_max_current', 'alarm_max_power', 'alarm_min_pf',
        'notify_email', 'target_email',
        'notify_whatsapp', 'target_whatsapp',
        'notify_telegram', 'telegram_bot_token', 'telegram_chat_id'
    ];

    foreach ($keys as $key) {
        // Handle checkboxes (boolean 1/0)
        if (strpos($key, 'notify_') === 0) {
            $value = $request->has($key) ? 1 : 0;
        } else {
            $value = $request->input($key);
        }
        
        \App\Models\Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    return redirect()->back()->with('success', 'Settings Saved Successfully!');
})->name('alarms.settings');

Route::post('/alarms/{id}/resolve', function($id) { 
    $alarm = \App\Models\Alarm::findOrFail($id);
    $alarm->update(['status' => 'resolved']);
    return redirect()->back()->with('success', 'Alarm marked as RESOLVED.'); 
})->name('alarms.resolve');

Route::post('/analytics/settings', [App\Http\Controllers\AnalyticsController::class, 'updateSettings'])->name('analytics.settings');

Route::post('/control/toggle', [App\Http\Controllers\ControlController::class, 'toggleRelay'])->name('control.toggle');
Route::post('/control/settings', [App\Http\Controllers\ControlController::class, 'updateSettings'])->name('control.settings');

Route::get('/control', [App\Http\Controllers\ControlController::class, 'index'])->name('control');

// Master Data Routes
Route::resource('master-data', MasterDataController::class);
// Device routes removed

// Power Sources Routes
Route::resource('power-sources', PowerSourceController::class);

Route::resource('users', UserController::class);

// Custom Auth Routes using AuthController
Route::get('login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('register', [App\Http\Controllers\AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);

// Auth::routes(); // Disabled standard routes


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
