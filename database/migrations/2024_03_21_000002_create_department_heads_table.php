<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('department_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_acting')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure only one active regular head per department
            $table->unique(['department_id', 'is_acting', 'end_date'], 'unique_active_head')
                ->where('is_acting', false)
                ->whereNull('end_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('department_heads');
    }
}; 