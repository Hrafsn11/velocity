<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_task_comments', function (Blueprint $table) {
            $table->ulid('comment_id')->primary();
            $table->foreignUlid('task_id')->constrained('kanban_tasks', 'task_id')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
            $table->softDeletes();

            $table->index('task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_task_comments');
    }
};
