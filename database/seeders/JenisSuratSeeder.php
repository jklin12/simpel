<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JenisSurat;

class JenisSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisSurats = [
            [
                'nama' => 'Surat Pengantar SKCK',
                'kode' => 'SKCK',
                'deskripsi' => 'Surat pengantar untuk keperluan penerbitan Surat Keterangan Catatan Kepolisian di Polsek/Polres.',
                'template_path' => 'templates/skck.docx',
                'required_fields' => json_encode(['ktp', 'kk', 'akse_kelahiran']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Domisili',
                'kode' => 'SKD',
                'deskripsi' => 'Surat keterangan yang menyatakan status tempat tinggal seseorang di wilayah kelurahan.',
                'template_path' => 'templates/domisili.docx',
                'required_fields' => json_encode(['ktp', 'kk', 'surat_pengantar_rt']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Usaha',
                'kode' => 'SKU',
                'deskripsi' => 'Surat keterangan untuk menerangkan bahwa orang tersebut memiliki usaha di wilayah kelurahan.',
                'template_path' => 'templates/sku.docx',
                'required_fields' => json_encode(['ktp', 'kk', 'foto_usaha']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Tidak Mampu',
                'kode' => 'SKTM',
                'deskripsi' => 'Surat keterangan untuk keperluan beasiswa, kesehatan, atau bantuan sosial lainnya.',
                'template_path' => 'templates/sktm.docx',
                'required_fields' => json_encode(['ktp', 'kk', 'surat_pengantar_rt', 'foto_rumah']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Belum Menikah',
                'kode' => 'SKBM',
                'deskripsi' => 'Surat pernyataan status belum menikah untuk keperluan administrasi pernikahan atau pekerjaan.',
                'template_path' => 'templates/skbm.docx',
                'required_fields' => json_encode(['ktp', 'kk', 'surat_pernyataan']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Kelahiran',
                'kode' => 'SKL',
                'deskripsi' => 'Surat keterangan peristiwa kelahiran baru untuk proses akta kelahiran.',
                'template_path' => 'templates/skl.docx',
                'required_fields' => json_encode(['ktp_ortu', 'kk', 'surat_bidan']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Kematian',
                'kode' => 'SKM',
                'deskripsi' => 'Surat keterangan peristiwa kematian untuk proses akta kematian.',
                'template_path' => 'templates/skm.docx',
                'required_fields' => json_encode(['ktp_alm', 'kk', 'surat_rs']),
                'is_active' => true,
            ],
            [
                'nama' => 'Surat Keterangan Domisili Usaha',
                'kode' => 'SKDU',
                'deskripsi' => 'Surat keterangan tempat kedudukan usaha untuk kelengkapan izin usaha.',
                'template_path' => 'templates/skdu.docx',
                'required_fields' => json_encode(['ktp', 'kk', 'akta_pendirian']),
                'is_active' => true,
            ],
        ];

        foreach ($jenisSurats as $surat) {
            JenisSurat::updateOrCreate(['kode' => $surat['kode']], $surat);
        }
    }
}
