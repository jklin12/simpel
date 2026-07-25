<?php

namespace App\Services;

use App\Models\LayananPopup;
use App\Repositories\Contracts\LayananPopupRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LayananPopupService
{
    protected $repository;

    public function __construct(LayananPopupRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getSingleton(): LayananPopup
    {
        return $this->repository->getSingleton();
    }

    public function updatePopup(array $data): LayananPopup
    {
        DB::beginTransaction();
        try {
            $popup = $this->repository->getSingleton();

            if (!empty($data['gambar']) && $data['gambar'] instanceof \Illuminate\Http\UploadedFile) {
                if ($popup->gambar) {
                    Storage::disk('public')->delete($popup->gambar);
                }
                $data['gambar'] = $data['gambar']->store('layanan/popup', 'public');
            } else {
                unset($data['gambar']);
            }

            $popup = $this->repository->update($data);

            Log::info('LayananPopup updated', ['id' => $popup->id]);
            DB::commit();
            return $popup;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update popup layanan', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal update popup layanan: ' . $e->getMessage());
        }
    }
}
