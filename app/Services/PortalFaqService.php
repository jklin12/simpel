<?php

namespace App\Services;

use App\Models\PortalFaq;
use App\Repositories\Contracts\PortalFaqRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PortalFaqService
{
    protected $repository;

    public function __construct(PortalFaqRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Ambil semua FAQ aktif yang dikelompokkan per kategori (untuk portal publik).
     */
    public function getGroupedAktif()
    {
        return $this->repository->getAktifByKategori();
    }

    public function createFaq(array $data): PortalFaq
    {
        DB::beginTransaction();
        try {
            $faq = $this->repository->create($data);
            Log::info('PortalFaq created', ['id' => $faq->id]);
            DB::commit();
            return $faq;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat FAQ', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal membuat FAQ: ' . $e->getMessage());
        }
    }

    public function updateFaq($id, array $data): PortalFaq
    {
        DB::beginTransaction();
        try {
            $faq = $this->repository->update($id, $data);
            Log::info('PortalFaq updated', ['id' => $id]);
            DB::commit();
            return $faq;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update FAQ', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal update FAQ: ' . $e->getMessage());
        }
    }

    public function deleteFaq($id): bool
    {
        DB::beginTransaction();
        try {
            $this->repository->delete($id);
            Log::info('PortalFaq deleted', ['id' => $id]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus FAQ', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal menghapus FAQ: ' . $e->getMessage());
        }
    }
}
