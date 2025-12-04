<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table) {
            $table->ulid('change_request_id')->primary();
            
            // Workspace relationship (what CR affects)
            $table->foreignUlid('workspace_id')
                ->constrained('workspaces', 'workspace_id')
                ->cascadeOnDelete();
            
            // Issue relationship (optional)
            $table->foreignUlid('issue_id')
                ->nullable()
                ->constrained('issues', 'issue_id')
                ->nullOnDelete();
            
            // CR details
            $table->string('code', 10); // CR001, CR002...
            $table->string('title', 255);
            $table->text('description')->nullable();
            
            // Change type (ONLY Timeline & Resource!)
            $table->enum('type', ['timeline', 'resource']);
            
            // Timeline impact (if type = timeline)
            $table->integer('timeline_extension_days')->nullable();
            $table->date('current_end_date')->nullable(); // snapshot
            $table->date('proposed_end_date')->nullable(); // new date
            
            // Resource impact (if type = resource)
            $table->json('members_to_add')->nullable(); // [employee_ids]
            $table->json('members_to_remove')->nullable(); // [employee_ids]
            
            // Justification
            $table->text('justification');
            
            // Approval workflow
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'implemented'
            ])->default('pending');
            
            $table->foreignUlid('approved_by')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();
            
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Requester
            $table->foreignUlid('requested_by')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'code']);
            $table->index('issue_id');
            $table->index('type');
            $table->index('approved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
