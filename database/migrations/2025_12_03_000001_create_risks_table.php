<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->ulid('risk_id')->primary();
            
            // Workspace relationship (CRITICAL!)
            $table->foreignUlid('workspace_id')
                ->constrained('workspaces', 'workspace_id')
                ->cascadeOnDelete();
            
            // Risk details
            $table->string('code', 10); // R001, R002...
            $table->text('description');
            $table->text('cause')->nullable();
            $table->string('category', 50); // Technical, SDM, Financial, Timeline
            $table->string('affected_module', 100)->nullable();
            
            // Risk assessment
            $table->tinyInteger('probability')->default(3); // 1-5
            $table->tinyInteger('impact')->default(3); // 1-5
            $table->tinyInteger('score')->storedAs('probability * impact'); // Calculated
            $table->string('urgency', 20); // Critical/High/Medium/Low
            
            // Status tracking
            $table->enum('status', [
                'active',
                'monitoring',
                'mitigated',
                'materialized',
                'closed'
            ])->default('active');
            
            // Mitigation
            $table->text('mitigation_actions')->nullable();
            
            // Audit
            $table->foreignUlid('created_by')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'code']);
            $table->index('urgency');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
