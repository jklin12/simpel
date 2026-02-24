<?php

namespace App\Services;

use App\Models\PortalBerita;
use App\Repositories\Contracts\PortalBeritaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PortalBeritaService
{
    protected $repository;

    public function __construct(PortalBeritaRepositoryInterface $repository)
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

    public function getBySlug(string $slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function getPublished($perPage = 6)
    {
        return $this->repository->getPublished($perPage);
    }

    public function getLatestPublished($limit = 3)
    {
        return PortalBerita::published()->limit($limit)->get();
    }

    public function createBerita(array $data): PortalBerita
    {
        DB::beginTransaction();

        try {
            // Auto-generate slug
            $data['slug'] = PortalBerita::generateSlug($data['judul']);

            // Handle upload thumbnail
            if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                $data['thumbnail'] = $data['thumbnail']->store('portal/berita', 'public');
            }

            // Set published_at jika status published
            if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $berita = $this->repository->create($data);

            Log::info('PortalBerita created', ['id' => $berita->id, 'judul' => $berita->judul]);

            DB::commit();

            return $berita;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat berita portal', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal membuat berita: ' . $e->getMessage());
        }
    }

    public function updateBerita($id, array $data): PortalBerita
    {
        DB::beginTransaction();

        try {
            $berita = $this->repository->find($id);

            // Regenerate slug hanya jika judul berubah
            if (!empty($data['judul']) && $data['judul'] !== $berita->judul) {
                $data['slug'] = PortalBerita::generateSlug($data['judul']);
            }

            // Handle upload thumbnail baru
            if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                // Hapus thumbnail lama
                if ($berita->thumbnail) {
                    Storage::disk('public')->delete($berita->thumbnail);
                }
                $data['thumbnail'] = $data['thumbnail']->store('portal/berita', 'public');
            } else {
                unset($data['thumbnail']); // Jangan overwrite jika tidak ada upload baru
            }

            // Set published_at saat pertama kali diubah ke published
            if (($data['status'] ?? null) === 'published' && !$berita->published_at) {
                $data['published_at'] = now();
            }

            $berita = $this->repository->update($id, $data);

            Log::info('PortalBerita updated', ['id' => $id]);

            DB::commit();

            return $berita;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update berita portal', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal update berita: ' . $e->getMessage());
        }
    }

    public function deleteBerita($id): bool
    {
        DB::beginTransaction();

        try {
            $berita = $this->repository->find($id);

            // Hapus thumbnail dari storage
            if ($berita->thumbnail) {
                Storage::disk('public')->delete($berita->thumbnail);
            }

            $this->repository->delete($id);

            Log::info('PortalBerita deleted', ['id' => $id]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus berita portal', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal menghapus berita: ' . $e->getMessage());
        }
    }
}
