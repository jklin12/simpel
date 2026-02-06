<?php

namespace App\Http\Controllers;

use App\Models\PermohonanSurat;
use App\Models\PermohonanApproval;
use App\Models\JenisSurat;
use App\Models\SuratCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PermohonanSuratController extends Controller
{
    /**
     * Display a listing of permohonan based on user role and location.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PermohonanSurat::with(['jenisSurat', 'kelurahan', 'createdBy', 'approvals'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nomor_permohonan', 'like', "%{$search}%")
                        ->orWhere('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('nik_pemohon', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->jenis_surat_id, function ($q, $jenisSuratId) {
                $q->where('jenis_surat_id', $jenisSuratId);
            });

        // Filter based on user role
        if ($user->hasRole('admin_kelurahan')) {
            $query->where('kelurahan_id', $user->kelurahan_id);
        } elseif ($user->hasRole('admin_kecamatan')) {
            $query->whereHas('kelurahan', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        } elseif ($user->hasRole('admin_kabupaten')) {
            $query->whereHas('kelurahan.kecamatan', function ($q) use ($user) {
                $q->where('kabupaten_id', $user->kabupaten_id);
            });
        }
        // super_admin sees all

        $permohonanSurats = $query->latest()->paginate(15)->withQueryString();
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();

        return view('admin.permohonan-surat.index', compact('permohonanSurats', 'jenisSurats'));
    }

    /**
     * Display the specified permohonan with approval timeline.
     */
    public function show(PermohonanSurat $permohonanSurat)
    {
        $permohonanSurat->load(['jenisSurat', 'kelurahan.kecamatan', 'createdBy', 'approvals.approver']);

        // Get approval timeline
        $approvals = $permohonanSurat->approvals()->orderBy('step_order')->get();

        return view('admin.permohonan-surat.show', compact('permohonanSurat', 'approvals'));
    }

    /**
     * Approve the permohonan at current step.
     */
    public function approve(Request $request, PermohonanSurat $permohonanSurat)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Get current pending approval for this user's role
            $currentApproval = $permohonanSurat->approvals()
                ->where('status', 'pending')
                ->where('step_order', $permohonanSurat->current_step)
                ->first();

            if (!$currentApproval) {
                return back()->with('error', 'Tidak ada approval yang pending untuk permohonan ini.');
            }

            // Check if user has the right role
            if (!$user->hasRole($currentApproval->target_role)) {
                return back()->with('error', 'Anda tidak memiliki wewenang untuk approve di step ini.');
            }

            // Update approval
            $currentApproval->update([
                'status' => 'approved',
                'user_id' => $user->id,
                'catatan' => $request->catatan,
                'approved_at' => now(),
            ]);

            // Check if there are more steps
            $nextApproval = $permohonanSurat->approvals()
                ->where('step_order', '>', $currentApproval->step_order)
                ->orderBy('step_order')
                ->first();

            if ($nextApproval) {
                // Move to next step
                $permohonanSurat->update([
                    'status' => 'in_review',
                    'current_step' => $nextApproval->step_order,
                ]);
            } else {
                // Final approval - generate letter number
                $nomorSurat = $this->generateNomorSurat($permohonanSurat);

                $permohonanSurat->update([
                    'status' => 'completed',
                    'current_step' => null,
                    'nomor_surat' => $nomorSurat,
                    'tanggal_surat' => now(),
                    'completed_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.permohonan-surat.show', $permohonanSurat->id)
                ->with('success', 'Permohonan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject the permohonan.
     */
    public function reject(Request $request, PermohonanSurat $permohonanSurat)
    {
        $request->validate([
            'rejected_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Get current pending approval
            $currentApproval = $permohonanSurat->approvals()
                ->where('status', 'pending')
                ->where('step_order', $permohonanSurat->current_step)
                ->first();

            if (!$currentApproval) {
                return back()->with('error', 'Tidak ada approval yang pending untuk permohonan ini.');
            }

            // Check if user has the right role
            if (!$user->hasRole($currentApproval->target_role)) {
                return back()->with('error', 'Anda tidak memiliki wewenang untuk reject di step ini.');
            }

            // Update approval
            $currentApproval->update([
                'status' => 'rejected',
                'user_id' => $user->id,
                'catatan' => $request->rejected_reason,
                'approved_at' => now(),
            ]);

            // Update permohonan
            $permohonanSurat->update([
                'status' => 'rejected',
                'rejected_reason' => $request->rejected_reason,
            ]);

            DB::commit();

            return redirect()->route('admin.permohonan-surat.show', $permohonanSurat->id)
                ->with('success', 'Permohonan telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor surat with auto-increment counter.
     */
    private function generateNomorSurat(PermohonanSurat $permohonan)
    {
        $jenisSurat = $permohonan->jenisSurat;
        $kelurahan = $permohonan->kelurahan;
        $now = Carbon::now();

        // Get or create counter for this month
        $counter = SuratCounter::firstOrCreate(
            [
                'jenis_surat_id' => $jenisSurat->id,
                'kelurahan_id' => $kelurahan->id,
                'tahun' => $now->year,
                'bulan' => $now->month,
            ],
            ['current_number' => 0]
        );

        // Increment counter
        $counter->increment('current_number');
        $counter->refresh();

        // Format: 001/SKD/KEL-A/II/2026
        $nomorSurat = sprintf(
            '%03d/%s/%s/%s/%s',
            $counter->current_number,
            $jenisSurat->kode,
            $kelurahan->kode,
            $now->format('m'),
            $now->format('Y')
        );

        return $nomorSurat;
    }

    /**
     * Download generated letter (placeholder).
     */
    public function downloadLetter(PermohonanSurat $permohonanSurat)
    {
        if ($permohonanSurat->status !== 'completed') {
            return back()->with('error', 'Surat belum selesai diproses.');
        }

        // TODO: Implement PDF generation
        return back()->with('info', 'Fitur download PDF akan segera tersedia.');
    }
}
