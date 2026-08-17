<?php

namespace Tests\Feature;

use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use App\Models\User;
use App\Support\Pii;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PiiRevealTest extends TestCase
{
    use DatabaseTransactions;

    private Kelurahan $kelurahan;
    private JenisSurat $jenisSurat;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'admin_kelurahan'] as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $this->jenisSurat = JenisSurat::where('kode', 'SKTMR')->firstOrFail();
        $this->kelurahan  = Kelurahan::whereHas('kecamatan', fn ($q) => $q->where('id', 6372010))
            ->firstOrFail();
    }

    private function makePermohonan(): PermohonanSurat
    {
        $creator = User::factory()->create();

        return PermohonanSurat::create([
            'nomor_permohonan'   => 'REG/20260817/' . strtoupper(bin2hex(random_bytes(3))),
            'created_by_user_id' => $creator->id,
            'jenis_surat_id'     => $this->jenisSurat->id,
            'kelurahan_id'       => $this->kelurahan->id,
            'nama_pemohon'       => 'Budi Santoso',
            'nik_pemohon'        => '3374010101900001',
            'alamat_pemohon'     => 'Jl. Merdeka No. 10 RT 02 RW 03',
            'phone_pemohon'      => '085600200913',
            'keperluan'          => 'Pengurusan bantuan',
            'status'             => 'pending',
            'data_permohonan'    => [
                'nik_jenazah'  => '3374010101800002',
                'pekerjaan'    => 'Buruh',
            ],
        ]);
    }

    private function superAdmin(): User
    {
        return tap(User::factory()->create())->assignRole('super_admin');
    }

    private function adminKelurahan(): User
    {
        $user = User::factory()->create(['kelurahan_id' => $this->kelurahan->id]);
        $user->assignRole('admin_kelurahan');

        return $user;
    }

    /** @test */
    public function masking_util_menghasilkan_pola_tersamar_yang_benar(): void
    {
        $this->assertSame('3374••••••••0001', Pii::maskNik('3374010101900001'));
        $this->assertStringContainsString(Pii::BULLET, Pii::maskPhone('085600200913'));
        $this->assertStringStartsWith('0856', Pii::maskPhone('085600200913'));
        $this->assertStringEndsWith('913', Pii::maskPhone('085600200913'));
        $this->assertTrue(Pii::isPiiKey('nik_jenazah'));
        $this->assertTrue(Pii::isPiiKey('alamat_pasangan'));
        $this->assertFalse(Pii::isPiiKey('pekerjaan'));
        $this->assertSame('nik', Pii::inferType('gaib_nik'));
        $this->assertSame('generic', Pii::inferType('pekerjaan'));
    }

    /** @test */
    public function super_admin_bisa_reveal_kolom_nik_dan_tercatat_di_audit_log(): void
    {
        $permohonan = $this->makePermohonan();

        $response = $this->actingAs($this->superAdmin())->postJson(route('admin.pii.reveal'), [
            'source' => 'permohonan',
            'id'     => $permohonan->id,
            'field'  => 'nik_pemohon',
        ]);

        $response->assertOk();
        $response->assertJson(['value' => '3374010101900001']);

        $this->assertDatabaseHas('pii_access_logs', [
            'source'     => 'permohonan',
            'subject_id' => $permohonan->id,
            'field'      => 'nik_pemohon',
        ]);
    }

    /** @test */
    public function super_admin_bisa_reveal_pii_di_data_permohonan(): void
    {
        $permohonan = $this->makePermohonan();

        $response = $this->actingAs($this->superAdmin())->postJson(route('admin.pii.reveal'), [
            'source' => 'permohonan',
            'id'     => $permohonan->id,
            'field'  => 'nik_jenazah',
        ]);

        $response->assertOk();
        $response->assertJson(['value' => '3374010101800002']);
    }

    /** @test */
    public function non_super_admin_dilarang_reveal(): void
    {
        $permohonan = $this->makePermohonan();

        $response = $this->actingAs($this->adminKelurahan())->postJson(route('admin.pii.reveal'), [
            'source' => 'permohonan',
            'id'     => $permohonan->id,
            'field'  => 'nik_pemohon',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('pii_access_logs', [
            'subject_id' => $permohonan->id,
        ]);
    }

    /** @test */
    public function field_di_luar_whitelist_ditolak(): void
    {
        $permohonan = $this->makePermohonan();

        // Kolom non-PII (bukan whitelist kolom, bukan PII key di data_permohonan).
        $response = $this->actingAs($this->superAdmin())->postJson(route('admin.pii.reveal'), [
            'source' => 'permohonan',
            'id'     => $permohonan->id,
            'field'  => 'pekerjaan',
        ]);

        $response->assertStatus(422);
    }
}
