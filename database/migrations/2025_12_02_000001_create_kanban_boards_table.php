<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_boards', function (Blueprint $table) {
            $table->ulid('board_id')->primary();
            $table->foreignUlid('workspace_id')->constrained('workspaces', 'workspace_id')->cascadeOnDelete();
            $table->string('title');
            $table->string('color', 7)->default('#6366f1');
            $table->integer('position')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['workspace_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_boards');
    }
};
