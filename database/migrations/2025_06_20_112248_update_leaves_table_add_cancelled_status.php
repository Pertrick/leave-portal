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
        Schema::table('leaves', function (Blueprint $table) {
            // First, update existing cancelled records to have 'cancelled' status
            DB::statement("UPDATE leaves SET status = 'cancelled' WHERE is_cancelled = true");
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Update records to restore the boolean field
            DB::statement("UPDATE leaves SET is_cancelled = true WHERE status = 'cancelled'");
        });
    }
};
