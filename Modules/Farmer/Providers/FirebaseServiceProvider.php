<?php

namespace Modules\Farmer\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Farmer\Services\Firebase;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Firebase::class, function ($app) {
            return new Firebase(config('firebase'));
        });
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
