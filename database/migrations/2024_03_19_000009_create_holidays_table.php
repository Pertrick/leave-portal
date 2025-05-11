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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date')->nullable(); // Nullable for recurring holidays
            $table->string('type'); // public, company, location
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_active')->default(true);
            // New fields for recurring holidays
            $table->string('recurrence_type')->nullable(); // 'fixed', 'easter', 'lunar', 'custom'
            $table->integer('recurrence_day')->nullable(); // Day of month for fixed
            $table->integer('recurrence_month')->nullable(); // Month for fixed
            $table->integer('easter_offset')->nullable(); // Days before/after Easter
            $table->string('custom_rule')->nullable(); // JSON string for custom rules
            $table->timestamps();

            // Ensure no duplicate holidays for the same date and location
            $table->unique(['date', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
}; 