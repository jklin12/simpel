<?php

namespace App\Services;

use App\Repositories\Contracts\JenisSuratRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JenisSuratService
{
    protected $repository;

    public function __construct(JenisSuratRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all jenis surat
     */
    public function getAllJenisSurat()
    {
        return $this->repository->all();
    }

    /**
     * Get paginated jenis surat with filters
     */
    public function getJenisSuratPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Get jenis surat by ID
     */
    public function getJenisSuratById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get only active jenis surat
     */
    public function getActiveJenisSurat()
    {
        return $this->repository->getActive();
    }

    /**
     * Create new jenis surat
     */
    public function createJenisSurat(array $data)
    {
        DB::beginTransaction();

        try {
            // Auto-format kode to uppercase
            if (isset($data['kode'])) {
                $data['kode'] = strtoupper($data['kode']);
            }

            // Handle is_active checkbox
            $data['is_active'] = $data['is_active'] ?? false;

            // Transform required_fields
            if (isset($data['required_fields']) && is_array($data['required_fields'])) {
                $data['required_fields'] = collect($data['required_fields'])
                    ->filter(fn($f) => !empty($f['name']))
                    ->map(function ($f) {
                        $f['is_required'] = filter_var($f['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        if (($f['type'] ?? '') !== 'select' || empty($f['options'])) {
                            $f['options'] = null;
                        } else {
                            $f['options'] = array_values(array_filter($f['options'], fn($o) => $o !== ''));
                        }
                        return $f;
                    })
                    ->values()
                    ->toArray();
            }

            // Create jenis surat
            $jenisSurat = $this->repository->create($data);

            Log::info('Jenis Surat created successfully', [
                'id' => $jenisSurat->id,
                'kode' => $jenisSurat->kode,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return $jenisSurat;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create jenis surat', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Gagal membuat jenis surat: ' . $e->getMessage());
        }
    }

    /**
     * Update jenis surat
     */
    public function updateJenisSurat($id, array $data)
    {
        DB::beginTransaction();

        try {
            // Auto-format kode to uppercase
            if (isset($data['kode'])) {
                $data['kode'] = strtoupper($data['kode']);
            }

            // Handle is_active checkbox
            $data['is_active'] = $data['is_active'] ?? false;

            // Transform required_fields
            if (isset($data['required_fields']) && is_array($data['required_fields'])) {
                $data['required_fields'] = collect($data['required_fields'])
                    ->filter(fn($f) => !empty($f['name']))
                    ->map(function ($f) {
                        $f['is_required'] = filter_var($f['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        if (($f['type'] ?? '') !== 'select' || empty($f['options'])) {
                            $f['options'] = null;
                        } else {
                            $f['options'] = array_values(array_filter($f['options'], fn($o) => $o !== ''));
                        }
                        return $f;
                    })
                    ->values()
                    ->toArray();
            } elseif (array_key_exists('required_fields', $data)) {
                $data['required_fields'] = [];
            }

            // Update jenis surat
            $jenisSurat = $this->repository->update($id, $data);

            Log::info('Jenis Surat updated successfully', [
                'id' => $id,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return $jenisSurat;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update jenis surat', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal update jenis surat: ' . $e->getMessage());
        }
    }

    /**
     * Delete jenis surat
     */
    public function deleteJenisSurat($id)
    {
        DB::beginTransaction();

        try {
            $jenisSurat = $this->repository->find($id);

            // Business validation: Check if has active permohonan
            if ($jenisSurat->permohonanSurats()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus Jenis Surat yang memiliki permohonan terkait');
            }

            $this->repository->delete($id);

            Log::info('Jenis Surat deleted successfully', [
                'id' => $id,
                'deleted_by' => auth()->id()
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete jenis surat', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal hapus jenis surat: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        try {
            $jenisSurat = $this->repository->find($id);

            $newStatus = !$jenisSurat->is_active;
            $this->repository->update($id, ['is_active' => $newStatus]);

            Log::info('Jenis Surat status toggled', [
                'id' => $id,
                'new_status' => $newStatus
            ]);

            return $newStatus;
        } catch (\Exception $e) {
            Log::error('Failed to toggle jenis surat status', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
