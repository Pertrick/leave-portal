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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Appearance Settings
            $table->string('theme')->default('light'); // light, dark, system
            $table->string('color_scheme')->default('blue'); // blue, green, purple, etc.
            $table->boolean('compact_mode')->default(false);
            
            // Notification Settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('leave_approval_notifications')->default(true);
            $table->boolean('leave_request_notifications')->default(true);
            $table->boolean('system_notifications')->default(true);
            
            // Display Settings
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('24h'); // 12h, 24h
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            
            // Dashboard Settings
            $table->json('dashboard_widgets')->nullable(); // Store enabled widgets
            $table->json('quick_links')->nullable(); // Store user's quick links
            
            // Privacy Settings
            $table->boolean('show_profile')->default(true);
            $table->boolean('show_leave_balance')->default(true);
            $table->boolean('show_contact_info')->default(true);
            
            // Other Settings
            $table->json('custom_settings')->nullable(); // For any additional settings
            $table->timestamps();
            
            // Ensure one settings record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
}; 