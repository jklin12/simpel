<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\TemplateSurat;
use App\Services\TemplateSuratService;
use Illuminate\Http\Request;

class TemplateSuratPublikController extends Controller
{
    protected $service;

    public function __construct(TemplateSuratService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman publik — template surat dikelompokkan per jenis surat.
     */
    public function index()
    {
        // Ambil semua jenis surat yang punya template aktif
        $jenisSurats = JenisSurat::whereHas('templateSurats', fn($q) => $q->where('is_active', true))
            ->with(['templateSurats' => fn($q) => $q->where('is_active', true)->latest()])
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('portal.template-surat.index', compact('jenisSurats'));
    }

    /**
     * Download file template surat (publik, tapi validasi is_active).
     */
    public function download(TemplateSurat $template)
    {
        abort_if(!$template->is_active, 404, 'Template tidak tersedia.');

        $storage = \Illuminate\Support\Facades\Storage::disk('public');

        if (!$storage->exists($template->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fileName    = $template->file_original_name ?? basename($template->file_path);
        $fullPath    = storage_path('app/public/' . $template->file_path);

        return response()->download($fullPath, $fileName);
    }
}
