<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            // Link to affected task
            $table->foreignUlid('linked_task_id')
                ->nullable()
                ->after('risk_id')
                ->constrained('kanban_tasks', 'task_id')
                ->nullOnDelete();
            
            // Resolution tracking
            $table->timestamp('resolved_at')->nullable()->after('deadline');
            $table->foreignUlid('resolved_by')
                ->nullable()
                ->after('resolved_at')
                ->constrained('users', 'user_id')
                ->nullOnDelete();
            
            // Index
            $table->index('linked_task_id');
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['linked_task_id']);
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['linked_task_id', 'resolved_at', 'resolved_by']);
        });
    }
};
