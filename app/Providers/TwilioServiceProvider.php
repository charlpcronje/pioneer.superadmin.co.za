<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Chat\V2\ServiceContext;
use Twilio\Rest\Client;

class TwilioServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class, function ($app) {
            return new Client($app['config']['services']['twilio']['account_sid'],
                $app['config']['services']['twilio']['auth_token']);
        });

    }

    public function provides(): array
    {
        return [
            Client::class,
        ];
    }
}
