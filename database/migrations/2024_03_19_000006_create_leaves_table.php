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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('calendar_days'); // Total calendar days including holidays
            $table->integer('working_days'); // Actual working days excluding holidays
            $table->text('reason');
            $table->text('applicant_comment')->nullable(); // optional notes by applicant
            $table->string('replacement_staff_name')->nullable(); // e.g., "James Smith"
            $table->string('replacement_staff_phone')->nullable(); // e.g., "08012345678"
            $table->string('attachment')->nullable();
            $table->string('current_approval_level')->nullable(); //draft,null, supervisor, hod
            $table->string('current_approval_id')->nullable();//2 (or NULL if finished) 
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft'); // Leave status: draft, pending, approved, rejected
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
}; 