<?php

namespace App\Providers;

use App\Contracts\Services\IPostService;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;
use Prettus\Repository\Providers\RepositoryServiceProvider;

class ServeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(IPostService::class, PostService::class);
    }
}
