<?php

namespace App\Repositories;

use App\Models\PortalFaq;
use App\Repositories\Contracts\PortalFaqRepositoryInterface;

class PortalFaqRepository implements PortalFaqRepositoryInterface
{
    protected $model;

    public function __construct(PortalFaq $model)
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
            $query->where('pertanyaan', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderBy('urutan');
        return $query->paginate($perPage);
    }

    public function getAktif()
    {
        return $this->model->aktif()->get();
    }

    public function getAktifByKategori()
    {
        return $this->model->aktif()
            ->get()
            ->groupBy('kategori');
    }
}
