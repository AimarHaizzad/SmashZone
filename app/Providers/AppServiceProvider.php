<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Transport;

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

        $this->app->make(MailManager::class)->extend('brevo', function (array $config) {
            $apiKey = $config['api_key'] ?? env('BREVO_API_KEY');

            if (blank($apiKey)) {
                throw new InvalidArgumentException('Brevo mailer requires a BREVO_API_KEY value.');
            }

            $endpoint = $config['endpoint'] ?? env('BREVO_API_ENDPOINT', 'default');
            $endpoint = $endpoint ?: 'default';

            $dsn = sprintf(
                'sendinblue+api://%s@%s',
                rawurlencode($apiKey),
                $endpoint
            );

            return Transport::fromDsn($dsn, null, HttpClient::create());
        });
    }
}
