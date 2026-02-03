<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\Kelurahan;

class LocationHelperController extends Controller
{
    /**
     * Get Kecamatan list by Kabupaten ID
     */
    public function getKecamatan($kabupatenId)
    {
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupatenId)->orderBy('nama')->get(['id', 'nama']);
        return response()->json($kecamatans);
    }

    /**
     * Get Kelurahan list by Kecamatan ID
     */
    public function getKelurahan($kecamatanId)
    {
        $kelurahans = Kelurahan::where('kecamatan_id', $kecamatanId)->orderBy('nama')->get(['id', 'nama']);
        return response()->json($kelurahans);
    }
}
