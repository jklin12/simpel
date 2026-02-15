<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovePermohonanRequest;
use App\Http\Requests\RejectPermohonanRequest;
use App\Services\PermohonanSuratService;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermohonanSuratController extends Controller
{
    protected $service;

    public function __construct(PermohonanSuratService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of permohonan based on user role and location.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['search', 'status', 'jenis_surat_id']);

        $permohonanSurats = $this->service->getPermohonanByUserRole($user, $filters, 15);
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();

        return view('admin.permohonan-surat.index', compact('permohonanSurats', 'jenisSurats'));
    }

    /**
     * Display the specified permohonan with approval timeline.
     */
    public function show($id)
    {
        try {
            $permohonanSurat = $this->service->getPermohonanById($id);
            $approvals = $permohonanSurat->approvals()->orderBy('step_order')->get();

            return view('admin.permohonan-surat.show', compact('permohonanSurat', 'approvals'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.permohonan-surat.index')
                ->with('error', 'Permohonan tidak ditemukan');
        }
    }

    /**
     * Approve the permohonan at current step.
     */
    public function approve(ApprovePermohonanRequest $request, $id)
    {
        try {
            $this->service->approvePermohonan($id, Auth::id(), $request->catatan);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Permohonan berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the permohonan.
     */
    public function reject(RejectPermohonanRequest $request, $id)
    {
        try {
            $this->service->rejectPermohonan($id, Auth::id(), $request->rejected_reason);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Permohonan telah ditolak');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Download generated letter (placeholder).
     */
    public function downloadLetter($id)
    {
        try {
            $permohonanSurat = $this->service->getPermohonanById($id);

            if ($permohonanSurat->status !== 'completed') {
                return redirect()
                    ->back()
                    ->with('error', 'Surat belum selesai diproses');
            }

            // TODO: Implement PDF generation
            return redirect()
                ->back()
                ->with('info', 'Fitur download PDF akan segera tersedia');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Permohonan tidak ditemukan');
        }
    }
}
