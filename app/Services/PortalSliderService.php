<?php

namespace App\Services;

use App\Models\PortalSlider;
use App\Repositories\Contracts\PortalSliderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PortalSliderService
{
    protected $repository;

    public function __construct(PortalSliderRepositoryInterface $repository)
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
     * Ambil semua slider aktif untuk portal publik.
     */
    public function getAktifSliders()
    {
        return $this->repository->getAktif();
    }

    public function createSlider(array $data): PortalSlider
    {
        DB::beginTransaction();
        try {
            if (!empty($data['gambar']) && $data['gambar'] instanceof \Illuminate\Http\UploadedFile) {
                $data['gambar'] = $data['gambar']->store('portal/slider', 'public');
            }

            $slider = $this->repository->create($data);

            Log::info('PortalSlider created', ['id' => $slider->id, 'judul' => $slider->judul]);
            DB::commit();
            return $slider;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat slider', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal membuat slider: ' . $e->getMessage());
        }
    }

    public function updateSlider($id, array $data): PortalSlider
    {
        DB::beginTransaction();
        try {
            $slider = $this->repository->find($id);

            if (!empty($data['gambar']) && $data['gambar'] instanceof \Illuminate\Http\UploadedFile) {
                // Hapus gambar lama
                if ($slider->gambar) {
                    Storage::disk('public')->delete($slider->gambar);
                }
                $data['gambar'] = $data['gambar']->store('portal/slider', 'public');
            } else {
                unset($data['gambar']);
            }

            $slider = $this->repository->update($id, $data);

            Log::info('PortalSlider updated', ['id' => $id]);
            DB::commit();
            return $slider;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update slider', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal update slider: ' . $e->getMessage());
        }
    }

    public function deleteSlider($id): bool
    {
        DB::beginTransaction();
        try {
            $slider = $this->repository->find($id);

            if ($slider->gambar) {
                Storage::disk('public')->delete($slider->gambar);
            }

            $this->repository->delete($id);

            Log::info('PortalSlider deleted', ['id' => $id]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus slider', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal menghapus slider: ' . $e->getMessage());
        }
    }
}
