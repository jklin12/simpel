<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * Get all users
     */
    public function all();

    /**
     * Find user by ID
     */
    public function find($id);

    /**
     * Create new user
     */
    public function create(array $data);

    /**
     * Update user
     */
    public function update($id, array $data);

    /**
     * Delete user
     */
    public function delete($id);

    /**
     * Get paginated users with filters
     */
    public function paginate($perPage = 15, array $filters = []);

    /**
     * Find user by email
     */
    public function findByEmail($email);

    /**
     * Get users with their roles loaded
     */
    public function getUsersWithRoles();
}
