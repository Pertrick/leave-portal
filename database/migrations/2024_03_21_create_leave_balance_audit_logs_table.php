<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_balance_id')->constrained()->onDelete('cascade');
            $table->foreignId('adjusted_by_id')->constrained('users')->onDelete('cascade');
            $table->decimal('previous_balance', 8, 2);
            $table->decimal('new_balance', 8, 2);
            $table->text('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balance_audit_logs');
    }
}; 