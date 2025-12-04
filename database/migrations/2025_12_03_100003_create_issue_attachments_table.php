<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_attachments', function (Blueprint $table) {
            $table->ulid('attachment_id')->primary();
            
            $table->foreignUlid('issue_id')
                ->constrained('issues', 'issue_id')
                ->cascadeOnDelete();
            
            $table->foreignUlid('comment_id')
                ->nullable()
                ->constrained('issue_comments', 'comment_id')
                ->cascadeOnDelete();
            
            $table->foreignUlid('uploaded_by')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 50);
            $table->integer('file_size'); // in bytes
            
            $table->timestamps();
            
            // Indexes
            $table->index('issue_id');
            $table->index('comment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_attachments');
    }
};
