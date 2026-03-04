<?php

namespace App\Services;

use App\Models\TemplateSurat;
use App\Models\JenisSurat;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class TemplateSuratService
{
    /**
     * Get paginated template surats with optional filters.
     */
    public function getPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = TemplateSurat::with('jenisSurat')->latest();

        if (!empty($filters['jenis_surat_id'])) {
            $query->where('jenis_surat_id', $filters['jenis_surat_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $query->where('judul', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all jenis surats for dropdown.
     */
    public function getAllJenisSurat()
    {
        return JenisSurat::where('is_active', true)->orderBy('nama')->get();
    }

    /**
     * Get single template by ID.
     */
    public function getById(int $id): TemplateSurat
    {
        return TemplateSurat::with('jenisSurat')->findOrFail($id);
    }

    /**
     * Create a new template surat.
     */
    public function create(array $data): TemplateSurat
    {
        $file = $data['file'] ?? null;
        if (!$file) {
            throw new \Exception('File template wajib diupload.');
        }

        $originalName = $file->getClientOriginalName();
        $filePath = $file->store('template-surat', 'public');

        return TemplateSurat::create([
            'jenis_surat_id'     => $data['jenis_surat_id'],
            'judul'              => $data['judul'],
            'deskripsi'          => $data['deskripsi'] ?? null,
            'file_path'          => $filePath,
            'file_original_name' => $originalName,
            'syarat'             => $data['syarat'] ?? [],
            'is_active'          => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ]);
    }

    /**
     * Update an existing template surat.
     */
    public function update(int $id, array $data): TemplateSurat
    {
        $template = $this->getById($id);

        $updateData = [
            'jenis_surat_id' => $data['jenis_surat_id'],
            'judul'          => $data['judul'],
            'deskripsi'      => $data['deskripsi'] ?? null,
            'syarat'         => $data['syarat'] ?? [],
            'is_active'      => isset($data['is_active']) ? (bool) $data['is_active'] : false,
        ];

        // Replace file if new one uploaded
        if (!empty($data['file'])) {
            // Delete old file
            Storage::disk('public')->delete($template->file_path);

            $file = $data['file'];
            $updateData['file_path']          = $file->store('template-surat', 'public');
            $updateData['file_original_name'] = $file->getClientOriginalName();
        }

        $template->update($updateData);

        return $template;
    }

    /**
     * Delete a template surat and its file.
     */
    public function delete(int $id): void
    {
        $template = $this->getById($id);
        Storage::disk('public')->delete($template->file_path);
        $template->delete();
    }

    /**
     * Get active templates grouped by jenis surat (for portal).
     */
    public function getActiveGroupedByJenisSurat(): \Illuminate\Support\Collection
    {
        return TemplateSurat::with('jenisSurat')
            ->where('is_active', true)
            ->get()
            ->groupBy('jenis_surat_id');
    }
}
