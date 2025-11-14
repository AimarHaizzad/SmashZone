<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('payment_id')
                ->nullable()
                ->after('total_price')
                ->constrained()
                ->nullOnDelete();
        });

        if (Schema::hasColumn('payments', 'booking_id')) {
            $existing = DB::table('payments')
                ->whereNotNull('booking_id')
                ->pluck('booking_id', 'id');

            foreach ($existing as $paymentId => $bookingId) {
                DB::table('bookings')
                    ->where('id', $bookingId)
                    ->update(['payment_id' => $paymentId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
        });
    }
};

