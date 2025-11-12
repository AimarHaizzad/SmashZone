<?php

namespace App\Providers;

use App\Notifications\Channels\BrevoChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = config('app.url');

        if ($appUrl) {
            URL::forceRootUrl($appUrl);

            if (Str::startsWith($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        $this->app->make(ChannelManager::class)->extend('brevo', function ($app) {
            return $app->make(BrevoChannel::class);
        });
    }
}
