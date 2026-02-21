<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $permohonan = PermohonanSurat::with(['jenisSurat', 'approvals', 'kelurahan', 'dokumens'])
            ->where('track_token', $request->track_token)
            ->firstOrFail();

        return view('user.permohonan.track', compact('permohonan'));
    }

    /**
     * Download signed letter — publicly accessible via track_token for security.
     */
    public function downloadSignedLetter($track_token)
    {
        $permohonan = PermohonanSurat::where('track_token', $track_token)->firstOrFail();

        if ($permohonan->status !== 'completed' || !$permohonan->signed_file_path) {
            abort(404, 'File surat tidak tersedia.');
        }

        if (!Storage::disk('public')->exists($permohonan->signed_file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $filename = 'Surat-' . $permohonan->track_token . '.pdf';
        return Storage::disk('public')->download($permohonan->signed_file_path, $filename);
    }
}
