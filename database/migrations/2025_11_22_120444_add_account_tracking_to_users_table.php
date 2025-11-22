<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active')->after('last_login_at');
            $table->unsignedInteger('login_count')->default(0)->after('account_status');
            $table->timestamp('suspended_at')->nullable()->after('login_count');
            $table->text('suspended_reason')->nullable()->after('suspended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'account_status',
                'login_count',
                'suspended_at',
                'suspended_reason',
            ]);
        });
    }
};
