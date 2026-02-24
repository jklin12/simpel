<?php

namespace App\Repositories;

use App\Models\PortalDataKelurahan;
use App\Repositories\Contracts\PortalDataKelurahanRepositoryInterface;

class PortalDataKelurahanRepository implements PortalDataKelurahanRepositoryInterface
{
    protected $model;

    public function __construct(PortalDataKelurahan $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('kelurahan')->orderBy('kategori')->get();
    }

    public function find($id)
    {
        return $this->model->with('kelurahan')->findOrFail($id);
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
        return $item->delete(); // Soft delete
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->with('kelurahan');

        if (!empty($filters['search'])) {
            $query->where('nama', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }

        if (!empty($filters['kelurahan_id'])) {
            $query->where('kelurahan_id', $filters['kelurahan_id']);
        }

        $sortBy    = $filters['sort_by'] ?? 'kategori';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getByKategori(string $kategori)
    {
        return $this->model->with('kelurahan')
            ->where('kategori', $kategori)
            ->orderBy('nama')
            ->get();
    }

    /**
     * Ambil semua data yang memiliki koordinat, dikelompokkan per kategori.
     * Digunakan untuk data JSON peta Leaflet.
     */
    public function getDataForMap()
    {
        return $this->model->with('kelurahan')
            ->hasKoordinat()
            ->orderBy('kategori')
            ->get()
            ->groupBy('kategori');
    }
}
