<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\Shared;
use App\Library\Services\Api;

class SharedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\Shared', function ($app) {
            return new Shared();
        });

        $this->app->bind('App\Library\Services\Api', function ($app) {
            return new Api();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


}
