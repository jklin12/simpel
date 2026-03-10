<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kecamatans = Kecamatan::with('kabupaten')
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhereHas('kabupaten', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->where('kabupaten_id', 6372)
            ->orderBy('kode', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.kecamatan.index', compact('kecamatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kabupatens = Kabupaten::orderBy('nama')->get();
        return view('admin.master.kecamatan.create', compact('kabupatens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:m_kabupatens,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:m_kecamatans,kode|max:20',
            'camat_nama' => 'nullable|string|max:255',
            'camat_nip' => 'nullable|string|max:255',
            'camat_pangkat' => 'nullable|string|max:255',
            'camat_golongan' => 'nullable|string|max:255',
            'kop_surat_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('kop_surat_path');

        if ($request->hasFile('kop_surat_path')) {
            $path = $request->file('kop_surat_path')->store('kop_surat_kecamatan', 'public');
            $data['kop_surat_path'] = $path;
        }

        Kecamatan::create($data);

        return redirect()->route('admin.master.kecamatan.index')->with('success', 'Kecamatan created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kecamatan $kecamatan)
    {
        $kabupatens = Kabupaten::orderBy('nama')->get();
        return view('admin.master.kecamatan.edit', compact('kecamatan', 'kabupatens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kecamatan $kecamatan)
    {
        $request->validate([
            'kabupaten_id' => 'required|exists:m_kabupatens,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20|unique:m_kecamatans,kode,' . $kecamatan->id,
            'camat_nama' => 'nullable|string|max:255',
            'camat_nip' => 'nullable|string|max:255',
            'camat_pangkat' => 'nullable|string|max:255',
            'camat_golongan' => 'nullable|string|max:255',
            'kop_surat_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except('kop_surat_path');

        if ($request->hasFile('kop_surat_path')) {
            // Delete old file if exists
            if ($kecamatan->kop_surat_path && Storage::disk('public')->exists($kecamatan->kop_surat_path)) {
                Storage::disk('public')->delete($kecamatan->kop_surat_path);
            }
            $path = $request->file('kop_surat_path')->store('kop_surat_kecamatan', 'public');
            $data['kop_surat_path'] = $path;
        }

        $kecamatan->update($data);

        return redirect()->route('admin.master.kecamatan.index')->with('success', 'Kecamatan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kecamatan $kecamatan)
    {
        if ($kecamatan->kelurahans()->count() > 0) {
            return back()->with('error', 'Cannot delete Kecamatan that has associated Kelurahans.');
        }

        $kecamatan->delete();

        return redirect()->route('admin.master.kecamatan.index')->with('success', 'Kecamatan deleted successfully');
    }
}
