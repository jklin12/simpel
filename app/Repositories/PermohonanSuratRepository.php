<?php

namespace App\Repositories;

use App\Models\PermohonanSurat;
use App\Repositories\Contracts\PermohonanSuratRepositoryInterface;

class PermohonanSuratRepository implements PermohonanSuratRepositoryInterface
{
    protected $model;

    public function __construct(PermohonanSurat $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->with(['jenisSurat', 'kelurahan', 'createdBy', 'approvals'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['jenisSurat', 'kelurahan.kecamatan', 'createdBy', 'approvals.approver'])
            ->findOrFail($id);
    }

    public function paginate($perPage = 15, array $filters = [])
    {
        $query = $this->model->with(['jenisSurat', 'kelurahan', 'createdBy', 'approvals']);

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_permohonan', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('nik_pemohon', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Jenis Surat filter
        if (isset($filters['jenis_surat_id']) && !empty($filters['jenis_surat_id'])) {
            $query->where('jenis_surat_id', $filters['jenis_surat_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getByUserRole($user, array $filters = [], $perPage = 15)
    {
        $query = $this->model->with(['jenisSurat', 'kelurahan', 'createdBy', 'approvals']);

        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_permohonan', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('nik_pemohon', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['jenis_surat_id']) && !empty($filters['jenis_surat_id'])) {
            $query->where('jenis_surat_id', $filters['jenis_surat_id']);
        }

        // Role-based filtering
        if ($user->hasRole('admin_kelurahan')) {
            $query->where('kelurahan_id', $user->kelurahan_id);
        } elseif ($user->hasRole('admin_kecamatan')) {
            $query->whereHas('kelurahan', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        } elseif ($user->hasRole('admin_kabupaten')) {
            $query->whereHas('kelurahan.kecamatan', function ($q) use ($user) {
                $q->where('kabupaten_id', $user->kabupaten_id);
            });
        }
        // super_admin sees all

        return $query->latest()->paginate($perPage);
    }

    public function updateStatus($id, $status, array $additionalData = [])
    {
        $permohonan = $this->model->findOrFail($id);
        $data = array_merge(['status' => $status], $additionalData);
        $permohonan->update($data);
        return $permohonan->fresh();
    }

    public function getCurrentApproval($permohonanId, $currentStep)
    {
        $permohonan = $this->model->findOrFail($permohonanId);
        return $permohonan->approvals()
            ->where('status', 'pending')
            ->where('step_order', $currentStep)
            ->first();
    }

    public function getNextApproval($permohonanId, $currentStepOrder)
    {
        $permohonan = $this->model->findOrFail($permohonanId);
        return $permohonan->approvals()
            ->where('step_order', '>', $currentStepOrder)
            ->orderBy('step_order')
            ->first();
    }
}
