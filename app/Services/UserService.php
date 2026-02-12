<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return $this->repository->all();
    }

    /**
     * Get paginated users with filters
     */
    public function getUsersPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data)
    {
        DB::beginTransaction();

        try {
            $data['kabupaten_id'] = '6372';

            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Extract role before creating user
            $role = $data['role'] ?? null;
            unset($data['role']);

            // Create user
            $user = $this->repository->create($data);

            // Assign role
            if ($role) {
                $user->assignRole($role);
            }

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $role,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return $user->fresh(['roles', 'kabupaten', 'kecamatan', 'kelurahan']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Gagal membuat user: ' . $e->getMessage());
        }
    }

    /**
     * Update user
     */
    public function updateUser($id, array $data)
    {
        DB::beginTransaction();

        try {
            $data['kabupaten_id'] = '6372';
            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Extract role
            $role = $data['role'] ?? null;
            unset($data['role']);

            // Update user
            $user = $this->repository->update($id, $data);

            // Update role if provided
            if ($role) {
                $user->syncRoles([$role]);
            }

            Log::info('User updated successfully', [
                'user_id' => $id,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        DB::beginTransaction();

        try {
            $user = $this->repository->find($id);

            // Business validation: Check if user has active permohonan
            // if ($user->permohonanSurats()->whereIn('status', ['pending', 'in_review'])->exists()) {
            //     throw new \Exception('Tidak dapat menghapus user yang memiliki permohonan aktif');
            // }

            $this->repository->delete($id);

            Log::info('User deleted successfully', [
                'user_id' => $id,
                'deleted_by' => auth()->id()
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal hapus user: ' . $e->getMessage());
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole($userId, $roleName)
    {
        try {
            $user = $this->repository->find($userId);
            $user->assignRole($roleName);

            Log::info('Role assigned to user', [
                'user_id' => $userId,
                'role' => $roleName
            ]);

            return $user->fresh('roles');
        } catch (\Exception $e) {
            Log::error('Failed to assign role', [
                'user_id' => $userId,
                'role' => $roleName,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal assign role: ' . $e->getMessage());
        }
    }

    /**
     * Update user location
     */
    public function updateUserLocation($userId, array $locationData)
    {
        try {
            $user = $this->repository->update($userId, $locationData);

            Log::info('User location updated', [
                'user_id' => $userId,
                'location' => $locationData
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to update user location', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal update lokasi user: ' . $e->getMessage());
        }
    }
}
