<?php

namespace App\Http\Controllers\Admin\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortalDataKelurahanRequest;
use App\Http\Requests\UpdatePortalDataKelurahanRequest;
use App\Models\PortalDataKelurahan;
use App\Models\Kelurahan;
use App\Services\PortalDataKelurahanService;
use Illuminate\Http\Request;

class DataKelurahanController extends Controller
{
    protected $service;

    public function __construct(PortalDataKelurahanService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters    = $request->only(['search', 'kategori', 'kelurahan_id', 'sort_by', 'sort_order']);
        $data       = $this->service->getPaginated(15, $filters);
        $kategoriList = PortalDataKelurahan::labelKategori();
        $kelurahans   = Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

        return view('admin.portal.data-kelurahan.index', compact('data', 'filters', 'kategoriList', 'kelurahans'));
    }

    public function create()
    {
        $kategoriList = PortalDataKelurahan::labelKategori();
        $kelurahans   = Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

        return view('admin.portal.data-kelurahan.form', compact('kategoriList', 'kelurahans'));
    }

    public function store(StorePortalDataKelurahanRequest $request)
    {
        try {
            $this->service->createData($request->validated());

            return redirect()
                ->route('admin.portal.data-kelurahan.index')
                ->with('success', 'Data berhasil ditambahkan.');
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
            $item         = $this->service->getById($id);
            $kategoriList = PortalDataKelurahan::labelKategori();
            $kelurahans   = Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

            return view('admin.portal.data-kelurahan.form', compact('item', 'kategoriList', 'kelurahans'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.portal.data-kelurahan.index')
                ->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(UpdatePortalDataKelurahanRequest $request, $id)
    {
        try {
            $this->service->updateData($id, $request->validated());

            return redirect()
                ->route('admin.portal.data-kelurahan.index')
                ->with('success', 'Data berhasil diperbarui.');
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
            $this->service->deleteData($id);

            return redirect()
                ->route('admin.portal.data-kelurahan.index')
                ->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
