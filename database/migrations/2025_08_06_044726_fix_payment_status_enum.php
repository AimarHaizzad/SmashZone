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
        // First update any existing 'completed' status to 'paid'
        DB::table('payments')->where('status', 'completed')->update(['status' => 'paid']);
        
        // Handle PostgreSQL vs MySQL differently
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // For PostgreSQL, find and drop the existing check constraint
            $constraints = DB::select("
                SELECT tc.constraint_name 
                FROM information_schema.table_constraints tc
                JOIN information_schema.constraint_column_usage ccu 
                    ON tc.constraint_name = ccu.constraint_name
                WHERE tc.table_name = 'payments' 
                AND tc.constraint_type = 'CHECK'
                AND ccu.column_name = 'status'
            ");
            
            foreach ($constraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Add new constraint with updated enum values
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'paid', 'failed'))");
            DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'pending'");
        } else {
            // MySQL syntax
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('status', ['pending', 'paid', 'failed'])->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update 'paid' back to 'completed'
        DB::table('payments')->where('status', 'paid')->update(['status' => 'completed']);
        
        // Handle PostgreSQL vs MySQL differently
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // For PostgreSQL, find and drop the existing check constraint
            $constraints = DB::select("
                SELECT tc.constraint_name 
                FROM information_schema.table_constraints tc
                JOIN information_schema.constraint_column_usage ccu 
                    ON tc.constraint_name = ccu.constraint_name
                WHERE tc.table_name = 'payments' 
                AND tc.constraint_type = 'CHECK'
                AND ccu.column_name = 'status'
            ");
            
            foreach ($constraints as $constraint) {
                try {
                    DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
            
            // Add original constraint
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'completed', 'failed'))");
            DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'pending'");
        } else {
            // MySQL syntax
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->change();
            });
        }
    }
};
