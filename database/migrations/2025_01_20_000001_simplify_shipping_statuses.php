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
        // Skip if table doesn't exist (fresh database)
        if (!Schema::hasTable('shippings')) {
            return;
        }

        // Update existing statuses to new simplified statuses
        DB::table('shippings')->whereIn('status', ['pending', 'ready_for_pickup', 'picked_up', 'in_transit'])
            ->update(['status' => 'preparing']);
        
        DB::table('shippings')->whereIn('status', ['failed', 'returned'])
            ->update(['status' => 'cancelled']);

        // Handle PostgreSQL vs MySQL differently
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // For PostgreSQL, find and drop the existing check constraint
            $constraints = DB::select("
                SELECT tc.constraint_name 
                FROM information_schema.table_constraints tc
                JOIN information_schema.constraint_column_usage ccu 
                    ON tc.constraint_name = ccu.constraint_name
                WHERE tc.table_name = 'shippings' 
                AND tc.constraint_type = 'CHECK'
                AND ccu.column_name = 'status'
            ");
            
            foreach ($constraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE shippings DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Add new constraint with updated enum values
            DB::statement("ALTER TABLE shippings ADD CONSTRAINT shippings_status_check CHECK (status IN ('preparing', 'out_for_delivery', 'delivered', 'cancelled'))");
            DB::statement("ALTER TABLE shippings ALTER COLUMN status SET DEFAULT 'preparing'");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE shippings MODIFY COLUMN status ENUM('preparing', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'preparing'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip if table doesn't exist
        if (!Schema::hasTable('shippings')) {
            return;
        }

        // Revert to old statuses (approximate mapping)
        DB::table('shippings')->where('status', 'preparing')
            ->update(['status' => 'pending']);
        
        DB::table('shippings')->where('status', 'cancelled')
            ->update(['status' => 'failed']);

        // Handle PostgreSQL vs MySQL differently
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // For PostgreSQL, find and drop the existing check constraint
            $constraints = DB::select("
                SELECT tc.constraint_name 
                FROM information_schema.table_constraints tc
                JOIN information_schema.constraint_column_usage ccu 
                    ON tc.constraint_name = ccu.constraint_name
                WHERE tc.table_name = 'shippings' 
                AND tc.constraint_type = 'CHECK'
                AND ccu.column_name = 'status'
            ");
            
            foreach ($constraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE shippings DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Restore the original constraint
            DB::statement("ALTER TABLE shippings ADD CONSTRAINT shippings_status_check CHECK (status IN ('pending', 'preparing', 'ready_for_pickup', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'))");
            DB::statement("ALTER TABLE shippings ALTER COLUMN status SET DEFAULT 'pending'");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE shippings MODIFY COLUMN status ENUM('pending', 'preparing', 'ready_for_pickup', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned') DEFAULT 'pending'");
        }
    }
};

