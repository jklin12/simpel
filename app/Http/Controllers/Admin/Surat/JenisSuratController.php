<?php

namespace App\Http\Controllers\Admin\Surat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJenisSuratRequest;
use App\Http\Requests\UpdateJenisSuratRequest;
use App\Services\JenisSuratService;
use Illuminate\Http\Request;

class JenisSuratController extends Controller
{
    protected $service;

    public function __construct(JenisSuratService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of jenis surat.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'is_active']);
        $jenisSurats = $this->service->getJenisSuratPaginated(10, $filters);

        return view('admin.jenis-surat.index', compact('jenisSurats'));
    }

    /**
     * Show the form for creating a new jenis surat.
     */
    public function create()
    {
        return view('admin.jenis-surat.create');
    }

    /**
     * Store a newly created jenis surat.
     */
    public function store(StoreJenisSuratRequest $request)
    {
        try {
            $data = $request->validated();
            // Pass uploaded contoh files (keyed by field name) to service
            $data['_attachment_guide_files'] = $request->file('attachment_guide_files', []);
            $this->service->createJenisSurat($data);

            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('success', 'Jenis Surat berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified jenis surat.
     */
    public function edit($id)
    {
        try {
            $jenisSurat = $this->service->getJenisSuratById($id);

            return view('admin.jenis-surat.edit', compact('jenisSurat'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('error', 'Jenis Surat tidak ditemukan');
        }
    }

    /**
     * Update the specified jenis surat.
     */
    public function update(UpdateJenisSuratRequest $request, $id)
    {
        try {
            $data = $request->validated();
            // Pass uploaded contoh files (keyed by field name) to service
            $data['_attachment_guide_files'] = $request->file('attachment_guide_files', []);
            $this->service->updateJenisSurat($id, $data);

            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('success', 'Jenis Surat berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified jenis surat.
     */
    public function destroy($id)
    {
        try {
            $this->service->deleteJenisSurat($id);

            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('success', 'Jenis Surat berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
