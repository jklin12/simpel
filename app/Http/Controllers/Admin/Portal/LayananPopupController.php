<?php

namespace App\Http\Controllers\Admin\Portal;

use App\Http\Controllers\Controller;
use App\Services\LayananPopupService;
use Illuminate\Http\Request;

class LayananPopupController extends Controller
{
    protected $service;

    public function __construct(LayananPopupService $service)
    {
        $this->service = $service;
    }

    public function edit()
    {
        $popup = $this->service->getSingleton();

        return view('admin.portal.layanan-popup.edit', compact('popup'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'wa_number'   => 'required|string|max:20',
            'wa_message'  => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        try {
            $this->service->updatePopup($validated);

            return redirect()
                ->route('admin.portal.layanan-popup.edit')
                ->with('success', 'Pengaturan popup berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
