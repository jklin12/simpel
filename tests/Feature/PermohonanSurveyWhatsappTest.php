<?php

namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use App\Models\User;
use App\Models\WhatsappNotificationLog;
use App\Notifications\PermohonanApprovedWhatsapp;
use App\Notifications\PermohonanSurveyWhatsapp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermohonanSurveyWhatsappTest extends TestCase
{
    use DatabaseTransactions;

    private function makePermohonan(string $status = 'approved'): PermohonanSurat
    {
        $kelurahan = Kelurahan::whereHas('kecamatan', fn ($q) => $q->where('id', 6372010))->firstOrFail();

        return PermohonanSurat::create([
            'nomor_permohonan'   => 'REG/20261026/' . strtoupper(bin2hex(random_bytes(3))),
            'created_by_user_id' => User::factory()->create()->id,
            'jenis_surat_id'     => JenisSurat::where('kode', 'SKTM')->firstOrFail()->id,
            'kelurahan_id'       => $kelurahan->id,
            'nama_pemohon'       => 'Henny',
            'nik_pemohon'        => '6372024307020002',
            'alamat_pemohon'     => 'Jl. Kampung Baru Gg. Kurma RT 003 RW 002',
            'phone_pemohon'      => '085600200913',
            'keperluan'          => 'Persyaratan administrasi',
            'status'             => $status,
            'nomor_surat'        => '001/SKTM/LU/X/2026',
            'tanggal_surat'      => now(),
            'data_permohonan'    => [],
        ]);
    }

    /** @test */
    public function upload_surat_ttd_menjadwalkan_survey_satu_jam_kemudian(): void
    {
        Storage::fake('local');
        Notification::fake();
        Role::firstOrCreate(['name' => 'super_admin']);

        $permohonan = $this->makePermohonan();
        $admin = tap(User::factory()->create())->assignRole('super_admin');

        $this->actingAs($admin)
            ->post(route('admin.permohonan-surat.upload-signed', $permohonan->id), [
                'signed_letter' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('success');

        Notification::assertSentTo($permohonan, PermohonanApprovedWhatsapp::class);
        Notification::assertSentTo($permohonan, PermohonanSurveyWhatsapp::class, function ($notification) {
            $delayMenit = now()->diffInMinutes($notification->delay);

            return $delayMenit >= 59 && $delayMenit <= 60;
        });
    }

    /** @test */
    public function pesan_survey_memuat_nama_pemohon_dan_link_kuesioner(): void
    {
        config(['services.whatsapp.survey_url' => 'https://forms.example/survei']);
        $permohonan = $this->makePermohonan('completed');

        $pesan = (new PermohonanSurveyWhatsapp($permohonan))->toWhatsApp($permohonan);

        $this->assertStringStartsWith('Yth. Sdr. Henny', $pesan);
        $this->assertStringContainsString('https://forms.example/survei', $pesan);
    }

    /** @test */
    public function survey_tidak_dikirim_jika_surat_sudah_tidak_completed(): void
    {
        $permohonan = $this->makePermohonan('completed');
        $notification = new PermohonanSurveyWhatsapp($permohonan);

        // Revisi mengembalikan permohonan ke pending (PermohonanSuratService).
        $permohonan->update(['status' => 'pending']);

        $this->assertFalse($notification->shouldSend($permohonan, 'whatsapp'));
    }

    /** @test */
    public function survey_tidak_dikirim_dua_kali(): void
    {
        $permohonan = $this->makePermohonan('completed');
        $notification = new PermohonanSurveyWhatsapp($permohonan);

        $this->assertTrue($notification->shouldSend($permohonan, 'whatsapp'));

        WhatsappNotificationLog::create([
            'permohonan_id'     => $permohonan->id,
            'notification_type' => 'survey',
            'notifiable_type'   => PermohonanSurat::class,
            'notifiable_id'     => $permohonan->id,
            'phone_to'          => $permohonan->phone_pemohon,
            'status'            => 'sent',
        ]);

        $this->assertFalse($notification->shouldSend($permohonan, 'whatsapp'));
    }
}
