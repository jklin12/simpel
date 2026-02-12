<?php

namespace App\Repositories;

use App\Models\SuratCounter;
use App\Repositories\Contracts\SuratCounterRepositoryInterface;

class SuratCounterRepository implements SuratCounterRepositoryInterface
{
    protected $model;

    public function __construct(SuratCounter $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with(['jenisSurat', 'kelurahan'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['jenisSurat', 'kelurahan'])->findOrFail($id);
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->with(['jenisSurat', 'kelurahan.kecamatan']);

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('jenisSurat', function ($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%");
                })
                    ->orWhereHas('kelurahan', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Jenis Surat filter
        if (isset($filters['jenis_surat_id']) && !empty($filters['jenis_surat_id'])) {
            $query->where('jenis_surat_id', $filters['jenis_surat_id']);
        }

        // Sorting
        $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        return $query->paginate($perPage);
    }

    public function resetCounter($id)
    {
        $counter = $this->find($id);
        $counter->update(['current_number' => 0]);
        return $counter->fresh();
    }

    public function getByJenisSuratAndKelurahan($jenisSuratId, $kelurahanId, $tahun, $bulan)
    {
        return $this->model->where('jenis_surat_id', $jenisSuratId)
            ->where('kelurahan_id', $kelurahanId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();
    }
}
