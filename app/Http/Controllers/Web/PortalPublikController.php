<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PortalBeritaService;
use App\Services\PortalDataKelurahanService;
use App\Services\PortalFaqService;
use App\Services\PortalSliderService;

class PortalPublikController extends Controller
{
    protected $beritaService;
    protected $dataService;
    protected $strukturService;
    protected $sliderService;
    protected $faqService;

    public function __construct(
        PortalBeritaService $beritaService,
        PortalDataKelurahanService $dataService,
        \App\Services\PortalStrukturOrganisasiService $strukturService,
        PortalSliderService $sliderService,
        PortalFaqService $faqService
    ) {
        $this->beritaService   = $beritaService;
        $this->dataService     = $dataService;
        $this->strukturService = $strukturService;
        $this->sliderService   = $sliderService;
        $this->faqService      = $faqService;
    }

    /**
     * Halaman utama portal kecamatan.
     */
    public function index()
    {
        $beritaTerbaru = $this->beritaService->getLatestPublished(3);
        $sliders       = $this->sliderService->getAktifSliders();
        $layananPopuler = $this->layananPopuler(6);

        // Tautan bantuan WhatsApp memakai nomor dari pengaturan popup layanan (dikelola admin).
        $popup = app(\App\Services\LayananPopupService::class)->getSingleton();
        $bantuanWa = ($popup && $popup->normalized_wa_number)
            ? 'https://wa.me/' . $popup->normalized_wa_number
            : null;

        return view('portal.index', compact('beritaTerbaru', 'sliders', 'layananPopuler', 'bantuanWa'));
    }

    /**
     * Jenis surat dengan permohonan terbanyak bulan ini. Bila kurang dari $limit,
     * dilengkapi jenis surat aktif lain agar carousel tidak kosong.
     */
    protected function layananPopuler(int $limit): \Illuminate\Support\Collection
    {
        $topIds = \App\Models\PermohonanSurat::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw('jenis_surat_id, COUNT(*) as total')
            ->groupBy('jenis_surat_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('jenis_surat_id');

        $aktif = \App\Models\JenisSurat::where('is_active', true)->get()->keyBy('id');

        $terpilih = $topIds->map(fn ($id) => $aktif->get($id))->filter();
        if ($terpilih->count() < $limit) {
            $terpilih = $terpilih->concat(
                $aktif->reject(fn ($j) => $terpilih->contains('id', $j->id))->take($limit - $terpilih->count())
            );
        }

        $kodeKeTone = [];
        foreach (config('layanan.kategori') as $kat) {
            foreach ($kat['kode'] as $kode) {
                $kodeKeTone[$kode] = $kat['tone'];
            }
        }

        return $terpilih->values()->map(fn ($j) => [
            'kode' => $j->kode,
            'nama' => $j->nama,
            'tone' => $kodeKeTone[$j->kode] ?? config('layanan.kategori_lainnya.tone'),
        ]);
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

    /**
     * Halaman FAQ Portal
     */
    public function faq()
    {
        $faqGrouped    = $this->faqService->getGroupedAktif();
        $kategoriLabels = \App\Models\PortalFaq::kategoriOptions();
        return view('portal.faq', compact('faqGrouped', 'kategoriLabels'));
    }
}
