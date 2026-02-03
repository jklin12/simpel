<?php

namespace App\Http\Controllers;

use App\Models\SuratCounter;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class SuratCounterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $suratCounters = SuratCounter::with(['jenisSurat', 'kelurahan.kecamatan'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('jenisSurat', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })
                    ->orWhereHas('kelurahan', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->when($request->jenis_surat_id, function ($query, $jenisSuratId) {
                $query->where('jenis_surat_id', $jenisSuratId);
            })
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(15)
            ->withQueryString();

        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();

        return view('admin.surat-counter.index', compact('suratCounters', 'jenisSurats'));
    }

    /**
     * Reset counter for a specific record.
     */
    public function reset(SuratCounter $suratCounter)
    {
        $suratCounter->update(['current_number' => 0]);

        return back()->with('success', 'Counter berhasil direset ke 0');
    }
}
