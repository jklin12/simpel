<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PortalPublikController;
use App\Http\Controllers\Layanan\ServiceController;
use App\Http\Controllers\Layanan\PermohonanController;
use App\Http\Controllers\Layanan\TrackingController;

// =============================================================================
// Portal Utama & Modul Layanan Publik (tanpa prefix)
// =============================================================================

Route::name('')->group(function () {
    // ── BERANDA PORTAL ──
    Route::get('/', [PortalPublikController::class, 'index'])->name('home');

    // ── KONTEN PORTAL ──
    Route::get('/berita', [PortalPublikController::class, 'berita'])->name('berita.index');
    Route::get('/berita/{slug}', [PortalPublikController::class, 'beritaDetail'])->name('berita.detail');
    Route::get('/peta', [PortalPublikController::class, 'peta'])->name('peta.index');
    Route::get('/api/peta-data', [PortalPublikController::class, 'petaData'])->name('peta.data');
    Route::get('/struktur-organisasi', [PortalPublikController::class, 'strukturOrganisasi'])->name('struktur-organisasi');
    Route::get('/faq', [PortalPublikController::class, 'faq'])->name('faq');

    // === PREVIEW ROUTES (hapus setelah memilih desain) ===
    Route::get('/preview-1', [PortalPublikController::class, 'previewDesain1'])->name('preview1');
    Route::get('/preview-2', [PortalPublikController::class, 'previewDesain2'])->name('preview2');
    Route::get('/preview-3', [PortalPublikController::class, 'previewDesain3'])->name('preview3');
    Route::get('/preview-r', [PortalPublikController::class, 'previewDesainR'])->name('previewR');

    // ── MODUL LAYANAN (EXTENSIBLE) ──
    Route::prefix('layanan')->name('layanan.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');

        // Modul Surat Menyurat
        Route::prefix('surat-menyurat')->name('surat.')->group(function () {
            Route::get('/ajukan', [PermohonanController::class, 'create'])->name('ajukan');
            Route::post('/ajukan', [PermohonanController::class, 'store'])->name('store');
            Route::post('/ocr', [PermohonanController::class, 'ocrKtp'])->name('ocr');

            Route::get('/cek-status', [TrackingController::class, 'index'])->name('tracking');
            Route::get('/cek-status/search', [TrackingController::class, 'search'])->name('tracking.search');
            Route::get('/cek-status/download/{track_token}', [TrackingController::class, 'downloadSignedLetter'])->name('tracking.download');
        });

        // Contoh modul berikutnya:
        // Route::prefix('e-lapor')->name('elapor.')->group(function () { ... });
    });
});
