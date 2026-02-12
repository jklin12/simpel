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

        // Add more repositories here as needed
        // \App\Repositories\Contracts\ProductRepositoryInterface::class => 
        //     \App\Repositories\ProductRepository::class,
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
