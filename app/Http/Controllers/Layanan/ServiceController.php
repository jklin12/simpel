<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;

use App\Models\JenisSurat;
use App\Services\LayananPopupService;
use Illuminate\Http\Request;

use App\Models\Kecamatan;
use App\Models\Kelurahan;

class ServiceController extends Controller
{
    protected $layananPopupService;

    public function __construct(LayananPopupService $layananPopupService)
    {
        $this->layananPopupService = $layananPopupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all active letter types
        $services = JenisSurat::where('is_active', true)->get();
        // Fetch kelurahans across all active kecamatan in the kabupaten.
        // Kelurahan under a deactivated kecamatan (or deactivated themselves) are hidden.
        $kelurahans = Kelurahan::where('is_active', true)
            ->whereHas('kecamatan', fn($q) => $q->where('kabupaten_id', 6372)->where('is_active', true))
            ->with('kecamatan')
            ->get();

        $popup = $this->layananPopupService->getSingleton();

        return view('services.index', compact('services', 'kelurahans', 'popup'));
    }

    public function getKelurahans($kecamatanId)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatanId)->get(['id', 'nama']);
        return response()->json($kelurahans);
    }
}
