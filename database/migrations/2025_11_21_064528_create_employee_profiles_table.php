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
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->ulid('employee_id')->primary();
            // Ensure foreign key references users.user_id (ULID primary key)
            $table->foreignUlid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->enum('role', ['programmer', 'designer', 'qa', 'analyst', 'manager'])->default('programmer');
            $table->string('specialization');
            $table->enum('level', ['intern', 'junior', 'middle', 'senior', 'lead'])->default('junior');
            $table->json('skills');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
