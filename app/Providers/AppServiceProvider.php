<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
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
                // Check if database connection is available first
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    // Database not available - set default and return early
                    $view->with('pendingOrdersCount', 0);
                    return;
                }

                // Only run if user is authenticated (wrap in try-catch as auth might need DB)
                try {
                    if (auth()->check()) {
                        try {
                            $user = auth()->user();
                            if ($user && ($user->isOwner() || $user->isStaff())) {
                                try {
                                    // Check if orders table exists
                                    if (Schema::hasTable('orders')) {
                                        // Count orders that need attention (pending, confirmed, processing, return_requested)
                                        $pendingOrdersCount = Order::whereIn('status', ['pending', 'confirmed', 'processing', 'return_requested'])
                                            ->count();
                                        
                                        $view->with('pendingOrdersCount', $pendingOrdersCount);
                                        return;
                                    }
                                } catch (\Exception $tableException) {
                                    // Table check failed - silently fail
                                    \Log::debug('Could not check orders table', ['error' => $tableException->getMessage()]);
                                }
                            }
                        } catch (\Exception $userException) {
                            // User check failed - silently fail
                            \Log::debug('Could not get user info', ['error' => $userException->getMessage()]);
                        }
                    }
                } catch (\Exception $authException) {
                    // Auth check failed (might need DB) - silently fail
                    \Log::debug('Could not check authentication', ['error' => $authException->getMessage()]);
                }
                // Default to 0 if not authenticated, not owner/staff, or database unavailable
                $view->with('pendingOrdersCount', 0);
            } catch (\Exception $e) {
                // Catch any other exceptions to prevent 500 errors
                \Log::debug('Failed to get pending orders count in view composer', ['error' => $e->getMessage()]);
                $view->with('pendingOrdersCount', 0);
            }
        });
    }
}
