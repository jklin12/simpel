<?php

namespace App\Http\Controllers\Admin\Portal;

use App\Http\Controllers\Controller;
use App\Models\PortalFaq;
use App\Services\PortalFaqService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    protected $service;

    public function __construct(PortalFaqService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters        = $request->only(['search', 'kategori', 'status']);
        $faqs           = $this->service->getPaginated(15, $filters);
        $kategoriOptions = PortalFaq::kategoriOptions();

        return view('admin.portal.faq.index', compact('faqs', 'filters', 'kategoriOptions'));
    }

    public function create()
    {
        $kategoriOptions = PortalFaq::kategoriOptions();
        return view('admin.portal.faq.form', compact('kategoriOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'jawaban'    => 'required|string',
            'kategori'   => 'required|in:umum,surat,kependudukan,perizinan,lainnya',
            'urutan'     => 'nullable|integer|min:0',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        try {
            $this->service->createFaq($validated);
            return redirect()->route('admin.portal.faq.index')->with('success', 'FAQ berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $faq             = $this->service->getById($id);
            $kategoriOptions = PortalFaq::kategoriOptions();
            return view('admin.portal.faq.form', compact('faq', 'kategoriOptions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.portal.faq.index')->with('error', 'FAQ tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:500',
            'jawaban'    => 'required|string',
            'kategori'   => 'required|in:umum,surat,kependudukan,perizinan,lainnya',
            'urutan'     => 'nullable|integer|min:0',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        try {
            $this->service->updateFaq($id, $validated);
            return redirect()->route('admin.portal.faq.index')->with('success', 'FAQ berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->deleteFaq($id);
            return redirect()->route('admin.portal.faq.index')->with('success', 'FAQ berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
