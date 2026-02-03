<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use Illuminate\Http\Request;

class KabupatenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kabupatens = Kabupaten::when($request->search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('kode', 'like', "%{$search}%");
        })
            ->orderBy('id', 'asc') // Order by ID to keep logical consistency
            ->paginate(10)
            ->withQueryString();

        return view('admin.master.kabupaten.index', compact('kabupatens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.kabupaten.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:m_kabupatens,kode|max:20',
        ]);

        Kabupaten::create($request->all());

        return redirect()->route('admin.master.kabupaten.index')->with('success', 'Kabupaten created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kabupaten $kabupaten)
    {
        return view('admin.master.kabupaten.edit', compact('kabupaten'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kabupaten $kabupaten)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20|unique:m_kabupatens,kode,' . $kabupaten->id,
        ]);

        $kabupaten->update($request->all());

        return redirect()->route('admin.master.kabupaten.index')->with('success', 'Kabupaten updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kabupaten $kabupaten)
    {
        // Check relationships to prevent orphan data (Optional but good practice)
        if ($kabupaten->kecamatans()->count() > 0) {
            return back()->with('error', 'Cannot delete Kabupaten that has associated Kecamatans.');
        }

        $kabupaten->delete();

        return redirect()->route('admin.master.kabupaten.index')->with('success', 'Kabupaten deleted successfully');
    }
}
