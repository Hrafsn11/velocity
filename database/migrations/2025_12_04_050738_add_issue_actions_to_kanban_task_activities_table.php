<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new issue-related action types to the enum
        DB::statement("ALTER TABLE kanban_task_activities 
            MODIFY COLUMN action ENUM(
                'created',
                'updated',
                'moved',
                'assigned',
                'unassigned',
                'commented',
                'attached',
                'deleted_attachment',
                'priority_changed',
                'label_changed',
                'due_date_changed',
                'archived',
                'restored',
                'issue_linked',
                'issue_resolved',
                'issue_closed',
                'issue_reopened',
                'issue_status_changed'
            ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove issue-related action types from the enum
        DB::statement("ALTER TABLE kanban_task_activities 
            MODIFY COLUMN action ENUM(
                'created',
                'updated',
                'moved',
                'assigned',
                'unassigned',
                'commented',
                'attached',
                'deleted_attachment',
                'priority_changed',
                'label_changed',
                'due_date_changed',
                'archived',
                'restored'
            ) NOT NULL");
    }
};
