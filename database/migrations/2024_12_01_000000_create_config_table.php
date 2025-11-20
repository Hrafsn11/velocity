<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old table if exists
        Schema::dropIfExists('app_settings');
        
        // Create new config table
        Schema::create('config', function (Blueprint $table) {
            $table->id('config_id');
            $table->string('config_name', 100);
            $table->text('config_value')->nullable();
            $table->timestamps();
            
            $table->unique('config_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config');
    }
};
