<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PortalStrukturOrganisasiRepositoryInterface
{
    /**
     * Get all items.
     */
    public function all();

    /**
     * Find an item by ID.
     */
    public function find($id);

    /**
     * Create a new item.
     */
    public function create(array $data);

    /**
     * Update an item.
     */
    public function update($id, array $data);

    /**
     * Delete an item.
     */
    public function delete($id);

    /**
     * Get paginated items with filters for table view.
     */
    public function paginate($perPage = 15, array $filters = []);

    /**
     * Get the full hierarchy tree.
     */
    public function getTree(): Collection;
}
