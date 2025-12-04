<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_comments', function (Blueprint $table) {
            $table->ulid('comment_id')->primary();
            
            $table->foreignUlid('issue_id')
                ->constrained('issues', 'issue_id')
                ->cascadeOnDelete();
            
            $table->foreignUlid('user_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            
            $table->text('comment');
            $table->boolean('is_resolution')->default(false); // Mark resolution comment
            
            $table->timestamps();
            
            // Indexes
            $table->index(['issue_id', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_comments');
    }
};
