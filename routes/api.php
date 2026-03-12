<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\TanggapanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/laporans', [LaporanController::class, 'index']);
    Route::get('/laporans/{id}', [LaporanController::class, 'show']);
    Route::post('/laporans', [LaporanController::class, 'store']);
    Route::put('/laporans/{id}', [LaporanController::class, 'update']);
    Route::delete('/laporans/{id}', [LaporanController::class, 'destroy']);

    Route::middleware('role.admin')->group(function () {
        Route::patch('/laporans/{id}/status', [LaporanController::class, 'updateStatus']);
        Route::post('/laporans/{laporanId}/tanggapans', [TanggapanController::class, 'store']);
    });
});
