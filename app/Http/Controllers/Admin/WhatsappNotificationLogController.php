<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappNotificationLog;
use App\Support\PhoneNumberFormatter;
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

            $notification = $this->buildNotification($log);

            $log->permohonan->notify($notification);

            return redirect()->back()->with('success', 'Notifikasi WhatsApp sedang dikirim ulang ke antrian.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal retry notifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Redirect ke WhatsApp Web/aplikasi dengan nomor tujuan dan pesan lengkap
     * sudah terisi, sehingga admin tinggal klik "Kirim" secara manual.
     */
    public function waWeb($logId)
    {
        $log = WhatsappNotificationLog::findOrFail($logId);

        // Default pakai preview yang tersimpan; ganti dengan pesan lengkap bila bisa.
        $message = $log->message_preview ?? '';

        if ($log->permohonan) {
            try {
                $data = $this->buildNotification($log)->toWhatsApp($log->permohonan);
                $message = is_array($data) ? ($data['message'] ?? $message) : $data;
            } catch (\Throwable $e) {
                // Abaikan — tetap gunakan message_preview sebagai fallback.
            }
        }

        $phone = $this->normalizePhone($log->phone_to);

        if ($phone === '') {
            return redirect()->back()->with('error', 'Nomor tujuan tidak valid untuk dikirim manual.');
        }

        $url = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);

        return redirect()->away($url);
    }

    /**
     * Bangun instance notifikasi WhatsApp sesuai tipe log.
     */
    private function buildNotification(WhatsappNotificationLog $log)
    {
        $permohonan = $log->permohonan;

        if (!$permohonan) {
            throw new \Exception('Permohonan tidak ditemukan untuk log ini.');
        }

        return match($log->notification_type) {
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
    }

    /**
     * Normalisasi nomor telepon Indonesia ke format internasional (62...)
     * tanpa tanda plus, sesuai kebutuhan tautan wa.me.
     */
    private function normalizePhone(?string $phone): string
    {
        return PhoneNumberFormatter::normalizeIndonesian($phone);
    }
}
