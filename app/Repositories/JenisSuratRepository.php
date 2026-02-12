<?php

namespace App\Repositories;

use App\Models\JenisSurat;
use App\Repositories\Contracts\JenisSuratRepositoryInterface;

class JenisSuratRepository implements JenisSuratRepositoryInterface
{
    protected $model;

    public function __construct(JenisSurat $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->orderBy('nama', 'asc')->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $jenisSurat = $this->find($id);
        $jenisSurat->update($data);
        return $jenisSurat->fresh();
    }

    public function delete($id)
    {
        $jenisSurat = $this->find($id);
        return $jenisSurat->delete();
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->query();

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'nama';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)
            ->orderBy('nama', 'asc')
            ->get();
    }

    public function findByKode($kode)
    {
        return $this->model->where('kode', $kode)->first();
    }

    public function getWithApprovalFlows()
    {
        return $this->model->with('approvalFlows')
            ->orderBy('nama', 'asc')
            ->get();
    }
}
