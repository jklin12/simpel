<?php

namespace App\Repositories\Contracts;

interface PermohonanSuratRepositoryInterface
{
    public function all();
    public function find($id);
    public function paginate($perPage = 15, array $filters = []);
    public function getByUserRole($user, array $filters = [], $perPage = 15);
    public function updateStatus($id, $status, array $additionalData = []);
    public function getCurrentApproval($permohonanId, $currentStep);
    public function getNextApproval($permohonanId, $currentStepOrder);
}
