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
        Schema::create('leave_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained('leaves')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('leaves')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');

            $table->string('level_id'); //supervisor or hod
            $table->integer('sequence')->default(1); 
            $table->enum('status', ['approved', 'rejected', 'pending'])->default('pending');
            $table->text('remark')->nullable();
            $table->timestamp('action_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_approvals');
    }
}; 