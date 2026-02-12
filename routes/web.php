<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/layanan', [App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/api/kelurahans/{kecamatanId}', [App\Http\Controllers\ServiceController::class, 'getKelurahans'])->name('api.kelurahans');

// Public Permohonan Routes
Route::get('/pengajuan/create', [App\Http\Controllers\PublicPermohonanController::class, 'create'])->name('permohonan.create.public');
Route::post('/pengajuan', [App\Http\Controllers\PublicPermohonanController::class, 'store'])->name('permohonan.store.public');
Route::post('/pengajuan/ocr', [App\Http\Controllers\PublicPermohonanController::class, 'ocrKtp'])->name('permohonan.ocr');

// Tracking Routes
Route::get('/cek-status', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::get('/cek-status/search', [App\Http\Controllers\TrackingController::class, 'search'])->name('tracking.search');


// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Dashboard Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Role specific dashboards
    Route::get('/admin/super', [DashboardController::class, 'superAdmin'])->name('dashboard.super_admin');
    Route::get('/admin/kabupaten', [DashboardController::class, 'kabupaten'])->name('dashboard.kabupaten');
    Route::get('/admin/kecamatan', [DashboardController::class, 'kecamatan'])->name('dashboard.kecamatan');
    Route::get('/admin/kelurahan', [DashboardController::class, 'kelurahan'])->name('dashboard.kelurahan');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notification Routes
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // Admin Only Routes
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', App\Http\Controllers\PermissionController::class);

        // Master Data
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('kabupaten', App\Http\Controllers\KabupatenController::class);
            Route::resource('kecamatan', App\Http\Controllers\KecamatanController::class);
            Route::resource('kelurahan', App\Http\Controllers\KelurahanController::class);
        });

        // Letter Management
        Route::resource('jenis-surat', App\Http\Controllers\JenisSuratController::class);
        Route::resource('approval-flow', App\Http\Controllers\ApprovalFlowController::class);
        Route::get('surat-counter', [App\Http\Controllers\SuratCounterController::class, 'index'])->name('surat-counter.index');
        Route::patch('surat-counter/{suratCounter}/reset', [App\Http\Controllers\SuratCounterController::class, 'reset'])->name('surat-counter.reset');
    });

    // Permohonan Surat Management (All authenticated users)
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('permohonan-surat', [App\Http\Controllers\PermohonanSuratController::class, 'index'])->name('permohonan-surat.index');
        Route::get('permohonan-surat/{permohonanSurat}', [App\Http\Controllers\PermohonanSuratController::class, 'show'])->name('permohonan-surat.show');
        Route::post('permohonan-surat/{permohonanSurat}/approve', [App\Http\Controllers\PermohonanSuratController::class, 'approve'])->name('permohonan-surat.approve');
        Route::post('permohonan-surat/{permohonanSurat}/reject', [App\Http\Controllers\PermohonanSuratController::class, 'reject'])->name('permohonan-surat.reject');
        Route::get('permohonan-surat/{permohonanSurat}/download', [App\Http\Controllers\PermohonanSuratController::class, 'downloadLetter'])->name('permohonan-surat.download');
    });
});

// Location Helper Routes (API)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/location/kecamatan/{kabupatenId}', [App\Http\Controllers\LocationHelperController::class, 'getKecamatan']);
    Route::get('/api/location/kelurahan/{kecamatanId}', [App\Http\Controllers\LocationHelperController::class, 'getKelurahan']);
});
