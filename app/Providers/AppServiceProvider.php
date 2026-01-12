<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
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
        // Force APP_URL to prevent localhost issues in production
        $appUrl = config('app.url', env('APP_URL'));
        
        // If still localhost in production, try to detect from request
        if (($appUrl === 'http://localhost' || !$appUrl) && app()->environment('production')) {
            $request = request();
            if ($request) {
                $appUrl = $request->getSchemeAndHttpHost();
            }
        }

        if ($appUrl && $appUrl !== 'http://localhost') {
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
            try {
                // Only run if user is authenticated and database connection is available
                if (auth()->check()) {
                    $user = auth()->user();
                    if ($user && ($user->isOwner() || $user->isStaff())) {
                        try {
                            // Check if orders table exists and database is connected
                            if (Schema::hasTable('orders')) {
                                // Count orders that need attention (pending, confirmed, processing, return_requested)
                                $pendingOrdersCount = Order::whereIn('status', ['pending', 'confirmed', 'processing', 'return_requested'])
                                    ->count();
                                
                                $view->with('pendingOrdersCount', $pendingOrdersCount);
                                return;
                            }
                        } catch (\Illuminate\Database\QueryException $dbException) {
                            // Database connection issue - silently fail
                            \Log::debug('Database not available for pending orders count', ['error' => $dbException->getMessage()]);
                        }
                    }
                }
                // Default to 0 if not authenticated, not owner/staff, or database unavailable
                $view->with('pendingOrdersCount', 0);
            } catch (\Exception $e) {
                // Catch any other exceptions to prevent 500 errors
                \Log::warning('Failed to get pending orders count in view composer', ['error' => $e->getMessage()]);
                $view->with('pendingOrdersCount', 0);
            }
        });
    }
}
