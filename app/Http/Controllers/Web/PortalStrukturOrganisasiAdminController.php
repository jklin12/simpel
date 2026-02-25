<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortalStrukturOrganisasiRequest;
use App\Http\Requests\UpdatePortalStrukturOrganisasiRequest;
use App\Services\PortalStrukturOrganisasiService;
use Illuminate\Http\Request;

class PortalStrukturOrganisasiAdminController extends Controller
{
    protected $service;

    public function __construct(PortalStrukturOrganisasiService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'parent_id', 'sort_by', 'sort_order']);
        $data    = $this->service->getPaginated(15, $filters);
        $parents = $this->service->getAll(); // Untuk filter by Atasan 

        return view('admin.portal.struktur-organisasi.index', compact('data', 'filters', 'parents'));
    }

    public function create()
    {
        // Ambil semua data untuk dropdown "Atasan"
        $parents = $this->service->getAll();
        return view('admin.portal.struktur-organisasi.form', compact('parents'));
    }

    public function store(StorePortalStrukturOrganisasiRequest $request)
    {
        try {
            $this->service->createPerson($request->validated());
            return redirect()->route('admin.portal.struktur-organisasi.index')
                ->with('success', 'Anggota struktur organisasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $person  = $this->service->getById($id);
        // Exclude the person themselves to prevent self-referencing bug
        $parents = $this->service->getAll()->where('id', '!=', $id);

        return view('admin.portal.struktur-organisasi.form', compact('person', 'parents'));
    }

    public function update(UpdatePortalStrukturOrganisasiRequest $request, $id)
    {
        try {
            $this->service->updatePerson($id, $request->validated());
            return redirect()->route('admin.portal.struktur-organisasi.index')
                ->with('success', 'Data anggota struktur organisasi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->deletePerson($id);
            return redirect()->route('admin.portal.struktur-organisasi.index')
                ->with('success', 'Anggota struktur organisasi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.portal.struktur-organisasi.index')
                ->with('error', $e->getMessage());
        }
    }
}
