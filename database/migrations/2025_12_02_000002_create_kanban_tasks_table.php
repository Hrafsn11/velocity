<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_tasks', function (Blueprint $table) {
            $table->ulid('task_id')->primary();
            $table->foreignUlid('board_id')->constrained('kanban_boards', 'board_id')->cascadeOnDelete();
            $table->foreignUlid('workspace_id')->constrained('workspaces', 'workspace_id')->cascadeOnDelete();
            $table->foreignUlid('created_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('label', ['UX', 'Images', 'Info', 'Code Review', 'App', 'Charts & Maps', 'Feature', 'Bug'])->nullable();
            $table->date('due_date')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['board_id', 'position']);
            $table->index(['workspace_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_tasks');
    }
};
