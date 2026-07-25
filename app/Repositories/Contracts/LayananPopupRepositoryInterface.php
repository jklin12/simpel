<?php

namespace App\Repositories\Contracts;

interface LayananPopupRepositoryInterface
{
    public function getSingleton();
    public function update(array $data);
}
