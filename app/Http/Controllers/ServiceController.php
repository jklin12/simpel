<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use Illuminate\Http\Request;

use App\Models\Kecamatan;
use App\Models\Kelurahan;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all active letter types
        $services = JenisSurat::where('is_active', true)->get();
        // Fetch kelurahans for static Kecamatan (Landasan Ulin - 6372010)
        $kelurahans = Kelurahan::where('kecamatan_id', '6372010')->get();

        return view('services.index', compact('services', 'kelurahans'));
    }

    public function getKelurahans($kecamatanId)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatanId)->get(['id', 'nama']);
        return response()->json($kelurahans);
    }
}
