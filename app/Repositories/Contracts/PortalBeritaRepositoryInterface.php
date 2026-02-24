<?php

namespace App\Repositories\Contracts;

interface PortalBeritaRepositoryInterface
{
    public function all();
    public function find($id);
    public function findBySlug(string $slug);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15, array $filters = []);
    public function getPublished($perPage = 6);
}
