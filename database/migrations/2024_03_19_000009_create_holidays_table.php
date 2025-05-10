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
            $table->date('date');
            $table->enum('type', ['public', 'company', 'location'])->default('public');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_recurring')->default(false); // For holidays that occur every year
            $table->boolean('is_active')->default(true);
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