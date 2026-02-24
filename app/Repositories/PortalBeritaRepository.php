<?php

namespace App\Repositories;

use App\Models\PortalBerita;
use App\Repositories\Contracts\PortalBeritaRepositoryInterface;

class PortalBeritaRepository implements PortalBeritaRepositoryInterface
{
    protected $model;

    public function __construct(PortalBerita $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
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

        $sortBy    = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getPublished($perPage = 6)
    {
        return $this->model->published()->paginate($perPage);
    }
}
