<?php

namespace ShowHeroes\Passport\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class GoogleServiceProvider
 * @package ShowHeroes\Passport\Providers
 *
 * Provides access to Google services.
 */
class GoogleServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        ;
        // YouTube Service
        $this->app->bind(
            \Google_Service::class,
            function ($app) {
                $client = new \Google_Client();
                $client->setApplicationName(config('app.name'));
                $client->setClientId(config('service.google.client_id'));
                $client->setClientSecret(config('service.google.client_secret'));
                $client->setRedirectUri(config('service.google.redirect'));

                return new \Google_Service($client);
            });
    }
}
