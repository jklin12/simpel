<?php

namespace App\Repositories\Contracts;

interface JenisSuratRepositoryInterface
{
    /**
     * Get all jenis surat
     */
    public function all();

    /**
     * Find jenis surat by ID
     */
    public function find($id);

    /**
     * Create new jenis surat
     */
    public function create(array $data);

    /**
     * Update jenis surat
     */
    public function update($id, array $data);

    /**
     * Delete jenis surat
     */
    public function delete($id);

    /**
     * Get paginated jenis surat with filters
     */
    public function paginate($perPage = 15, array $filters = []);

    /**
     * Get only active jenis surat
     */
    public function getActive();

    /**
     * Find jenis surat by kode
     */
    public function findByKode($kode);

    /**
     * Get jenis surat with approval flows loaded
     */
    public function getWithApprovalFlows();
}
