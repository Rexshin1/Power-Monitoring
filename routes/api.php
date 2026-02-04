<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/measurements', [App\Http\Controllers\Api\SensorDataController::class, 'store']);
Route::get('/measurements/latest', [App\Http\Controllers\Api\SensorDataController::class, 'latest']);
Route::get('/measurements/history', [App\Http\Controllers\Api\SensorDataController::class, 'history']);
