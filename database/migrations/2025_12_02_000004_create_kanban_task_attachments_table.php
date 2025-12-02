<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_task_attachments', function (Blueprint $table) {
            $table->ulid('attachment_id')->primary();
            $table->foreignUlid('task_id')->constrained('kanban_tasks', 'task_id')->cascadeOnDelete();
            $table->foreignUlid('uploaded_by')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index('task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_task_attachments');
    }
};
