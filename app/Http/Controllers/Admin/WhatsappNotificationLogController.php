<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappNotificationLog;
use Illuminate\Http\Request;

class WhatsappNotificationLogController extends Controller
{
    /**
     * Display all WhatsApp notification logs.
     */
    public function index(Request $request)
    {
        $query = WhatsappNotificationLog::with('permohonan.jenisSurat')
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by notification type
        if ($request->filled('notification_type')) {
            $query->where('notification_type', $request->notification_type);
        }

        // Search by phone number or message preview
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone_to', 'like', "%{$search}%")
                  ->orWhere('message_preview', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25);
        $statuses = ['pending', 'sent', 'failed'];
        $notificationTypes = ['created', 'approved', 'rejected', 'revisi', 'sign_request'];

        return view('admin.whatsapp-logs.index', compact('logs', 'statuses', 'notificationTypes'));
    }

    /**
     * Retry a failed WhatsApp notification.
     */
    public function retry($logId)
    {
        try {
            $log = WhatsappNotificationLog::findOrFail($logId);

            if (!$log->permohonan) {
                return redirect()->back()->with('error', 'Permohonan tidak ditemukan untuk log ini.');
            }

            $permohonan = $log->permohonan;

            $notification = match($log->notification_type) {
                'created' => new \App\Notifications\PermohonanCreatedWhatsapp($permohonan),
                'approved' => new \App\Notifications\PermohonanApprovedWhatsapp($permohonan),
                'rejected' => new \App\Notifications\PermohonanRejectedWhatsapp($permohonan, $permohonan->rejected_reason ?? 'Tidak sesuai kriteria'),
                'revisi' => new \App\Notifications\PermohonanRevisiWhatsapp($permohonan),
                'sign_request' => new \App\Notifications\PermohonanSignRequestWhatsapp(
                    $permohonan,
                    $permohonan->currentApprovalStep?->approval_pejabat_name ?? 'Bapak/Ibu'
                ),
                default => throw new \Exception('Tipe notifikasi tidak dikenal: ' . $log->notification_type)
            };

            $permohonan->notify($notification);

            return redirect()->back()->with('success', 'Notifikasi WhatsApp sedang dikirim ulang ke antrian.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal retry notifikasi: ' . $e->getMessage());
        }
    }
}
