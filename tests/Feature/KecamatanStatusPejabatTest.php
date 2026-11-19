<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KecamatanStatusPejabatTest extends TestCase
{
    use DatabaseTransactions;

    public function test_signer_jabatan_dan_title_mengikuti_status(): void
    {
        $kec = new Kecamatan(['nama' => 'LANDASAN ULIN']);

        $kec->status_pejabat = 'Definitif';
        $this->assertSame('Camat', $kec->signer_jabatan);
        $this->assertSame('Camat Landasan Ulin', $kec->signer_title);

        $kec->status_pejabat = 'Plt';
        $this->assertSame('Plt. Camat', $kec->signer_jabatan);
        $this->assertSame('Plt. Camat Landasan Ulin', $kec->signer_title);

        $kec->status_pejabat = 'Plh';
        $this->assertSame('Plh. Camat', $kec->signer_jabatan);
    }
}
