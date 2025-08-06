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
        
        // Then modify the enum to use 'paid' instead of 'completed'
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->change();
        });
        
        // Update 'paid' back to 'completed'
        DB::table('payments')->where('status', 'paid')->update(['status' => 'completed']);
    }
};
