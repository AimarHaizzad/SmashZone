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
        // Only modify the enum if we're using MySQL/MariaDB
        // SQLite doesn't support MODIFY COLUMN, and since the original migration
        // already includes 'completed' in the enum, this is safe to skip for SQLite
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            try {
                // For MySQL, we can modify the enum directly
                DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");
            } catch (\Exception $e) {
                // If the column already has the correct enum values, this might fail
                // In that case, we can safely ignore the error
                if (strpos($e->getMessage(), 'Duplicate column name') === false && 
                    strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
        // For SQLite and other databases, this migration is a no-op
        // The original migration already includes 'completed' in the enum definition
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only modify the enum if we're using MySQL/MariaDB
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            try {
                // Remove 'completed' from the enum
                DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'");
            } catch (\Exception $e) {
                // Ignore errors on rollback
            }
        }
        // For SQLite, no action needed as enum constraints aren't enforced
    }
};
