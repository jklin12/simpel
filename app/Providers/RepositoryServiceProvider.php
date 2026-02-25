<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings
     */
    protected $repositories = [
        \App\Repositories\Contracts\UserRepositoryInterface::class =>
        \App\Repositories\UserRepository::class,

        \App\Repositories\Contracts\JenisSuratRepositoryInterface::class =>
        \App\Repositories\JenisSuratRepository::class,

        \App\Repositories\Contracts\ApprovalFlowRepositoryInterface::class =>
        \App\Repositories\ApprovalFlowRepository::class,

        \App\Repositories\Contracts\SuratCounterRepositoryInterface::class =>
        \App\Repositories\SuratCounterRepository::class,

        \App\Repositories\Contracts\PermohonanSuratRepositoryInterface::class =>
        \App\Repositories\PermohonanSuratRepository::class,

        // Portal Kecamatan
        \App\Repositories\Contracts\PortalBeritaRepositoryInterface::class =>
        \App\Repositories\PortalBeritaRepository::class,

        \App\Repositories\Contracts\PortalDataKelurahanRepositoryInterface::class =>
        \App\Repositories\PortalDataKelurahanRepository::class,

        \App\Repositories\Contracts\PortalStrukturOrganisasiRepositoryInterface::class =>
        \App\Repositories\PortalStrukturOrganisasiRepository::class,
    ];

    /**
     * Register services.
     */
    public function register()
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
