<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SpamPreventionSktmTest extends TestCase
{
    use DatabaseTransactions;

    private JenisSurat $jenisSurat;
    private Kelurahan $kelurahan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->jenisSurat = JenisSurat::where('kode', 'SKTM')->firstOrFail();
        $this->kelurahan  = Kelurahan::whereHas('kecamatan', fn ($q) => $q->where('id', 6372010))
            ->firstOrFail();
    }

    /** @test */
    public function honeypot_terisi_dibalas_sukses_palsu_dan_tidak_menyimpan(): void
    {
        $payload = array_merge($this->validPayload(), ['website' => 'http://spam.example']);

        $response = $this->post(route('layanan.surat.store'), $payload);

        $response->assertRedirect(route('layanan.index'));
        $response->assertSessionHas('success');
        $response->assertSessionMissing('success_application');
        $this->assertSame(0, PermohonanSurat::where('nik_pemohon', $this->validNik())->count());
    }

    /** @test */
    public function nik_diawali_0000_ditolak(): void
    {
        $payload = array_merge($this->validPayload(), ['nik_bersangkutan' => '0000011010900001']);

        $this->post(route('layanan.surat.store'), $payload)
            ->assertSessionHasErrors('nik_bersangkutan');
    }

    /** @test */
    public function nik_bukan_kode_kabupaten_6372_ditolak(): void
    {
        $payload = array_merge($this->validPayload(), ['nik_bersangkutan' => '3374011010900001']);

        $this->post(route('layanan.surat.store'), $payload)
            ->assertSessionHasErrors('nik_bersangkutan');
    }

    /** @test */
    public function nama_berisi_tanda_tanya_ditolak(): void
    {
        $payload = array_merge($this->validPayload(), ['nama_lengkap' => '??????']);

        $this->post(route('layanan.surat.store'), $payload)
            ->assertSessionHasErrors('nama_lengkap');
    }

    /** @test */
    public function no_wa_tidak_valid_ditolak(): void
    {
        $payload = array_merge($this->validPayload(), ['no_wa' => '123']);

        $this->post(route('layanan.surat.store'), $payload)
            ->assertSessionHasErrors('no_wa');
    }

    /** @test */
    public function no_wa_dengan_spasi_dan_tanda_hubung_dinormalisasi(): void
    {
        $payload = array_merge($this->validPayload(), ['no_wa' => '0812-3456 7890']);

        $this->post(route('layanan.surat.store'), $payload)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('permohonan_surats', [
            'nik_pemohon'   => $this->validNik(),
            'phone_pemohon' => '081234567890',
        ]);
    }

    /** @test */
    public function satu_no_wa_maksimal_tiga_permohonan_per_hari(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $nik = '63720110109' . str_pad((string) $i, 5, '0', STR_PAD_LEFT);
            $this->post(route('layanan.surat.store'), array_merge(
                $this->validPayload(),
                ['nik_bersangkutan' => $nik, 'no_wa' => '081200000001'],
            ))->assertSessionHasNoErrors();
        }

        $response = $this->post(route('layanan.surat.store'), array_merge(
            $this->validPayload(),
            ['nik_bersangkutan' => '6372011010900009', 'no_wa' => '081200000001'],
        ));

        $response->assertSessionHas('error');
        $this->assertSame(3, PermohonanSurat::where('phone_pemohon', '081200000001')->count());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validNik(): string
    {
        return '6372011010900001';
    }

    private function validPayload(): array
    {
        return [
            'jenis_surat_id'          => $this->jenisSurat->id,
            'kelurahan_id'            => $this->kelurahan->id,
            'nama_lengkap'            => 'Ahmad Saputra',
            'nik_bersangkutan'        => $this->validNik(),
            'jenis_kelamin'           => 'Laki-laki',
            'agama'                   => 'Islam',
            'tempat_lahir'            => 'Banjarbaru',
            'tanggal_lahir'           => '1990-01-01',
            'status_perkawinan'       => 'Kawin',
            'pekerjaan'               => 'Wiraswasta',
            'alamat_lengkap'          => 'Jl. Landasan Ulin No. 10',
            'no_wa'                   => '081234567890',
            'keperluan_sktm'          => 'Pengajuan KPR Subsidi',
            'keterangan_sktm'         => 'Untuk keperluan administrasi',
            'rt'                      => '001',
            'rw'                      => '002',
            'no_surat_pengantar'      => '001/RT001/II/2026',
            'tanggal_surat_pengantar' => '2026-02-01',
            'tanggal_surat_pernyataan' => '2026-02-01',
            'surat_pengantar_rtrw'      => UploadedFile::fake()->create('pengantar.pdf', 200, 'application/pdf'),
            'blangko_pernyataan'        => UploadedFile::fake()->create('blangko.pdf', 200, 'application/pdf'),
            'ktp_kk_bersangkutan'       => UploadedFile::fake()->image('ktp_kk.jpg', 800, 600),
            'ktp_saksi'                 => UploadedFile::fake()->image('ktp_saksi.jpg', 800, 600),
            'surat_rekomendasi_sekolah' => UploadedFile::fake()->create('rekomendasi.pdf', 200, 'application/pdf'),
            'bukti_lunas_pbb'           => UploadedFile::fake()->create('pbb.pdf', 150, 'application/pdf'),
        ];
    }
}
