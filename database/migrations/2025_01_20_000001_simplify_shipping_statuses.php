<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing statuses to new simplified statuses
        DB::table('shippings')->whereIn('status', ['pending', 'ready_for_pickup', 'picked_up', 'in_transit'])
            ->update(['status' => 'preparing']);
        
        DB::table('shippings')->whereIn('status', ['failed', 'returned'])
            ->update(['status' => 'cancelled']);

        // Modify the enum column
        DB::statement("ALTER TABLE shippings MODIFY COLUMN status ENUM('preparing', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'preparing'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old statuses (approximate mapping)
        DB::table('shippings')->where('status', 'preparing')
            ->update(['status' => 'pending']);
        
        DB::table('shippings')->where('status', 'cancelled')
            ->update(['status' => 'failed']);

        // Restore the original enum
        DB::statement("ALTER TABLE shippings MODIFY COLUMN status ENUM('pending', 'preparing', 'ready_for_pickup', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned') DEFAULT 'pending'");
    }
};

