<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SuratCounterService;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class SuratCounterController extends Controller
{
    protected $service;

    public function __construct(SuratCounterService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of surat counters.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'jenis_surat_id']);
        $suratCounters = $this->service->getSuratCountersPaginated(15, $filters);
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();

        return view('admin.surat-counter.index', compact('suratCounters', 'jenisSurats'));
    }

    /**
     * Reset counter for a specific record.
     */
    public function reset($id)
    {
        try {
            $this->service->resetCounter($id);

            return redirect()
                ->back()
                ->with('success', 'Counter berhasil direset ke 0');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
