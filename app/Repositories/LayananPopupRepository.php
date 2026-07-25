<?php

namespace App\Repositories;

use App\Models\LayananPopup;
use App\Repositories\Contracts\LayananPopupRepositoryInterface;

class LayananPopupRepository implements LayananPopupRepositoryInterface
{
    protected $model;

    public function __construct(LayananPopup $model)
    {
        $this->model = $model;
    }

    public function getSingleton()
    {
        return $this->model->firstOrCreate(['id' => 1], [
            'wa_message'  => 'Halo Admin, saya ingin bertanya tentang layanan...',
            'button_text' => 'Mulai Chat',
            'is_active'   => true,
        ]);
    }

    public function update(array $data)
    {
        $item = $this->getSingleton();
        $item->update($data);
        return $item->fresh();
    }
}
