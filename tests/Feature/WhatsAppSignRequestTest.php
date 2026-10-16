<?php

namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use App\Models\User;
use App\Models\WhatsappNotificationLog;
use App\Notifications\PermohonanApprovedWhatsapp;
use App\Notifications\PermohonanSignRequestWhatsapp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WhatsAppSignRequestTest extends TestCase
{
    use DatabaseTransactions;

    private const NOMOR_LURAH = '081200000001';

    private Kelurahan $kelurahan;
    private JenisSurat $jenisSurat;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin']);

        $this->jenisSurat = JenisSurat::where('kode', 'SKTMR')->firstOrFail();
        $this->kelurahan  = Kelurahan::whereHas('kecamatan', fn ($q) => $q->where('id', 6372010))
            ->firstOrFail();

        Http::fake([
            '*/send-media'   => Http::response(['status' => true], 200),
            '*/send-message' => Http::response(['status' => true], 200),
        ]);
    }

    private function makePermohonan(): PermohonanSurat
    {
        $creator = User::factory()->create();

        return PermohonanSurat::create([
            'nomor_permohonan'   => 'REG/20261016/' . strtoupper(bin2hex(random_bytes(3))),
            'created_by_user_id' => $creator->id,
            'jenis_surat_id'     => $this->jenisSurat->id,
            'kelurahan_id'       => $this->kelurahan->id,
            'nama_pemohon'       => 'Budi Santoso',
            'nik_pemohon'        => '3374010101900001',
            'alamat_pemohon'     => 'Jl. Merdeka No. 10 RT 02 RW 03',
            'phone_pemohon'      => '085600200913',
            'keperluan'          => 'Pengurusan bantuan',
            'status'             => 'approved',
            'data_permohonan'    => [
                'nik_jenazah' => '3374010101800002',
                'pekerjaan'   => 'Buruh',
            ],
        ]);
    }

    /** @test */
    public function permintaan_ttd_on_demand_terkirim_dan_tercatat_di_log(): void
    {
        $permohonan = $this->makePermohonan();

        Notification::route('whatsapp', self::NOMOR_LURAH)
            ->notifyNow(new PermohonanSignRequestWhatsapp($permohonan, 'Bapak/Ibu Lurah'));

        Http::assertSent(fn (Request $request) =>
            str_contains($request->url(), '/send-')
            && str_contains($request->body(), self::NOMOR_LURAH)
        );

        $log = WhatsappNotificationLog::where('notification_type', 'sign_request')
            ->where('permohonan_id', $permohonan->id)
            ->first();

        $this->assertNotNull($log, 'Log sign_request harus tercatat');
        $this->assertSame(AnonymousNotifiable::class, $log->notifiable_type);
        $this->assertNull($log->notifiable_id);
        $this->assertSame(self::NOMOR_LURAH, $log->phone_to);
        $this->assertSame('sent', $log->status);
    }

    /** @test */
    public function notifikasi_ke_model_tetap_mencatat_notifiable_id(): void
    {
        $permohonan = $this->makePermohonan();

        $permohonan->notifyNow(new PermohonanApprovedWhatsapp($permohonan));

        $log = WhatsappNotificationLog::where('notification_type', 'approved')
            ->where('permohonan_id', $permohonan->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertSame(PermohonanSurat::class, $log->notifiable_type);
        $this->assertEquals($permohonan->id, $log->notifiable_id);
    }

    /** @test */
    public function retry_permintaan_ttd_dikirim_ke_pejabat_bukan_ke_pemohon(): void
    {
        $permohonan = $this->makePermohonan();

        $log = WhatsappNotificationLog::create([
            'permohonan_id'     => $permohonan->id,
            'notification_type' => 'sign_request',
            'notifiable_type'   => AnonymousNotifiable::class,
            'notifiable_id'     => null,
            'phone_to'          => self::NOMOR_LURAH,
            'status'            => 'failed',
        ]);

        Notification::fake();

        $admin = tap(User::factory()->create())->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('admin.whatsapp-logs.retry', $log->id))
            ->assertSessionHas('success');

        Notification::assertSentOnDemand(
            PermohonanSignRequestWhatsapp::class,
            fn ($notification, $channels, $notifiable) =>
                $notifiable->routes['whatsapp'] === self::NOMOR_LURAH
        );
        Notification::assertNotSentTo($permohonan, PermohonanSignRequestWhatsapp::class);
    }
}
