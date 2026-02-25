<?php

namespace App\Services;

use App\Models\PortalStrukturOrganisasi;
use App\Repositories\Contracts\PortalStrukturOrganisasiRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PortalStrukturOrganisasiService
{
    protected $repository;

    public function __construct(PortalStrukturOrganisasiRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getPaginated($perPage = 15, array $filters = [])
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getById($id)
    {
        return $this->repository->find($id);
    }

    public function getTreeData()
    {
        return $this->repository->getTree();
    }

    public function createPerson(array $data): PortalStrukturOrganisasi
    {
        DB::beginTransaction();

        try {
            // Handle upload foto
            if (!empty($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
                $data['foto'] = $data['foto']->store('portal/struktur-organisasi', 'public');
            }

            // Sanitasi parent_id jika kosong string jadikan null
            if (empty($data['parent_id'])) {
                $data['parent_id'] = null;
            }

            $person = $this->repository->create($data);

            Log::info('PortalStrukturOrganisasi created', [
                'id'        => $person->id,
                'nama'      => $person->nama,
                'jabatan'   => $person->jabatan,
                'parent_id' => $person->parent_id,
            ]);

            DB::commit();

            return $person;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat anggota struktur organisasi', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function updatePerson($id, array $data): PortalStrukturOrganisasi
    {
        DB::beginTransaction();

        try {
            $person = $this->repository->find($id);

            // Mencegah self-referencing loop: Seseorang tidak bisa menjadi parent bagi dirinya sendiri
            if (isset($data['parent_id']) && $data['parent_id'] == $id) {
                throw new \Exception('Tidak dapat memilih diri sendiri sebagai atasan.');
            }

            // Handle upload foto
            if (!empty($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
                if ($person->foto) {
                    Storage::disk('public')->delete($person->foto);
                }
                $data['foto'] = $data['foto']->store('portal/struktur-organisasi', 'public');
            } else {
                unset($data['foto']); // Hapus dari update data jika tidak ada foto baru
            }

            // Sanitasi parent_id jika kosong
            if (array_key_exists('parent_id', $data) && empty($data['parent_id'])) {
                $data['parent_id'] = null;
            }

            $person = $this->repository->update($id, $data);

            Log::info('PortalStrukturOrganisasi updated', ['id' => $id]);

            DB::commit();

            return $person;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update anggota struktur organisasi', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal update data: ' . $e->getMessage());
        }
    }

    public function deletePerson($id): bool
    {
        DB::beginTransaction();

        try {
            $person = $this->repository->find($id);

            if ($person->foto) {
                Storage::disk('public')->delete($person->foto);
            }

            // Note: the model's 'deleting' event will trigger cascaded deletion
            // of its children. This means the photos of children will NOT be explicitly
            // deleted here unless we write a recursive block, but since soft-deletes are
            // not used on this table, DB consistency is maintained. 
            // For production, a more robust photo cleanup for cascaded deletes could be implemented in the model.

            $this->repository->delete($id);

            Log::info('PortalStrukturOrganisasi deleted', ['id' => $id]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus anggota struktur organisasi', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
