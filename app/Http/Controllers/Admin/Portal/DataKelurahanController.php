<?php

namespace App\Http\Controllers\Admin\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePortalDataKelurahanRequest;
use App\Http\Requests\UpdatePortalDataKelurahanRequest;
use App\Models\PortalDataKelurahan;
use App\Models\Kelurahan;
use App\Services\PortalDataKelurahanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataKelurahanController extends Controller
{
    protected $service;

    public function __construct(PortalDataKelurahanService $service)
    {
        $this->service = $service;
    }

    /**
     * Apakah user saat ini adalah admin kelurahan (bukan kecamatan/super_admin)?
     */
    private function isAdminKelurahan(): bool
    {
        return Auth::user()->hasRole('admin_kelurahan') && !Auth::user()->hasAnyRole(['admin_kecamatan', 'admin_kabupaten', 'super_admin']);
    }

    /**
     * Ambil kelurahan_id untuk scope filter (hanya untuk admin kelurahan).
     */
    private function scopedKelurahanId(): ?string
    {
        if ($this->isAdminKelurahan()) {
            return Auth::user()->kelurahan_id;
        }

        return null;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'kategori', 'kelurahan_id', 'sort_by', 'sort_order', 'rt', 'rw']);

        // Admin kelurahan: paksa filter ke kelurahan sendiri
        if ($this->isAdminKelurahan()) {
            $filters['kelurahan_id'] = Auth::user()->kelurahan_id;
        }

        $data         = $this->service->getPaginated(15, $filters);
        $kategoriList = PortalDataKelurahan::labelKategori();

        // Admin kecamatan/super_admin bisa lihat semua kelurahan untuk filter
        $kelurahans = $this->isAdminKelurahan()
            ? collect()
            : Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

        return view('admin.portal.data-kelurahan.index', compact('data', 'filters', 'kategoriList', 'kelurahans'));
    }

    public function create()
    {
        $kategoriList = PortalDataKelurahan::labelKategori();

        // Admin kelurahan tidak perlu memilih kelurahan — sudah auto-set
        $kelurahans = $this->isAdminKelurahan()
            ? collect()
            : Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

        // Ambil opsi statis dari Model
        $options = [
            'ibadah'     => PortalDataKelurahan::opsiJenisIbadah(),
            'pemakaman'  => PortalDataKelurahan::opsiJenisPemakaman(),
            'pendidikan' => PortalDataKelurahan::opsiJenisPendidikan(),
            'kesehatan'  => PortalDataKelurahan::opsiJenisKesehatan(),
            'keamanan'   => PortalDataKelurahan::opsiJenisKeamanan(),
            'status'     => PortalDataKelurahan::opsiStatusFasilitas(),
        ];

        return view('admin.portal.data-kelurahan.form', compact('kategoriList', 'kelurahans', 'options'));
    }

    public function store(StorePortalDataKelurahanRequest $request)
    {
        try {
            $validated = $request->validated();

            // Admin kelurahan: force set kelurahan_id ke milik sendiri
            if ($this->isAdminKelurahan()) {
                $validated['kelurahan_id'] = Auth::user()->kelurahan_id;
            }

            $this->service->createData($validated);

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
            $item = $this->service->getById($id);

            // Admin kelurahan hanya boleh edit data milik kelurahan sendiri
            if ($this->isAdminKelurahan() && $item->kelurahan_id != Auth::user()->kelurahan_id) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            $kategoriList = PortalDataKelurahan::labelKategori();

            $kelurahans = $this->isAdminKelurahan()
                ? collect()
                : Kelurahan::where('kecamatan_id', '6372010')->orderBy('nama')->get();

            // Ambil opsi statis dari Model
            $options = [
                'ibadah'     => PortalDataKelurahan::opsiJenisIbadah(),
                'pemakaman'  => PortalDataKelurahan::opsiJenisPemakaman(),
                'pendidikan' => PortalDataKelurahan::opsiJenisPendidikan(),
                'kesehatan'  => PortalDataKelurahan::opsiJenisKesehatan(),
                'keamanan'   => PortalDataKelurahan::opsiJenisKeamanan(),
                'status'     => PortalDataKelurahan::opsiStatusFasilitas(),
            ];

            return view('admin.portal.data-kelurahan.form', compact('item', 'kategoriList', 'kelurahans', 'options'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.portal.data-kelurahan.index')
                ->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(UpdatePortalDataKelurahanRequest $request, $id)
    {
        try {
            $item = $this->service->getById($id);

            // Admin kelurahan hanya boleh update data milik kelurahan sendiri
            if ($this->isAdminKelurahan() && $item->kelurahan_id != Auth::user()->kelurahan_id) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

            $validated = $request->validated();

            // Admin kelurahan: force set kelurahan_id ke milik sendiri
            if ($this->isAdminKelurahan()) {
                $validated['kelurahan_id'] = Auth::user()->kelurahan_id;
            }

            $this->service->updateData($id, $validated);

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
            $item = $this->service->getById($id);

            // Admin kelurahan hanya boleh hapus data milik kelurahan sendiri
            if ($this->isAdminKelurahan() && $item->kelurahan_id != Auth::user()->kelurahan_id) {
                abort(403, 'Anda tidak memiliki akses ke data ini.');
            }

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
