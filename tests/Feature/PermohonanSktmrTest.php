<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use App\Models\PermohonanDokumen;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PermohonanSktmrTest extends TestCase
{
    use DatabaseTransactions;

    private JenisSurat $jenisSurat;
    private Kelurahan $kelurahan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->jenisSurat = JenisSurat::where('kode', 'SKTMR')->firstOrFail();
        $this->kelurahan  = Kelurahan::whereHas('kecamatan', fn ($q) => $q->where('id', 6372010))
            ->firstOrFail();
    }

    /** @test */
    public function submit_sktmr_berhasil_dengan_semua_field_valid(): void
    {
        $payload = array_merge($this->commonPayload(), $this->sktmrPayload(), $this->filePayload());

        $response = $this->post(route('permohonan.store.public'), $payload);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success_application');

        // Permohonan tersimpan di DB
        $this->assertDatabaseHas('permohonan_surats', [
            'jenis_surat_id' => $this->jenisSurat->id,
            'kelurahan_id'   => $this->kelurahan->id,
            'nik_pemohon'    => '3374010101900001',
            'phone_pemohon'  => '085600200913',
            'status'         => 'pending',
        ]);

        // Dokumen tersimpan
        $permohonan = PermohonanSurat::where('nik_pemohon', '3374010101900001')
            ->where('jenis_surat_id', $this->jenisSurat->id)
            ->latest()
            ->first();

        $this->assertNotNull($permohonan);
        $this->assertCount(5, $permohonan->dokumens);
    }

    /** @test */
    public function submit_sktmr_gagal_tanpa_file_wajib(): void
    {
        $payload = array_merge($this->commonPayload(), $this->sktmrPayload());
        // Tidak menyertakan file sama sekali

        $response = $this->post(route('permohonan.store.public'), $payload);

        $response->assertSessionHasErrors([
            'sktmr_surat_pengantar',
            'sktmr_blangko_pernyataan',
            'sktmr_ktp_kk',
            'sktmr_ktp_saksi',
            'sktmr_bukti_pbb',
        ]);
    }

    /** @test */
    public function submit_sktmr_gagal_tanpa_data_diri(): void
    {
        $payload = array_merge($this->commonPayload(), $this->filePayload(), [
            // nama_lengkap, nik_bersangkutan, dll sengaja tidak diisi
        ]);

        $response = $this->post(route('permohonan.store.public'), $payload);

        $response->assertSessionHasErrors([
            'nama_lengkap',
            'nik_bersangkutan',
            'jenis_kelamin',
        ]);
    }

    /** @test */
    public function submit_sktmr_gagal_nik_tidak_16_digit(): void
    {
        $payload = array_merge(
            $this->commonPayload(),
            $this->sktmrPayload(),
            $this->filePayload(),
            ['nik_bersangkutan' => '123'] // NIK tidak valid
        );

        $response = $this->post(route('permohonan.store.public'), $payload);

        $response->assertSessionHasErrors('nik_bersangkutan');
    }

    /** @test */
    public function file_pdf_tersimpan_di_storage(): void
    {
        $payload = array_merge($this->commonPayload(), $this->sktmrPayload(), $this->filePayload());

        $this->post(route('permohonan.store.public'), $payload);

        $permohonan = PermohonanSurat::where('nik_pemohon', '3374010101900001')
            ->where('jenis_surat_id', $this->jenisSurat->id)
            ->latest()
            ->first();

        $this->assertNotNull($permohonan);

        foreach ($permohonan->dokumens as $dokumen) {
            Storage::disk('public')->assertExists($dokumen->file_path);
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function commonPayload(): array
    {
        return [
            'jenis_surat_id' => $this->jenisSurat->id,
            'kelurahan_id'   => $this->kelurahan->id,
            'pemohon_nama'   => 'Ahmad Saputra',
            'pemohon_nik'    => '3374010101900001',
            'pemohon_phone'  => '085600200913',
            'pemohon_alamat' => 'Jl. Landasan Ulin No. 10, Banjarbaru',
        ];
    }

    private function sktmrPayload(): array
    {
        return [
            // Data Diri
            'nama_lengkap'             => 'Ahmad Saputra',
            'nik_bersangkutan'         => '3374010101900001',
            'jenis_kelamin'            => 'Laki-laki',
            'agama'                    => 'Islam',
            'tempat_lahir'             => 'Banjarbaru',
            'tanggal_lahir'            => '1990-01-01',
            'status_perkawinan'        => 'Kawin',
            'pekerjaan'                => 'Wiraswasta',
            'pendidikan_terakhir'      => 'SMA',
            'alamat_lengkap'           => 'Jl. Landasan Ulin No. 10',
            'keperluan'                => 'Pengajuan KPR Subsidi',

            // Surat Pengantar RT/RW
            'rt'                       => '001',
            'rw'                       => '002',
            'no_surat_pengantar'       => '001/RT001/II/2026',
            'tanggal_surat_pengantar'  => '2026-02-01',

            // Surat Pernyataan
            'no_surat_pernyataan'      => '002/SP/II/2026',
            'tanggal_surat_pernyataan' => '2026-02-01',
        ];
    }

    private function filePayload(): array
    {
        return [
            'sktmr_surat_pengantar'    => UploadedFile::fake()->create('surat_pengantar.pdf', 200, 'application/pdf'),
            'sktmr_blangko_pernyataan' => UploadedFile::fake()->create('blangko_pernyataan.pdf', 200, 'application/pdf'),
            'sktmr_ktp_kk'             => UploadedFile::fake()->image('ktp_kk.jpg', 800, 600),
            'sktmr_ktp_saksi'          => UploadedFile::fake()->image('ktp_saksi.jpg', 800, 600),
            'sktmr_bukti_pbb'          => UploadedFile::fake()->create('bukti_pbb.pdf', 150, 'application/pdf'),
        ];
    }
}
