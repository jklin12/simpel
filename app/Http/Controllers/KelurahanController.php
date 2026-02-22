<?php

namespace App\Http\Controllers;

use App\Models\Kelurahan;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KelurahanController extends Controller
{
    public function index(Request $request)
    {
        $kelurahans = Kelurahan::with(['kecamatan', 'kecamatan.kabupaten'])
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhereHas('kecamatan', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->whereIn('kecamatan_id', [6372010])
            ->orderBy('kode', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.kelurahan.index', compact('kelurahans'));
    }

    public function create()
    {
        $kecamatans = Kecamatan::with('kabupaten')->orderBy('nama')->get();
        return view('admin.master.kelurahan.create', compact('kecamatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kecamatan_id'  => 'required|exists:m_kecamatans,id',
            'nama'          => 'required|string|max:255',
            'kode'          => 'required|string|unique:m_kelurahans,kode|max:20',
            'akronim'       => 'nullable|string|max:20',
            'lurah_nama'    => 'nullable|string|max:255',
            'lurah_nip'     => 'nullable|string|max:50',
            'lurah_pangkat' => 'nullable|string|max:100',
            'lurah_golongan'=> 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'telp'          => 'nullable|string|max:20',
            'kop_surat'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['kop_surat', '_token']);

        if ($request->hasFile('kop_surat')) {
            $data['kop_surat_path'] = $request->file('kop_surat')
                ->store('kelurahan/kop-surat', 'public');
        }

        Kelurahan::create($data);

        return redirect()->route('admin.master.kelurahan.index')
            ->with('success', 'Kelurahan berhasil ditambahkan');
    }

    public function edit(Kelurahan $kelurahan)
    {
        $kecamatans = Kecamatan::with('kabupaten')->orderBy('nama')->get();
        return view('admin.master.kelurahan.edit', compact('kelurahan', 'kecamatans'));
    }

    public function update(Request $request, Kelurahan $kelurahan)
    {
        $request->validate([
            'kecamatan_id'  => 'required|exists:m_kecamatans,id',
            'nama'          => 'required|string|max:255',
            'kode'          => 'required|string|max:20|unique:m_kelurahans,kode,' . $kelurahan->id,
            'akronim'       => 'nullable|string|max:20',
            'lurah_nama'    => 'nullable|string|max:255',
            'lurah_nip'     => 'nullable|string|max:50',
            'lurah_pangkat' => 'nullable|string|max:100',
            'lurah_golongan'=> 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'telp'          => 'nullable|string|max:20',
            'kop_surat'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['kop_surat', '_token', '_method']);

        if ($request->hasFile('kop_surat')) {
            // Hapus kop surat lama
            if ($kelurahan->kop_surat_path) {
                Storage::disk('public')->delete($kelurahan->kop_surat_path);
            }
            $data['kop_surat_path'] = $request->file('kop_surat')
                ->store('kelurahan/kop-surat', 'public');
        }

        $kelurahan->update($data);

        return redirect()->route('admin.master.kelurahan.index')
            ->with('success', 'Kelurahan berhasil diperbarui');
    }

    public function destroy(Kelurahan $kelurahan)
    {
        if ($kelurahan->kop_surat_path) {
            Storage::disk('public')->delete($kelurahan->kop_surat_path);
        }
        $kelurahan->delete();

        return redirect()->route('admin.master.kelurahan.index')
            ->with('success', 'Kelurahan berhasil dihapus');
    }
}
