<?php

namespace App\Services;

use App\Repositories\Contracts\SuratCounterRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SuratCounterService
{
    protected $repository;

    public function __construct(SuratCounterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllSuratCounters()
    {
        return $this->repository->all();
    }

    public function getSuratCountersPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getSuratCounterById($id)
    {
        return $this->repository->find($id);
    }

    public function resetCounter($id)
    {
        try {
            $counter = $this->repository->resetCounter($id);

            Log::info('Surat Counter reset successfully', [
                'id' => $id,
                'reset_by' => auth()->id()
            ]);

            return $counter;
        } catch (\Exception $e) {
            Log::error('Failed to reset surat counter', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal reset counter: ' . $e->getMessage());
        }
    }
}
