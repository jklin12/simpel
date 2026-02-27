<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PortalBeritaService;
use App\Services\PortalDataKelurahanService;
use App\Services\PortalSliderService;

class PortalPublikController extends Controller
{
    protected $beritaService;
    protected $dataService;
    protected $strukturService;
    protected $sliderService;

    public function __construct(
        PortalBeritaService $beritaService,
        PortalDataKelurahanService $dataService,
        \App\Services\PortalStrukturOrganisasiService $strukturService,
        PortalSliderService $sliderService
    ) {
        $this->beritaService   = $beritaService;
        $this->dataService     = $dataService;
        $this->strukturService = $strukturService;
        $this->sliderService   = $sliderService;
    }

    /**
     * Halaman utama portal kecamatan.
     */
    public function index()
    {
        $beritaTerbaru = $this->beritaService->getLatestPublished(3);
        $sliders       = $this->sliderService->getAktifSliders();
        return view('portal.index', compact('beritaTerbaru', 'sliders'));
    }

    /** PREVIEW SEMENTARA - hapus setelah pilih desain **/
    public function previewDesain1()
    {
        $beritaTerbaru = $this->beritaService->getLatestPublished(3);
        $sliders       = $this->sliderService->getAktifSliders();
        return view('portal.index-1', compact('beritaTerbaru', 'sliders'));
    }

    public function previewDesain2()
    {
        $beritaTerbaru = $this->beritaService->getLatestPublished(3);
        return view('portal.index-2', compact('beritaTerbaru'));
    }

    public function previewDesain3()
    {
        $beritaTerbaru = $this->beritaService->getLatestPublished(3);
        return view('portal.index-3', compact('beritaTerbaru'));
    }

    public function previewDesainR()
    {
        $beritaTerbaru = $this->beritaService->getLatestPublished(3);
        return view('portal.index-r', compact('beritaTerbaru'));
    }

    /**
     * Daftar semua berita yang sudah dipublikasikan.
     */
    public function berita()
    {
        $beritas = $this->beritaService->getPublished(9);

        return view('portal.berita.index', compact('beritas'));
    }

    /**
     * Detail satu berita berdasarkan slug.
     */
    public function beritaDetail(string $slug)
    {
        try {
            $berita        = $this->beritaService->getBySlug($slug);
            $beritaLainnya = $this->beritaService->getLatestPublished(3);

            return view('portal.berita.show', compact('berita', 'beritaLainnya'));
        } catch (\Exception $e) {
            abort(404, 'Berita tidak ditemukan.');
        }
    }

    /**
     * Halaman peta interaktif.
     */
    public function peta()
    {
        return view('portal.peta');
    }

    /**
     * API endpoint: return data peta dalam format JSON untuk Leaflet.js.
     */
    public function petaData()
    {
        $data = $this->dataService->getDataForMap();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Halaman Struktur Organisasi (UI Bagan)
     */
    public function strukturOrganisasi()
    {
        $strukturTree = $this->strukturService->getTreeData();
        return view('portal.struktur-organisasi', compact('strukturTree'));
    }
}
