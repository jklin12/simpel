<?php

namespace App\Repositories;

use App\Models\ApprovalFlow;
use App\Repositories\Contracts\ApprovalFlowRepositoryInterface;

class ApprovalFlowRepository implements ApprovalFlowRepositoryInterface
{
    protected $model;

    public function __construct(ApprovalFlow $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with(['jenisSurat', 'kelurahan'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['jenisSurat', 'kelurahan', 'steps'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $approvalFlow = $this->find($id);
        $approvalFlow->update($data);
        return $approvalFlow->fresh(['jenisSurat', 'kelurahan']);
    }

    public function delete($id)
    {
        $approvalFlow = $this->find($id);
        return $approvalFlow->delete();
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->with(['jenisSurat', 'kelurahan']);

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

        // Status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)
            ->with(['jenisSurat', 'kelurahan'])
            ->get();
    }

    public function findByJenisSuratAndKelurahan($jenisSuratId, $kelurahanId)
    {
        return $this->model->where('jenis_surat_id', $jenisSuratId)
            ->where('kelurahan_id', $kelurahanId)
            ->first();
    }

    public function checkDuplicate($jenisSuratId, $kelurahanId, $excludeId = null)
    {
        $query = $this->model->where('jenis_surat_id', $jenisSuratId)
            ->where('kelurahan_id', $kelurahanId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
