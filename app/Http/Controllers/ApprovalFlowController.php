<?php

namespace App\Http\Controllers;

use App\Models\ApprovalFlow;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class ApprovalFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $approvalFlows = ApprovalFlow::with(['jenisSurat', 'kelurahan'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('jenisSurat', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })
                    ->orWhereHas('kelurahan', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.approval-flow.index', compact('approvalFlows'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();
        $kelurahans = Kelurahan::with('kecamatan')->orderBy('nama')->get();

        return view('admin.approval-flow.create', compact('jenisSurats', 'kelurahans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'kelurahan_id' => 'required|exists:m_kelurahans,id',
            'require_kecamatan_approval' => 'boolean',
            'require_kabupaten_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate
        $exists = ApprovalFlow::where('jenis_surat_id', $request->jenis_surat_id)
            ->where('kelurahan_id', $request->kelurahan_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Approval Flow untuk kombinasi Jenis Surat dan Kelurahan ini sudah ada.')->withInput();
        }

        ApprovalFlow::create([
            'jenis_surat_id' => $request->jenis_surat_id,
            'kelurahan_id' => $request->kelurahan_id,
            'require_kecamatan_approval' => $request->has('require_kecamatan_approval'),
            'require_kabupaten_approval' => $request->has('require_kabupaten_approval'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.approval-flow.index')->with('success', 'Approval Flow berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovalFlow $approvalFlow)
    {
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();
        $kelurahans = Kelurahan::with('kecamatan')->orderBy('nama')->get();

        return view('admin.approval-flow.edit', compact('approvalFlow', 'jenisSurats', 'kelurahans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApprovalFlow $approvalFlow)
    {
        $request->validate([
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'kelurahan_id' => 'required|exists:m_kelurahans,id',
            'require_kecamatan_approval' => 'boolean',
            'require_kabupaten_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate (excluding current record)
        $exists = ApprovalFlow::where('jenis_surat_id', $request->jenis_surat_id)
            ->where('kelurahan_id', $request->kelurahan_id)
            ->where('id', '!=', $approvalFlow->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Approval Flow untuk kombinasi Jenis Surat dan Kelurahan ini sudah ada.')->withInput();
        }

        $approvalFlow->update([
            'jenis_surat_id' => $request->jenis_surat_id,
            'kelurahan_id' => $request->kelurahan_id,
            'require_kecamatan_approval' => $request->has('require_kecamatan_approval'),
            'require_kabupaten_approval' => $request->has('require_kabupaten_approval'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.approval-flow.index')->with('success', 'Approval Flow berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalFlow $approvalFlow)
    {
        // Check if there are related permohonan
        if ($approvalFlow->permohonanSurats()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus Approval Flow yang memiliki permohonan terkait.');
        }

        $approvalFlow->delete();

        return redirect()->route('admin.approval-flow.index')->with('success', 'Approval Flow berhasil dihapus');
    }
}
