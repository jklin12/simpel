<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PiiAccessLog;
use App\Models\WhatsappNotificationLog;
use App\Services\PermohonanSuratService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Endpoint reveal nilai PII penuh untuk halaman admin.
 *
 * Diproteksi middleware `role:super_admin` di routes/admin.php. Setiap reveal
 * memvalidasi field terhadap whitelist config('pii') dan menulis PiiAccessLog.
 */
class PiiRevealController extends Controller
{
    public function __construct(
        protected PermohonanSuratService $permohonanService
    ) {
    }

    public function reveal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source' => ['required', 'string', 'in:permohonan,wa_log'],
            'id'     => ['required', 'integer'],
            'field'  => ['required', 'string'],
        ]);

        $source = $validated['source'];
        $id     = (int) $validated['id'];
        $field  = $validated['field'];

        $value = $source === 'wa_log'
            ? $this->resolveWaLog($id, $field)
            : $this->resolvePermohonan($id, $field);

        $this->log($source, $id, $field, $request);

        return response()->json([
            'value' => $value === null || $value === '' ? '-' : (string) $value,
        ]);
    }

    /**
     * Ambil nilai PII dari permohonan (kolom langsung atau key data_permohonan).
     * Scoping wilayah tetap dijalankan lewat service (super_admin akses penuh).
     */
    protected function resolvePermohonan(int $id, string $field): ?string
    {
        $permohonan = $this->permohonanService->getPermohonanById($id);
        abort_if(!$permohonan, 404);

        $columns = (array) config('pii.permohonan_columns', []);

        // Kolom PII langsung (whitelisted).
        if (array_key_exists($field, $columns)) {
            return $permohonan->{$field};
        }

        // Key PII di dalam data_permohonan — dibatasi oleh Pii::isPiiKey.
        $data = is_array($permohonan->data_permohonan) ? $permohonan->data_permohonan : [];
        if (array_key_exists($field, $data) && \App\Support\Pii::isPiiKey($field)) {
            return is_scalar($data[$field]) ? (string) $data[$field] : null;
        }

        abort(422, 'Field tidak diperbolehkan.');
    }

    /**
     * Ambil nilai PII dari log WhatsApp (hanya kolom yang di-whitelist).
     */
    protected function resolveWaLog(int $id, string $field): ?string
    {
        $columns = (array) config('pii.wa_log_columns', []);
        abort_unless(array_key_exists($field, $columns), 422, 'Field tidak diperbolehkan.');

        $log = WhatsappNotificationLog::find($id);
        abort_if(!$log, 404);

        return $log->{$field};
    }

    protected function log(string $source, int $id, string $field, Request $request): void
    {
        PiiAccessLog::create([
            'user_id'    => Auth::id(),
            'source'     => $source,
            'subject_id' => $id,
            'field'      => $field,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
    }
}
