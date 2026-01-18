<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Database\Seeders\PastDataSeeder;
use Illuminate\Support\Facades\Log;

class SeederController extends Controller
{
    /**
     * Run the PastDataSeeder via HTTP route
     * Only accessible by owners for security
     */
    public function runPastDataSeeder(Request $request)
    {
        // Only allow owners
        if (!auth()->check() || !auth()->user()->isOwner()) {
            abort(403, 'Unauthorized. Only owners can run this seeder.');
        }

        try {
            Log::info('Starting PastDataSeeder via HTTP request', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email
            ]);

            // Create seeder instance and run it
            // The seeder now handles running without a command object
            $seeder = new PastDataSeeder();
            $seeder->run();

            Log::info('PastDataSeeder completed successfully via HTTP');

            return redirect()->route('analytics.index')->with('success', '✅ Past booking data seeded successfully! You can now view analytics with historical data.');
        } catch (\Exception $e) {
            Log::error('PastDataSeeder failed via HTTP', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to seed past data: ' . $e->getMessage() . '. Please check the logs for details.');
        }
    }
}
