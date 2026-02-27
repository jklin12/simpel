<?php

namespace App\Repositories;

use App\Models\PortalSlider;
use App\Repositories\Contracts\PortalSliderRepositoryInterface;

class PortalSliderRepository implements PortalSliderRepositoryInterface
{
    protected $model;

    public function __construct(PortalSlider $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->orderBy('urutan')->get();
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
        $item = $this->find($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item->delete();
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->newQuery();

        if (!empty($filters['search'])) {
            $query->where('judul', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sortBy    = $filters['sort_by'] ?? 'urutan';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getAktif()
    {
        return $this->model->aktif()->get();
    }
}
