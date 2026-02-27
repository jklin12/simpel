<?php

namespace App\Http\Controllers\Admin\Portal;

use App\Http\Controllers\Controller;
use App\Models\PortalSlider;
use App\Services\PortalSliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    protected $service;

    public function __construct(PortalSliderService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'sort_by', 'sort_order']);
        $sliders = $this->service->getPaginated(10, $filters);

        return view('admin.portal.slider.index', compact('sliders', 'filters'));
    }

    public function create()
    {
        $warnaOptions = PortalSlider::warnaOptions();
        return view('admin.portal.slider.form', compact('warnaOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'sub_judul'  => 'nullable|string|max:255',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'warna_tema' => 'required|in:blue,green,orange',
            'label_cta_1' => 'nullable|string|max:100',
            'url_cta_1'  => 'nullable|string|max:500',
            'label_cta_2' => 'nullable|string|max:100',
            'url_cta_2'  => 'nullable|string|max:500',
            'urutan'     => 'nullable|integer|min:0',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        try {
            $this->service->createSlider($validated);

            return redirect()
                ->route('admin.portal.slider.index')
                ->with('success', 'Slider berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $slider       = $this->service->getById($id);
            $warnaOptions = PortalSlider::warnaOptions();

            return view('admin.portal.slider.form', compact('slider', 'warnaOptions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.portal.slider.index')->with('error', 'Slider tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'judul'      => 'required|string|max:255',
            'sub_judul'  => 'nullable|string|max:255',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'warna_tema' => 'required|in:blue,green,orange',
            'label_cta_1' => 'nullable|string|max:100',
            'url_cta_1'  => 'nullable|string|max:500',
            'label_cta_2' => 'nullable|string|max:100',
            'url_cta_2'  => 'nullable|string|max:500',
            'urutan'     => 'nullable|integer|min:0',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        try {
            $this->service->updateSlider($id, $validated);

            return redirect()
                ->route('admin.portal.slider.index')
                ->with('success', 'Slider berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->deleteSlider($id);

            return redirect()
                ->route('admin.portal.slider.index')
                ->with('success', 'Slider berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
