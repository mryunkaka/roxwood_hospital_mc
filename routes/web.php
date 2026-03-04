<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RekapFarmasiController;
use App\Http\Controllers\EmsServicesController;
use App\Http\Controllers\KonsumenController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\RegulasiController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\ManageUsersController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\RestaurantConsumptionController;
use App\Http\Controllers\RestaurantSettingsController;
use App\Http\Controllers\OperasiPlastikController;
use App\Http\Controllers\DutyMonitoringController;
use App\Http\Controllers\PresenceController;

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

use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return "Cache cleared";
});

use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "Database connected successfully!";
    } catch (\Exception $e) {
        return "Database not connected: " . $e->getMessage();
    }
});

// ============================================================
// AUTHENTICATION ROUTES (UI Only)
// ============================================================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API endpoints for login (no auth required)
Route::get('/api/users/search', [AuthController::class, 'searchUsers'])->name('api.users.search');
Route::get('/api/assets/logo-profile/{filename}', [AuthController::class, 'logoProfile'])
    ->where('filename', '.*')
    ->name('api.assets.logo_profile');
Route::get('/api/users/session/check', [AuthController::class, 'getActiveSession'])->name('api.users.session.check');
Route::get('/api/session/valid', [AuthController::class, 'checkSession'])->name('api.session.check');

// ============================================================
// TEMPORARY PDF PREVIEW ROUTES (DELETE AFTER TESTING)
// ============================================================

