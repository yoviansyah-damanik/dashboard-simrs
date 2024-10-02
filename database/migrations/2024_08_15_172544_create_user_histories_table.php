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
        Schema::create('user_histories', function (Blueprint $table) {
            $table->uuid('user_id')
                ->reference('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamp('login_at');
            $table->string('ip_address');
            $table->string('browser');
            $table->string('platform');
            $table->string('device');
            $table->string('device_type');
            $table->boolean('is_robot')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_histories');
    }
};
