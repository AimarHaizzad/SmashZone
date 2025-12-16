<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Sendinblue\Transport\SendinblueTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use App\Models\Order;

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

        $app = $this->app;
        $this->app->make(MailManager::class)->extend('brevo', function (array $config) use ($app) {
            $apiKey = $config['api_key'] ?? env('BREVO_API_KEY');

            if (blank($apiKey)) {
                throw new InvalidArgumentException('Brevo mailer requires a BREVO_API_KEY value.');
            }

            $endpoint = $config['endpoint'] ?? env('BREVO_API_ENDPOINT', 'default');
            $endpoint = $endpoint ?: 'default';

            // Create HttpClient instance (or null if not needed)
            $httpClient = HttpClient::create();

            $factory = new SendinblueTransportFactory(
                null, // Dispatcher - Laravel's dispatcher is not compatible with Symfony's interface
                $httpClient,
                $app->bound('log') ? $app['log'] : null
            );

            return $factory->create(new Dsn(
                'sendinblue+api',
                $endpoint,
                $apiKey,
                null
            ));
        });

        // Share pending orders count with all views (for owners and staff)
        View::composer('*', function ($view) {
            if (auth()->check() && (auth()->user()->isOwner() || auth()->user()->isStaff())) {
                // Count orders that need attention (pending, confirmed, processing, return_requested)
                $pendingOrdersCount = Order::whereIn('status', ['pending', 'confirmed', 'processing', 'return_requested'])
                    ->count();
                
                $view->with('pendingOrdersCount', $pendingOrdersCount);
            } else {
                $view->with('pendingOrdersCount', 0);
            }
        });
    }
}
