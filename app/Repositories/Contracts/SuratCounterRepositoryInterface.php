<?php

namespace App\Repositories\Contracts;

interface SuratCounterRepositoryInterface
{
    public function all();
    public function find($id);
    public function paginate($perPage = 15, array $filters = []);
    public function resetCounter($id);
    public function getByJenisSuratAndKelurahan($jenisSuratId, $kelurahanId, $tahun, $bulan);
}