Route::match(['get', 'post'], '/preview-pdf/id', [AuthController::class, 'previewPdfIndonesian'])->name('preview.pdf.id');
Route::match(['get', 'post'], '/preview-pdf/en', [AuthController::class, 'previewPdfEnglish'])->name('preview.pdf.en');

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

    // Rekap Farmasi
    Route::get('/rekap-farmasi', [RekapFarmasiController::class, 'index'])->name('farmasi.rekap');
    Route::post('/rekap-farmasi', [RekapFarmasiController::class, 'store'])->name('farmasi.rekap.store');
    Route::delete('/rekap-farmasi/sales', [RekapFarmasiController::class, 'bulkDestroy'])->name('farmasi.rekap.sales.bulk_destroy');
    Route::delete('/rekap-farmasi/sales/{sale}', [RekapFarmasiController::class, 'destroy'])->name('farmasi.rekap.sales.destroy');
    Route::get('/api/farmasi/consumer/today', [RekapFarmasiController::class, 'checkConsumerToday'])->name('api.farmasi.consumer.today');
    Route::post('/api/farmasi/consumer/merge', [RekapFarmasiController::class, 'mergeSimilar'])->name('api.farmasi.consumer.merge');
    Route::get('/api/farmasi/consumers/search', [RekapFarmasiController::class, 'searchConsumers'])->name('api.farmasi.consumers.search');
    Route::get('/konsumen', [KonsumenController::class, 'index'])->name('farmasi.konsumen');
    Route::get('/api/identity/{identity}', [KonsumenController::class, 'identityJson'])->name('api.identity.show');
    Route::get('/gaji', [GajiController::class, 'index'])->name('farmasi.gaji');
    Route::post('/gaji/generate-manual', [GajiController::class, 'generateManual'])->name('farmasi.gaji.generate_manual');
    Route::post('/api/gaji/pay', [GajiController::class, 'pay'])->name('api.gaji.pay');

    // Layanan Medis (EMS)
    Route::get('/layanan-medis', [EmsServicesController::class, 'index'])->name('medis.ems');
    Route::post('/layanan-medis', [EmsServicesController::class, 'store'])->name('medis.ems.store');
    Route::delete('/layanan-medis/sales', [EmsServicesController::class, 'bulkDestroy'])->name('medis.ems.sales.bulk_destroy');
    Route::delete('/layanan-medis/sales/{sale}', [EmsServicesController::class, 'destroy'])->name('medis.ems.sales.destroy');
    Route::post('/api/medis/preview-price', [EmsServicesController::class, 'previewPrice'])->name('api.medis.preview_price');

    // Operasi Plastik (Medis)
    Route::get('/operasi-plastik', [OperasiPlastikController::class, 'index'])->name('medis.operasi_plastik.index');
    Route::post('/operasi-plastik', [OperasiPlastikController::class, 'store'])->name('medis.operasi_plastik.store');
    Route::post('/operasi-plastik/{operasi}/approve', [OperasiPlastikController::class, 'approve'])->name('medis.operasi_plastik.approve');
    Route::post('/operasi-plastik/{operasi}/reject', [OperasiPlastikController::class, 'reject'])->name('medis.operasi_plastik.reject');

    // Regulasi EMS (Non-staff)
    Route::get('/regulasi', [RegulasiController::class, 'medis'])->name('medis.regulasi');
    Route::get('/regulasi-farmasi', [RegulasiController::class, 'farmasi'])->name('farmasi.regulasi');
    Route::patch('/regulasi/packages/{package}', [RegulasiController::class, 'updatePackage'])->name('medis.regulasi.packages.update');
    Route::patch('/regulasi/medical-regulations/{medicalRegulation}', [RegulasiController::class, 'updateRegulation'])->name('medis.regulasi.regulations.update');

    // Validasi Akun (Non-staff)
    Route::get('/validasi', [ValidasiController::class, 'index'])->name('validasi.index');
    Route::patch('/validasi/users/{userRh}', [ValidasiController::class, 'update'])->name('validasi.users.update');

    // Manajemen User (Non-staff)
    Route::get('/manage-users', [ManageUsersController::class, 'index'])->name('users.manage');
    Route::post('/manage-users/users', [ManageUsersController::class, 'store'])->name('users.manage.users.store');
    Route::patch('/manage-users/users/{userRh}', [ManageUsersController::class, 'update'])->name('users.manage.users.update');
    Route::post('/manage-users/users/{userRh}/resign', [ManageUsersController::class, 'resign'])->name('users.manage.users.resign');
    Route::post('/manage-users/users/{userRh}/reactivate', [ManageUsersController::class, 'reactivate'])->name('users.manage.users.reactivate');
    Route::post('/manage-users/users/{userRh}/delete-kode-medis', [ManageUsersController::class, 'deleteKodeMedis'])->name('users.manage.users.delete_kode_medis');
    Route::delete('/manage-users/users/{userRh}', [ManageUsersController::class, 'destroy'])->name('users.manage.users.destroy');

    // Components
    Route::get('/components', [ComponentController::class, 'index'])->name('components');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account.update');
    Route::patch('/settings/web', [SettingsController::class, 'updateWeb'])->name('settings.web.update');

    // Reimbursement
    Route::get('/reimbursement', [ReimbursementController::class, 'index'])->name('reimbursement.index');
    Route::post('/reimbursement', [ReimbursementController::class, 'store'])->name('reimbursement.store');
    Route::post('/reimbursement/pay', [ReimbursementController::class, 'pay'])->name('reimbursement.pay');
    Route::post('/reimbursement/delete', [ReimbursementController::class, 'destroy'])->name('reimbursement.delete');
    Route::get('/reimbursement/receipt/{code}', [ReimbursementController::class, 'receipt'])->name('reimbursement.receipt');

    // Restaurant Consumption (UI session-backed)
    Route::get('/restaurant-consumption', [RestaurantConsumptionController::class, 'index'])->name('restaurant.consumption.index');
    Route::post('/restaurant-consumption', [RestaurantConsumptionController::class, 'store'])->name('restaurant.consumption.store');
    Route::post('/restaurant-consumption/{id}/approve', [RestaurantConsumptionController::class, 'approve'])->name('restaurant.consumption.approve');
    Route::post('/restaurant-consumption/{id}/paid', [RestaurantConsumptionController::class, 'paid'])->name('restaurant.consumption.paid');
    Route::post('/restaurant-consumption/{id}/delete', [RestaurantConsumptionController::class, 'destroy'])->name('restaurant.consumption.delete');

    // Restaurant Settings (page, session-backed)
    Route::get('/restaurant-settings', [RestaurantSettingsController::class, 'index'])->name('restaurant.settings.index');
    Route::post('/restaurant-settings/create', [RestaurantSettingsController::class, 'store'])->name('restaurant.settings.store');
    Route::post('/restaurant-settings/update', [RestaurantSettingsController::class, 'update'])->name('restaurant.settings.update');
    Route::post('/restaurant-settings/toggle', [RestaurantSettingsController::class, 'toggle'])->name('restaurant.settings.toggle');
    Route::post('/restaurant-settings/delete', [RestaurantSettingsController::class, 'destroy'])->name('restaurant.settings.delete');

    // Monitoring Jam Duty (berdasarkan transaksi/aktivitas simpan data)
    Route::get('/monitoring-jam-duty', [DutyMonitoringController::class, 'index'])->name('duty.monitor');

    // Presence (online/offline) for logout/close browser
    Route::post('/api/presence/ping', [PresenceController::class, 'ping'])->name('api.presence.ping');
    Route::post('/api/presence/offline', [PresenceController::class, 'offline'])->name('api.presence.offline');
});

// ============================================================
// FALLBACK - Redirect to login
// ============================================================

Route::fallback(function () {
    return redirect()->route('login');
});
