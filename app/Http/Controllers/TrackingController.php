<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermohonanSurat;

class TrackingController extends Controller
{
    public function index()
    {
        return view('user.permohonan.track');
    }

    public function search(Request $request)
    {
        $request->validate([
            'track_token' => 'required|string|exists:permohonan_surats,track_token',
        ], [
            'track_token.exists' => 'Kode tracking tidak ditemukan.',
        ]);

        $permohonan = PermohonanSurat::with(['jenisSurat', 'approvals', 'kelurahan'])
            ->where('track_token', $request->track_token)
            ->firstOrFail();

        return view('user.permohonan.track', compact('permohonan'));
    }
}
