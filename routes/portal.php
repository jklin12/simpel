<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PortalPublikController;
use App\Http\Controllers\Layanan\ServiceController;
use App\Http\Controllers\Layanan\PermohonanController;
use App\Http\Controllers\Layanan\TrackingController;
use App\Http\Controllers\Layanan\TemplateSuratPublikController;

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
            Route::post('/ajukan', [PermohonanController::class, 'store'])->middleware('throttle:20,1')->name('store');
            // OCR memanggil Anthropic API berbayar tanpa login → throttle ketat (cost-DoS).
            Route::post('/ocr', [PermohonanController::class, 'ocrKtp'])->middleware('throttle:10,1')->name('ocr');

            Route::get('/cek-status', [TrackingController::class, 'index'])->name('tracking');
            Route::get('/cek-status/search', [TrackingController::class, 'search'])->middleware('throttle:30,1')->name('tracking.search');
            Route::get('/cek-status/download/{track_token}', [TrackingController::class, 'downloadSignedLetter'])->name('tracking.download');

            // Revisi permohonan yang ditolak (publik via track_token)
            Route::get('/revisi/{track_token}', [PermohonanController::class, 'edit'])->name('revisi');
            Route::post('/revisi/{track_token}', [PermohonanController::class, 'update'])->middleware('throttle:20,1')->name('revisi.update');

            // Download Template Surat
            Route::get('/download-template', [TemplateSuratPublikController::class, 'index'])->name('template.index');
            Route::get('/download-template/{template}/download', [TemplateSuratPublikController::class, 'download'])->name('template.download');
        });

        // Contoh modul berikutnya:
        // Route::prefix('e-lapor')->name('elapor.')->group(function () { ... });
    });
});
