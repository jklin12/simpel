<?php

namespace App\Http\Controllers;

use App\Models\Kelurahan;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KelurahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            ->whereIn('kecamatan_id', [2706, 2707, 2708, 2709, 2710])
            ->orderBy('kode', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.kelurahan.index', compact('kelurahans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kecamatans = Kecamatan::with('kabupaten')->orderBy('nama')->get();
        return view('admin.master.kelurahan.create', compact('kecamatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kecamatan_id' => 'required|exists:m_kecamatans,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:m_kelurahans,kode|max:20',
        ]);

        Kelurahan::create($request->all());

        return redirect()->route('admin.master.kelurahan.index')->with('success', 'Kelurahan created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelurahan $kelurahan)
    {
        $kecamatans = Kecamatan::with('kabupaten')->orderBy('nama')->get();
        return view('admin.master.kelurahan.edit', compact('kelurahan', 'kecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelurahan $kelurahan)
    {
        $request->validate([
            'kecamatan_id' => 'required|exists:m_kecamatans,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20|unique:m_kelurahans,kode,' . $kelurahan->id,
        ]);

        $kelurahan->update($request->all());

        return redirect()->route('admin.master.kelurahan.index')->with('success', 'Kelurahan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelurahan $kelurahan)
    {
        $kelurahan->delete();

        return redirect()->route('admin.master.kelurahan.index')->with('success', 'Kelurahan deleted successfully');
    }
}
