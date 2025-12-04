<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_activities', function (Blueprint $table) {
            $table->ulid('activity_id')->primary();
            
            // Polymorphic relationship (Risk, Issue, or ChangeRequest)
            $table->ulidMorphs('subject'); // subject_id, subject_type
            
            // Activity details
            $table->string('activity_type', 50); // created, updated, converted, status_changed, etc.
            $table->text('description')->nullable();
            $table->json('changes')->nullable(); // before/after values
            
            // User who performed action
            $table->foreignUlid('user_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            
            $table->timestamps();
            
            // Indexes (ulidMorphs already creates subject index)
            $table->index('user_id');
            $table->index('activity_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_activities');
    }
};
