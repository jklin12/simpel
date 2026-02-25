<?php

namespace App\Repositories;

use App\Models\PortalStrukturOrganisasi;
use App\Repositories\Contracts\PortalStrukturOrganisasiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PortalStrukturOrganisasiRepository implements PortalStrukturOrganisasiRepositoryInterface
{
    protected $model;

    public function __construct(PortalStrukturOrganisasi $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with('parent')->orderBy('urutan')->get();
    }

    public function find($id)
    {
        return $this->model->with('parent')->findOrFail($id);
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
        return $item->delete(); // This will trigger cascade delete in Model's boot method
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->with('parent');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('jabatan', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['parent_id'])) {
            // khusus mencari berdasarkan atasan
            $query->where('parent_id', $filters['parent_id']);
        }

        $sortBy    = $filters['sort_by'] ?? 'urutan';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        // kalau urutan sama, sorting fallback by jabatan
        $query->orderBy($sortBy, $sortOrder)->orderBy('jabatan', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Mengambil hirarki penuh. Hanya ambil level paling atas (parent_id null)
     * lalu eager load children secara recursive.
     */
    public function getTree(): Collection
    {
        // Untuk Eager Loading tak terhingga/mendalam, kita load children.children.children
        // Sebagai ganti pendekatan manual recursive mapping, kita bisa load relasi dinamis.
        // Asumsi organisasi tidak sedalam puluhan level:
        return $this->model->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->with(['children' => function ($q2) {
                    $q2->with('children'); // Load up to 4 levels for now. Can be expanded statically or recursively mapped.
                }]);
            }])
            ->orderBy('urutan')
            ->get();
    }
}
