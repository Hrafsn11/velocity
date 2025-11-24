<?php

use App\Enums\WorkspaceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->ulid('workspace_id')->primary();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignUlid('manager_id')
                ->constrained('employee_profiles', 'employee_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('status', 20)->default(WorkspaceStatus::PLANNING->value);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};

