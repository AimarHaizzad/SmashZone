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

    /**
     * Delete past seeded booking data (for presentation/demo purposes)
     * Only accessible by owners for security
     */
    public function deletePastData(Request $request)
    {
        // Only allow owners
        if (!auth()->check() || !auth()->user()->isOwner()) {
            abort(403, 'Unauthorized. Only owners can delete seeded data.');
        }

        try {
            Log::info('Starting deletion of past seeded data', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email
            ]);

            $user = auth()->user();
            
            // Get owner's court IDs
            $courtIds = \App\Models\Court::where('owner_id', $user->id)->pluck('id');
            
            if ($courtIds->isEmpty()) {
                return redirect()->back()->with('error', 'No courts found. Cannot delete seeded data.');
            }

            // Delete payments for bookings in owner's courts (past data)
            // Past data is typically older than 1 day
            $deletedPayments = \App\Models\Payment::whereHas('booking', function($query) use ($courtIds) {
                $query->whereIn('court_id', $courtIds)
                      ->where('date', '<', now()->subDay()->format('Y-m-d'));
            })->delete();

            // Delete bookings in owner's courts (past data)
            $deletedBookings = \App\Models\Booking::whereIn('court_id', $courtIds)
                ->where('date', '<', now()->subDay()->format('Y-m-d'))
                ->delete();

            Log::info('Past seeded data deleted successfully', [
                'user_id' => auth()->id(),
                'deleted_bookings' => $deletedBookings,
                'deleted_payments' => $deletedPayments
            ]);

            return redirect()->route('analytics.index')->with('success', "✅ Deleted {$deletedBookings} past bookings and {$deletedPayments} payments. You can now seed fresh data for your presentation!");
        } catch (\Exception $e) {
            Log::error('Failed to delete past seeded data', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to delete seeded data: ' . $e->getMessage() . '. Please check the logs for details.');
        }
    }
}
