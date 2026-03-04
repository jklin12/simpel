<?php

namespace App\Services;

use App\Models\PortalDataKelurahan;
use App\Repositories\Contracts\PortalDataKelurahanRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PortalDataKelurahanService
{
    protected $repository;

    public function __construct(PortalDataKelurahanRepositoryInterface $repository)
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

    public function getByKategori(string $kategori)
    {
        return $this->repository->getByKategori($kategori);
    }

    /**
     * Ambil semua data berlokasi untuk ditampilkan di peta.
     * Return format yang sudah siap untuk JSON response Leaflet.
     */
    public function getDataForMap(): array
    {
        $grouped = $this->repository->getDataForMap();
        $labels  = PortalDataKelurahan::labelKategori();
        $ikons   = PortalDataKelurahan::ikonKategori();

        $result = [];
        foreach ($grouped as $kategori => $items) {
            $baseIkon = $ikons[$kategori] ?? '📍';

            $result[$kategori] = [
                'label' => $labels[$kategori] ?? $kategori,
                'ikon'  => $baseIkon,
                'data'  => $items->map(function ($item) use ($kategori, $baseIkon) {
                    $itemIkon = $baseIkon;
                    if ($kategori === 'tempat_ibadah' && !empty($item->jenis_fasilitas)) {
                        $jns = strtolower($item->jenis_fasilitas);
                        if (str_contains($jns, 'masjid') || str_contains($jns, 'langgar') || str_contains($jns, 'musholla')) {
                            $itemIkon = '🕌';
                        } elseif (str_contains($jns, 'gereja')) {
                            $itemIkon = '⛪';
                        } elseif (str_contains($jns, 'pura')) {
                            $itemIkon = '🛕';
                        } elseif (str_contains($jns, 'vihara')) {
                            $itemIkon = '☸️';
                        } elseif (str_contains($jns, 'kelenteng') || str_contains($jns, 'lintang')) {
                            $itemIkon = '⛩️';
                        } elseif (str_contains($jns, 'sinagoge')) {
                            $itemIkon = '🕍';
                        }
                    }

                    return [
                        'id'               => $item->id,
                        'nama'             => $item->nama,
                        'alamat'           => $item->alamat,
                        'keterangan'       => $item->keterangan,
                        'kelurahan'        => $item->kelurahan?->nama,
                        'jenis_fasilitas'  => $item->jenis_fasilitas,
                        'status_fasilitas' => $item->status_fasilitas,
                        'rt'               => $item->rt ? str_pad($item->rt, 3, '0', STR_PAD_LEFT) : null,
                        'rw'               => $item->rw ? str_pad($item->rw, 3, '0', STR_PAD_LEFT) : null,
                        'lat'              => $item->latitude,
                        'lng'              => $item->longitude,
                        'ikon'             => $itemIkon,
                        'foto'             => $item->foto ? \Illuminate\Support\Facades\Storage::url($item->foto) : null,
                    ];
                })->values(),
            ];
        }

        return $result;
    }

    public function createData(array $data): PortalDataKelurahan
    {
        DB::beginTransaction();

        try {
            // Handle upload foto
            if (!empty($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
                $data['foto'] = $data['foto']->store('portal/data-kelurahan', 'public');
            }

            $item = $this->repository->create($data);

            Log::info('PortalDataKelurahan created', [
                'id'       => $item->id,
                'kategori' => $item->kategori,
                'nama'     => $item->nama,
            ]);

            DB::commit();

            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat data kelurahan', ['error' => $e->getMessage()]);
            throw new \Exception('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function updateData($id, array $data): PortalDataKelurahan
    {
        DB::beginTransaction();

        try {
            $item = $this->repository->find($id);

            // Handle upload foto baru
            if (!empty($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
                if ($item->foto) {
                    Storage::disk('public')->delete($item->foto);
                }
                $data['foto'] = $data['foto']->store('portal/data-kelurahan', 'public');
            } else {
                unset($data['foto']);
            }

            $item = $this->repository->update($id, $data);

            Log::info('PortalDataKelurahan updated', ['id' => $id]);

            DB::commit();

            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update data kelurahan', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal update data: ' . $e->getMessage());
        }
    }

    public function deleteData($id): bool
    {
        DB::beginTransaction();

        try {
            $item = $this->repository->find($id);

            if ($item->foto) {
                Storage::disk('public')->delete($item->foto);
            }

            $this->repository->delete($id); // Soft delete

            Log::info('PortalDataKelurahan deleted', ['id' => $id]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal hapus data kelurahan', ['id' => $id, 'error' => $e->getMessage()]);
            throw new \Exception('Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
