<?php

namespace App\Http\Controllers\Admin\Surat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTemplateSuratRequest;
use App\Http\Requests\UpdateTemplateSuratRequest;
use App\Services\TemplateSuratService;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    protected $service;

    public function __construct(TemplateSuratService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of template surats.
     */
    public function index(Request $request)
    {
        $filters       = $request->only(['search', 'jenis_surat_id', 'is_active']);
        $templates     = $this->service->getPaginated(10, $filters);
        $jenisSurats   = $this->service->getAllJenisSurat();

        return view('admin.template-surat.index', compact('templates', 'jenisSurats', 'filters'));
    }

    /**
     * Show the form for creating a new template surat.
     */
    public function create()
    {
        $jenisSurats = $this->service->getAllJenisSurat();

        return view('admin.template-surat.create', compact('jenisSurats'));
    }

    /**
     * Store a newly created template surat.
     */
    public function store(StoreTemplateSuratRequest $request)
    {
        try {
            $data = $request->validated();
            $data['file'] = $request->file('file');
            $this->service->create($data);

            return redirect()
                ->route('admin.template-surat.index')
                ->with('success', 'Template Surat berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified template surat.
     */
    public function edit($id)
    {
        try {
            $template    = $this->service->getById($id);
            $jenisSurats = $this->service->getAllJenisSurat();

            return view('admin.template-surat.edit', compact('template', 'jenisSurats'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.template-surat.index')
                ->with('error', 'Template tidak ditemukan.');
        }
    }

    /**
     * Update the specified template surat.
     */
    public function update(UpdateTemplateSuratRequest $request, $id)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('file')) {
                $data['file'] = $request->file('file');
            }
            $this->service->update($id, $data);

            return redirect()
                ->route('admin.template-surat.index')
                ->with('success', 'Template Surat berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified template surat.
     */
    public function destroy($id)
    {
        try {
            $this->service->delete($id);

            return redirect()
                ->route('admin.template-surat.index')
                ->with('success', 'Template Surat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
