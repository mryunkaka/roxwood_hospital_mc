<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PatientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ============================================================
// AUTHENTICATION ROUTES (UI Only)
// ============================================================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================================
// LANGUAGE ROUTES
// ============================================================

Route::get('/lang/{code}/json', [LanguageController::class, 'getTranslations'])->name('lang.json');
Route::post('/lang/{code}', [LanguageController::class, 'switch'])->name('lang.switch');
Route::get('/lang/{code}', [LanguageController::class, 'switch'])->name('lang.switch.get');

// ============================================================
// AUTHENTICATED ROUTES (UI Only)
// ============================================================

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Components
    Route::get('/components', [ComponentController::class, 'index'])->name('components');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

});

// ============================================================
// FALLBACK - Redirect to login
// ============================================================

Route::fallback(function () {
    return redirect()->route('login');
});
