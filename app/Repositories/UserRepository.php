<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with(['roles', 'kabupaten', 'kecamatan', 'kelurahan'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['roles', 'kabupaten', 'kecamatan', 'kelurahan'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user->fresh(['roles', 'kabupaten', 'kecamatan', 'kelurahan']);
    }

    public function delete($id)
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->with(['roles', 'kabupaten', 'kecamatan', 'kelurahan']);

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if (isset($filters['role']) && !empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Location filters
        if (isset($filters['kabupaten_id']) && !empty($filters['kabupaten_id'])) {
            $query->where('kabupaten_id', $filters['kabupaten_id']);
        }

        if (isset($filters['kecamatan_id']) && !empty($filters['kecamatan_id'])) {
            $query->where('kecamatan_id', $filters['kecamatan_id']);
        }

        if (isset($filters['kelurahan_id']) && !empty($filters['kelurahan_id'])) {
            $query->where('kelurahan_id', $filters['kelurahan_id']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getUsersWithRoles()
    {
        return $this->model->with('roles')->get();
    }
}
