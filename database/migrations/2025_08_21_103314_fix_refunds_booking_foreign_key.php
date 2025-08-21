<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Make the column nullable first
            $table->unsignedBigInteger('booking_id')->nullable()->change();
            
            // Add the foreign key constraint with set null on delete
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['booking_id']);
            
            // Make the column not nullable again
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
        });
    }
};
