<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breadcrumbs', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('title');
            $table->foreignId('parent_id')->nullable()->constrained('breadcrumbs')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('icon')->nullable();
            $table->json('roles')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breadcrumbs');
    }
}; 