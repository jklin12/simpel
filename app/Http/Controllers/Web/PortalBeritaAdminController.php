<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortalBeritaRequest;
use App\Http\Requests\UpdatePortalBeritaRequest;
use App\Services\PortalBeritaService;
use Illuminate\Http\Request;

class PortalBeritaAdminController extends Controller
{
    protected $service;

    public function __construct(PortalBeritaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort_by', 'sort_order']);
        $beritas  = $this->service->getPaginated(10, $filters);

        return view('admin.portal.berita.index', compact('beritas', 'filters'));
    }

    public function create()
    {
        return view('admin.portal.berita.form');
    }

    public function store(StorePortalBeritaRequest $request)
    {
        try {
            $this->service->createBerita($request->validated());

            return redirect()
                ->route('admin.portal.berita.index')
                ->with('success', 'Berita berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $berita = $this->service->getById($id);

            return view('admin.portal.berita.form', compact('berita'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.portal.berita.index')
                ->with('error', 'Berita tidak ditemukan.');
        }
    }

    public function update(UpdatePortalBeritaRequest $request, $id)
    {
        try {
            $this->service->updateBerita($id, $request->validated());

            return redirect()
                ->route('admin.portal.berita.index')
                ->with('success', 'Berita berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->deleteBerita($id);

            return redirect()
                ->route('admin.portal.berita.index')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
