<?php

namespace App\Providers;

use App\Contracts\Repositories\PostRepository;
use App\Repositories\Eloquent\PostRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PostRepository::class, PostRepositoryEloquent::class);
        //:end-bindings:
    }
}
