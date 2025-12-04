<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->ulid('issue_id')->primary();
            
            // Workspace relationship
            $table->foreignUlid('workspace_id')
                ->constrained('workspaces', 'workspace_id')
                ->cascadeOnDelete();
            
            // Risk relationship (nullable - can report issue directly)
            $table->foreignUlid('risk_id')
                ->nullable()
                ->constrained('risks', 'risk_id')
                ->nullOnDelete();
            
            // Issue details
            $table->string('code', 10); // ISS001, ISS002...
            $table->string('title', 255);
            $table->text('description')->nullable();
            
            // Priority & Severity
            $table->tinyInteger('priority')->default(3); // 1-5
            $table->tinyInteger('severity')->default(3); // 1-5
            
            // Status
            $table->enum('status', [
                'open',
                'in_progress',
                'resolved',
                'closed',
                'reopened'
            ])->default('open');
            
            // Assignment
            $table->foreignUlid('assignee_id')
                ->nullable()
                ->constrained('employee_profiles', 'employee_id')
                ->nullOnDelete();
            
            // Deadline
            $table->date('deadline')->nullable();
            
            // Audit
            $table->foreignUlid('created_by')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'code']);
            $table->index('risk_id');
            $table->index('assignee_id');
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
