<?php

namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\PermohonanKelurahanLog;
use App\Models\PermohonanSurat;
use App\Models\User;
use App\Notifications\PermohonanPindahWilayahNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PindahWilayahTest extends TestCase
{
    use DatabaseTransactions;

    private Kelurahan $asal;
    private Kelurahan $sameKecamatan;
    private JenisSurat $jenisSurat;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'admin_kelurahan', 'admin_kecamatan'] as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $this->jenisSurat = JenisSurat::where('kode', 'SKTMR')->firstOrFail();
        $this->asal = Kelurahan::whereHas('kecamatan', fn ($q) => $q->where('id', 6372010))->firstOrFail();
        $this->sameKecamatan = Kelurahan::where('kecamatan_id', $this->asal->kecamatan_id)
            ->where('id', '!=', $this->asal->id)
            ->firstOrFail();
    }

    private function makePermohonan(string $status = 'pending'): PermohonanSurat
    {
        $creator = User::factory()->create();

        return PermohonanSurat::create([
            'nomor_permohonan'   => 'REG/20261005/' . strtoupper(bin2hex(random_bytes(3))),
            'created_by_user_id' => $creator->id,
            'jenis_surat_id'     => $this->jenisSurat->id,
            'kelurahan_id'       => $this->asal->id,
            'nama_pemohon'       => 'Budi Santoso',
            'nik_pemohon'        => '3374010101900001',
            'alamat_pemohon'     => 'Jl. Merdeka No. 10 RT 02 RW 03',
            'phone_pemohon'      => '085600200913',
            'keperluan'          => 'Pengurusan bantuan',
            'status'             => $status,
            'data_permohonan'    => ['pekerjaan' => 'Buruh'],
        ]);
    }

    private function adminKelurahan(Kelurahan $kelurahan): User
    {
        $user = User::factory()->create(['kelurahan_id' => $kelurahan->id, 'kecamatan_id' => $kelurahan->kecamatan_id]);
        $user->assignRole('admin_kelurahan');

        return $user;
    }

    /** @test */
    public function admin_kelurahan_bisa_pindahkan_ke_kelurahan_lain_di_kecamatan_sama(): void
    {
        Notification::fake();
        $admin = $this->adminKelurahan($this->asal);
        $permohonan = $this->makePermohonan();

        $this->actingAs($admin)
            ->post(route('admin.permohonan-surat.pindah-wilayah', $permohonan->id), [
                'kelurahan_id' => $this->sameKecamatan->id,
                'alasan'       => 'Domisili sesuai KTP',
            ])
            ->assertRedirect(route('admin.permohonan-surat.show', $permohonan->id))
            ->assertSessionHas('success');

        $this->assertSame($this->sameKecamatan->id, $permohonan->fresh()->kelurahan_id);
        $this->assertDatabaseHas('permohonan_kelurahan_logs', [
            'permohonan_surat_id' => $permohonan->id,
            'from_kelurahan_id'   => $this->asal->id,
            'to_kelurahan_id'     => $this->sameKecamatan->id,
            'moved_by'            => $admin->id,
            'alasan'              => 'Domisili sesuai KTP',
        ]);
    }

    /** @test */
    public function admin_kelurahan_bisa_pindahkan_lintas_kecamatan(): void
    {
        Notification::fake();
        $kabupaten = Kabupaten::findOrFail($this->asal->kecamatan->kabupaten_id);
        $kecamatanLain = Kecamatan::create(['kabupaten_id' => $kabupaten->id, 'nama' => 'Kecamatan Uji', 'kode' => 'UJI-' . random_int(1000, 9999), 'is_active' => true]);
        $kelurahanLain = Kelurahan::create(['kecamatan_id' => $kecamatanLain->id, 'nama' => 'Kelurahan Uji', 'kode' => 'KLU-' . random_int(1000, 9999), 'is_active' => true]);

        $admin = $this->adminKelurahan($this->asal);
        $permohonan = $this->makePermohonan();

        $this->actingAs($admin);
        $this->service()->pindahkanWilayah($permohonan, $kelurahanLain->id, 'Salah kecamatan');

        $this->assertSame($kelurahanLain->id, $permohonan->fresh()->kelurahan_id);
    }

    /** @test */
    public function notifikasi_hanya_ke_admin_bukan_pemohon(): void
    {
        Notification::fake();
        $admin = $this->adminKelurahan($this->asal);
        $tujuanAdmin = $this->adminKelurahan($this->sameKecamatan);
        $permohonan = $this->makePermohonan();

        $this->actingAs($admin);
        $this->service()->pindahkanWilayah($permohonan, $this->sameKecamatan->id, 'Salah pilih');

        Notification::assertSentTo($tujuanAdmin, PermohonanPindahWilayahNotification::class);
        Notification::assertNotSentTo($admin, PermohonanPindahWilayahNotification::class);
        Notification::assertNotSentTo($permohonan->createdBy, PermohonanPindahWilayahNotification::class);
    }

    /** @test */
    public function ditolak_jika_status_sudah_approved(): void
    {
        Notification::fake();
        $admin = $this->adminKelurahan($this->asal);
        $permohonan = $this->makePermohonan('approved');

        $this->actingAs($admin);

        $this->expectException(\RuntimeException::class);
        $this->service()->pindahkanWilayah($permohonan, $this->sameKecamatan->id, 'Terlambat');
    }

    /** @test */
    public function ditolak_jika_tujuan_sama_dengan_asal(): void
    {
        Notification::fake();
        $admin = $this->adminKelurahan($this->asal);
        $permohonan = $this->makePermohonan();

        $this->actingAs($admin);

        $this->expectException(\RuntimeException::class);
        $this->service()->pindahkanWilayah($permohonan, $this->asal->id, 'Sama');
    }

    /** @test */
    public function ditolak_jika_kelurahan_tujuan_nonaktif(): void
    {
        Notification::fake();
        $admin = $this->adminKelurahan($this->asal);
        $permohonan = $this->makePermohonan();
        $this->sameKecamatan->update(['is_active' => false]);

        $this->actingAs($admin);

        $this->expectException(\RuntimeException::class);
        $this->service()->pindahkanWilayah($permohonan, $this->sameKecamatan->id, 'Nonaktif');
    }

    /** @test */
    public function admin_kelurahan_lain_tidak_bisa_pindahkan_permohonan_wilayah_orang(): void
    {
        Notification::fake();
        $adminLain = $this->adminKelurahan($this->sameKecamatan);
        $permohonan = $this->makePermohonan();

        $this->actingAs($adminLain)
            ->post(route('admin.permohonan-surat.pindah-wilayah', $permohonan->id), [
                'kelurahan_id' => $this->sameKecamatan->id,
                'alasan'       => 'Bukan wilayah saya',
            ])
            ->assertSessionHas('error');

        $this->assertSame($this->asal->id, $permohonan->fresh()->kelurahan_id);
        $this->assertSame(0, PermohonanKelurahanLog::where('permohonan_surat_id', $permohonan->id)->count());
    }

    private function service(): \App\Services\PermohonanSuratService
    {
        return app(\App\Services\PermohonanSuratService::class);
    }
}
