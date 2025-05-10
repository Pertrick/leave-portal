<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level');
            $table->string('role_name'); // The role that can approve at this level
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['level', 'role_name']);
        });

        Schema::create('leave_approval_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->constrained()->onDelete('cascade');
            $table->foreignId('approval_level_id')->constrained()->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['leave_id', 'approval_level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_approval_levels');
        Schema::dropIfExists('approval_levels');
    }
}; 