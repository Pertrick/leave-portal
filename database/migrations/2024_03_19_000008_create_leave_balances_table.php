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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->year('year'); // The year this balance applies to
            $table->integer('total_entitled_days'); // Total days entitled for this year
            $table->integer('days_taken')->default(0); // Days already taken
            $table->integer('days_remaining'); // Remaining days
            $table->integer('days_carried_forward')->default(0); // Days carried from previous year
            $table->timestamps();
            $table->unique(['user_id', 'leave_type_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
}; 