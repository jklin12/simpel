<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\LoginController;

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
    $featuredServices = \App\Models\JenisSurat::whereIn('kode', ['SKTM', 'SKTMR', 'SKBM'])
        ->where('is_active', true)
        ->orderByRaw("FIELD(kode, 'SKTM', 'SKTMR', 'SKBM')")
        ->get();
    $kelurahans = \App\Models\Kelurahan::where('kecamatan_id', '6372010')->get();
    return view('home', compact('featuredServices', 'kelurahans'));
})->name('home');

Route::get('/layanan', [App\Http\Controllers\Web\ServiceController::class, 'index'])->name('services.index');
Route::get('/api/kelurahans/{kecamatanId}', [App\Http\Controllers\Web\ServiceController::class, 'getKelurahans'])->name('api.kelurahans');

// Public Permohonan Routes
Route::get('/pengajuan/create', [App\Http\Controllers\PublicPermohonanController::class, 'create'])->name('permohonan.create.public');
Route::post('/pengajuan', [App\Http\Controllers\PublicPermohonanController::class, 'store'])->name('permohonan.store.public');
Route::post('/pengajuan/ocr', [App\Http\Controllers\PublicPermohonanController::class, 'ocrKtp'])->name('permohonan.ocr');

// Tracking Routes
Route::get('/cek-status', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::get('/cek-status/search', [App\Http\Controllers\TrackingController::class, 'search'])->name('tracking.search');
Route::get('/cek-status/download/{track_token}', [App\Http\Controllers\TrackingController::class, 'downloadSignedLetter'])->name('tracking.download.signed');


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
        Route::resource('users', App\Http\Controllers\Admin\AccessControl\UserController::class);
        Route::resource('roles', App\Http\Controllers\Admin\AccessControl\RoleController::class);
        Route::resource('permissions', App\Http\Controllers\Admin\AccessControl\PermissionController::class);

        // Master Data
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('kabupaten', App\Http\Controllers\Admin\Master\KabupatenController::class);
            Route::resource('kecamatan', App\Http\Controllers\Admin\Master\KecamatanController::class);
            Route::resource('kelurahan', App\Http\Controllers\Admin\Master\KelurahanController::class);
        });

        // Letter Management
        Route::resource('jenis-surat', App\Http\Controllers\Admin\Surat\JenisSuratController::class);
        Route::resource('approval-flow', App\Http\Controllers\Admin\Surat\ApprovalFlowController::class);
        Route::get('surat-counter', [App\Http\Controllers\Admin\Surat\SuratCounterController::class, 'index'])->name('surat-counter.index');
        Route::patch('surat-counter/{suratCounter}/reset', [App\Http\Controllers\Admin\Surat\SuratCounterController::class, 'reset'])->name('surat-counter.reset');
    });

    // Permohonan Surat Management (All authenticated users)
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('permohonan-surat', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'index'])->name('permohonan-surat.index');
        Route::get('permohonan-surat/{permohonanSurat}', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'show'])->name('permohonan-surat.show');
        Route::get('permohonan-surat/{permohonanSurat}/edit', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'edit'])->name('permohonan-surat.edit');
        Route::put('permohonan-surat/{permohonanSurat}', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'update'])->name('permohonan-surat.update');
        Route::post('permohonan-surat/{permohonanSurat}/approve', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'approve'])->name('permohonan-surat.approve');
        Route::post('permohonan-surat/{permohonanSurat}/reject', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'reject'])->name('permohonan-surat.reject');
        Route::get('permohonan-surat/{permohonanSurat}/download', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'downloadLetter'])->name('permohonan-surat.download');
        Route::get('permohonan-surat/{permohonanSurat}/dokumen/{dokumen}/download', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'downloadDokumen'])->name('permohonan-surat.download-dokumen');
        Route::post('permohonan-surat/{permohonanSurat}/upload-signed', [App\Http\Controllers\Admin\Surat\PermohonanSuratController::class, 'uploadSignedLetter'])->name('permohonan-surat.upload-signed');
    });
});

// Location Helper Routes (API)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/location/kecamatan/{kabupatenId}', [App\Http\Controllers\Api\LocationHelperController::class, 'getKecamatan']);
    Route::get('/api/location/kelurahan/{kecamatanId}', [App\Http\Controllers\Api\LocationHelperController::class, 'getKelurahan']);
});

// =============================================================================
// Portal Kecamatan — Publik (tanpa login)
// =============================================================================
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [App\Http\Controllers\Web\PortalPublikController::class, 'index'])->name('home');
    Route::get('/berita', [App\Http\Controllers\Web\PortalPublikController::class, 'berita'])->name('berita');
    Route::get('/berita/{slug}', [App\Http\Controllers\Web\PortalPublikController::class, 'beritaDetail'])->name('berita.detail');
    Route::get('/peta', [App\Http\Controllers\Web\PortalPublikController::class, 'peta'])->name('peta');
    Route::get('/api/peta-data', [App\Http\Controllers\Web\PortalPublikController::class, 'petaData'])->name('peta.data');
    Route::get('/struktur-organisasi', [App\Http\Controllers\Web\PortalPublikController::class, 'strukturOrganisasi'])->name('struktur-organisasi');
    Route::get('/faq', [App\Http\Controllers\Web\PortalPublikController::class, 'faq'])->name('faq');

    // === PREVIEW ROUTES (hapus setelah memilih desain) ===
    Route::get('/preview-1', [App\Http\Controllers\Web\PortalPublikController::class, 'previewDesain1'])->name('preview1');
    Route::get('/preview-2', [App\Http\Controllers\Web\PortalPublikController::class, 'previewDesain2'])->name('preview2');
    Route::get('/preview-3', [App\Http\Controllers\Web\PortalPublikController::class, 'previewDesain3'])->name('preview3');
    Route::get('/preview-r', [App\Http\Controllers\Web\PortalPublikController::class, 'previewDesainR'])->name('previewR');
});


// =============================================================================
// Portal Kecamatan — Admin (harus login + role kecamatan atau super_admin)
// =============================================================================
Route::middleware(['auth', 'role:kecamatan|super_admin'])
    ->prefix('admin/portal')
    ->name('admin.portal.')
    ->group(function () {
        Route::resource('berita', App\Http\Controllers\Admin\Portal\BeritaController::class);
        Route::resource('data-kelurahan', App\Http\Controllers\Admin\Portal\DataKelurahanController::class);
        Route::resource('struktur-organisasi', App\Http\Controllers\Admin\Portal\StrukturOrganisasiController::class);
        Route::resource('slider', App\Http\Controllers\Admin\Portal\SliderController::class);
        Route::resource('faq', App\Http\Controllers\Admin\Portal\FaqController::class);
    });
