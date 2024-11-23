<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable(false);
            $table->string('description')->nullable(false);
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'COMPLETED', 'REJECTED'])->nullable(false)->default('OPEN');
            $table->foreignId('building_id')->nullable(false)->constrained('buildings')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable(false)->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_user_id')->nullable(false)->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
