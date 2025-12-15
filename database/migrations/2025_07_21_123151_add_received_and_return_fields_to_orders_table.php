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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('received_at')->nullable()->after('status');
            $table->timestamp('return_requested_at')->nullable()->after('received_at');
            $table->text('return_reason')->nullable()->after('return_requested_at');
        });

        // Update status enum to include return_requested
        // PostgreSQL doesn't support MODIFY COLUMN, use ALTER COLUMN instead
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // For PostgreSQL, Laravel's enum() creates a check constraint
            // Find and drop all check constraints on the status column
            $constraints = DB::select("
                SELECT tc.constraint_name 
                FROM information_schema.table_constraints tc
                JOIN information_schema.constraint_column_usage ccu 
                    ON tc.constraint_name = ccu.constraint_name
                WHERE tc.table_name = 'orders' 
                AND tc.constraint_type = 'CHECK'
                AND ccu.column_name = 'status'
            ");
            
            foreach ($constraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Add new constraint with updated enum values
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'return_requested'))");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'return_requested') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['received_at', 'return_requested_at', 'return_reason']);
        });

        // Revert status enum
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // For PostgreSQL, find and drop all check constraints on the status column
            $constraints = DB::select("
                SELECT tc.constraint_name 
                FROM information_schema.table_constraints tc
                JOIN information_schema.constraint_column_usage ccu 
                    ON tc.constraint_name = ccu.constraint_name
                WHERE tc.table_name = 'orders' 
                AND tc.constraint_type = 'CHECK'
                AND ccu.column_name = 'status'
            ");
            
            foreach ($constraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Add original constraint
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'))");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
        }
    }
};

