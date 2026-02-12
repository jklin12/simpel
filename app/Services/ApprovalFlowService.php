<?php

namespace App\Services;

use App\Repositories\Contracts\ApprovalFlowRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalFlowService
{
    protected $repository;

    public function __construct(ApprovalFlowRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllApprovalFlows()
    {
        return $this->repository->all();
    }

    public function getApprovalFlowsPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getApprovalFlowById($id)
    {
        return $this->repository->find($id);
    }

    public function getActiveApprovalFlows()
    {
        return $this->repository->getActive();
    }

    public function createApprovalFlow(array $data)
    {
        DB::beginTransaction();

        try {
            // Business validation: Check duplicate
            if ($this->repository->checkDuplicate($data['jenis_surat_id'], $data['kelurahan_id'])) {
                throw new \Exception('Approval Flow untuk kombinasi Jenis Surat dan Kelurahan ini sudah ada');
            }

            // Handle checkboxes
            $data['require_kecamatan_approval'] = $data['require_kecamatan_approval'] ?? false;
            $data['require_kabupaten_approval'] = $data['require_kabupaten_approval'] ?? false;
            $data['is_active'] = $data['is_active'] ?? false;

            $approvalFlow = $this->repository->create($data);

            Log::info('Approval Flow created successfully', [
                'id' => $approvalFlow->id,
                'jenis_surat_id' => $approvalFlow->jenis_surat_id,
                'kelurahan_id' => $approvalFlow->kelurahan_id,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return $approvalFlow;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create approval flow', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Gagal membuat approval flow: ' . $e->getMessage());
        }
    }

    public function updateApprovalFlow($id, array $data)
    {
        DB::beginTransaction();

        try {
            // Business validation: Check duplicate (excluding current record)
            if ($this->repository->checkDuplicate($data['jenis_surat_id'], $data['kelurahan_id'], $id)) {
                throw new \Exception('Approval Flow untuk kombinasi Jenis Surat dan Kelurahan ini sudah ada');
            }

            // Handle checkboxes
            $data['require_kecamatan_approval'] = $data['require_kecamatan_approval'] ?? false;
            $data['require_kabupaten_approval'] = $data['require_kabupaten_approval'] ?? false;
            $data['is_active'] = $data['is_active'] ?? false;

            $approvalFlow = $this->repository->update($id, $data);

            Log::info('Approval Flow updated successfully', [
                'id' => $id,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return $approvalFlow;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update approval flow', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal update approval flow: ' . $e->getMessage());
        }
    }

    public function deleteApprovalFlow($id)
    {
        DB::beginTransaction();

        try {
            $approvalFlow = $this->repository->find($id);

            // Business validation: Check if has permohonan
            if ($approvalFlow->permohonanSurats()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus Approval Flow yang memiliki permohonan terkait');
            }

            $this->repository->delete($id);

            Log::info('Approval Flow deleted successfully', [
                'id' => $id,
                'deleted_by' => auth()->id()
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete approval flow', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Gagal hapus approval flow: ' . $e->getMessage());
        }
    }
}
