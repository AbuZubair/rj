<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Model\Model;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Model\Model', function ($app) {
            return new Model();
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
