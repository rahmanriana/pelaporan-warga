<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\LaporanController;
use App\Http\Controllers\Web\AdminController;

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

Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/laporans', [LaporanController::class, 'index'])->name('laporans.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/laporans/saya', [LaporanController::class, 'mine'])->name('laporans.mine');
    Route::get('/laporans/create', [LaporanController::class, 'create'])->name('laporans.create');
    Route::post('/laporans', [LaporanController::class, 'store'])->name('laporans.store');
    Route::get('/laporans/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporans.edit');
    Route::put('/laporans/{laporan}', [LaporanController::class, 'update'])->name('laporans.update');
    Route::delete('/laporans/{laporan}', [LaporanController::class, 'destroy'])->name('laporans.destroy');

    Route::middleware('role.admin.web')->group(function () {
        Route::get('/admin/panel', [AdminController::class, 'panel'])->name('admin.panel');
        Route::post('/admin/laporans/{laporan}/tanggapans', [AdminController::class, 'storeTanggapan'])->name('admin.tanggapan.store');
    });
});

Route::get('/laporans/{laporan}', [LaporanController::class, 'show'])
    ->whereNumber('laporan')
    ->name('laporans.show');
