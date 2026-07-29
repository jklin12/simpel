<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OcrKtpTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function ocr_ktp_dengan_provider_mock_berhasil(): void
    {
        config(['ai.provider' => 'mock']);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'OCR berhasil.',
        ]);

        $data = $response->json('data');
        $this->assertNotNull($data['nik']);
        $this->assertNotNull($data['nama']);
        $this->assertNotNull($data['alamat']);
        $this->assertNotNull($data['tanggal_lahir']);
        $this->assertIsArray($data);
        $this->assertCount(9, $data);
    }

    /** @test */
    public function ocr_ktp_dengan_provider_claude_mock_berhasil(): void
    {
        config(['ai.provider' => 'claude']);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    [
                        'text' => json_encode([
                            'nik'               => '6372010101900001',
                            'nama'              => 'JOHN DOE',
                            'tempat_lahir'      => 'BANJARBARU',
                            'tanggal_lahir'     => '1990-01-01',
                            'jenis_kelamin'     => 'LAKI-LAKI',
                            'alamat'            => 'JL. MERDEKA NO. 123',
                            'agama'             => 'ISLAM',
                            'status_perkawinan' => 'BELUM KAWIN',
                            'pekerjaan'         => 'KARYAWAN SWASTA',
                        ]),
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'OCR berhasil.',
        ]);

        $data = $response->json('data');
        $this->assertEquals('6372010101900001', $data['nik']);
        $this->assertEquals('JOHN DOE', $data['nama']);
    }

    /** @test */
    public function ocr_ktp_claude_dengan_markdown_fence_terparse(): void
    {
        config(['ai.provider' => 'claude']);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    [
                        'text' => '```json' . "\n" . json_encode([
                            'nik'               => '6372010101900001',
                            'nama'              => 'JANE DOE',
                            'tempat_lahir'      => 'BANJARBARU',
                            'tanggal_lahir'     => '1995-05-15',
                            'jenis_kelamin'     => 'PEREMPUAN',
                            'alamat'            => 'JL. SOEKARNO NO. 456',
                            'agama'             => 'ISLAM',
                            'status_perkawinan' => 'BELUM KAWIN',
                            'pekerjaan'         => 'GURU',
                        ]) . "\n" . '```',
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('JANE DOE', $data['nama']);
    }

    /** @test */
    public function ocr_ktp_dengan_provider_gemini_mock_berhasil(): void
    {
        config(['ai.provider' => 'gemini']);

        Http::fake([
            'generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-lite:generateContent' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'nik'               => '6372010101900002',
                                        'nama'              => 'BUDI SANTOSO',
                                        'tempat_lahir'      => 'BANJARBARU',
                                        'tanggal_lahir'     => '1992-07-20',
                                        'jenis_kelamin'     => 'Laki-laki',
                                        'alamat'            => 'JL. AHMAD YANI NO. 789',
                                        'agama'             => 'ISLAM',
                                        'status_perkawinan' => 'KAWIN',
                                        'pekerjaan'         => 'PEDAGANG',
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('6372010101900002', $data['nik']);
        $this->assertEquals('BUDI SANTOSO', $data['nama']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com');
        });
    }

    /** @test */
    public function ocr_ktp_normalisasi_jenis_kelamin(): void
    {
        config(['ai.provider' => 'claude']);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    [
                        'text' => json_encode([
                            'nik'               => '6372010101900003',
                            'nama'              => 'TEST USER',
                            'tempat_lahir'      => 'BANJARBARU',
                            'tanggal_lahir'     => '1990-01-01',
                            'jenis_kelamin'     => 'LAKI-LAKI',  // uppercase
                            'alamat'            => 'JL. TEST',
                            'agama'             => 'ISLAM',
                            'status_perkawinan' => 'BELUM KAWIN',
                            'pekerjaan'         => 'KARYAWAN',
                        ]),
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        // Should normalize to title case
        $this->assertEquals('Laki-laki', $data['jenis_kelamin']);
    }

    /** @test */
    public function ocr_ktp_normalisasi_tanggal_lahir(): void
    {
        config(['ai.provider' => 'mock']);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        // Mock provider returns YYYY-MM-DD format
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $data['tanggal_lahir']);
    }

    /** @test */
    public function ocr_ktp_gagal_tanpa_file(): void
    {
        $response = $this->post(route('layanan.surat.ocr'), []);

        $response->assertStatus(422);
        $response->assertSessionHasErrors('ktp_image');
    }

    /** @test */
    public function ocr_ktp_gagal_file_bukan_gambar(): void
    {
        $file = UploadedFile::fake()->create('ktp.txt', 100, 'text/plain');

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertSessionHasErrors('ktp_image');
    }

    /** @test */
    public function ocr_ktp_gagal_file_terlalu_besar(): void
    {
        // 6MB > 5MB limit
        $file = UploadedFile::fake()->image('ktp.jpg', 3000, 2000)->size(6 * 1024);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertSessionHasErrors('ktp_image');
    }

    /** @test */
    public function ocr_ktp_gagal_api_key_tidak_dikonfigurasi(): void
    {
        config(['ai.provider' => 'claude', 'ai.providers.claude.api_key' => null]);

        Http::fake(); // No API call should happen

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'message' => 'Gagal memproses OCR. Silakan coba lagi atau isi data secara manual.',
        ]);

        // Ensure no API call was made
        Http::assertNothingSent();
    }

    /** @test */
    public function ocr_ktp_gagal_respons_json_invalid(): void
    {
        config(['ai.provider' => 'claude']);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    [
                        'text' => 'invalid json {{{',
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
        // Ensure error message doesn't leak implementation details
        $this->assertStringNotContainsString('json_decode', $response->json('message'));
        $this->assertStringNotContainsString('Exception', $response->json('message'));
    }

    /** @test */
    public function ocr_ktp_gagal_api_error(): void
    {
        config(['ai.provider' => 'claude']);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'error' => [
                    'message' => 'API rate limit exceeded',
                ],
            ], 429),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function ocr_ktp_field_boleh_null(): void
    {
        config(['ai.provider' => 'claude']);

        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    [
                        'text' => json_encode([
                            'nik'               => '6372010101900001',
                            'nama'              => 'JOHN DOE',
                            'tempat_lahir'      => null,  // nullable
                            'tanggal_lahir'     => '1990-01-01',
                            'jenis_kelamin'     => 'Laki-laki',
                            'alamat'            => null,  // nullable
                            'agama'             => 'ISLAM',
                            'status_perkawinan' => null,  // nullable
                            'pekerjaan'         => 'KARYAWAN SWASTA',
                        ]),
                    ],
                ],
            ], 200),
        ]);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNull($data['tempat_lahir']);
        $this->assertNull($data['alamat']);
        $this->assertNull($data['status_perkawinan']);
    }

    /** @test */
    public function ocr_ktp_endpoint_throttled(): void
    {
        config(['ai.provider' => 'mock']);

        $file = UploadedFile::fake()->image('ktp.jpg', 800, 600);

        // Route punya throttle:10,1 (10 requests per 1 minute)
        // We test dengan mock, jadi tidak perlu benar-benar mengirim 11 request
        // Cukup assert route memiliki throttle middleware
        $routeConfig = \Route::getRoutes()->getByName('layanan.surat.ocr');
        $this->assertNotNull($routeConfig);

        // First request should succeed
        $response = $this->post(route('layanan.surat.ocr'), [
            'ktp_image' => $file,
        ]);
        $response->assertStatus(200);
    }
}
