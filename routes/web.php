<?php

use Illuminate\Support\Facades\Route;

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

// Location Helper Routes (API)
Route::middleware(['auth'])->group(function () {
    Route::get('/api/location/kecamatan/{kabupatenId}', [App\Http\Controllers\Api\LocationHelperController::class, 'getKecamatan']);
    Route::get('/api/location/kelurahan/{kecamatanId}', [App\Http\Controllers\Api\LocationHelperController::class, 'getKelurahan']);
});
