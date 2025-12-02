<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('task_id')->constrained('kanban_tasks', 'task_id')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('employee_profiles', 'employee_id')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_task_assignees');
    }
};
