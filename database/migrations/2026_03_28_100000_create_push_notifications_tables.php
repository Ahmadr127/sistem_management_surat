<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_device_tokens')) {
            Schema::create('user_device_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('device_token', 500)->unique();
                $table->enum('device_type', ['android', 'ios', 'web'])->nullable();
                $table->timestamps();

                $table->index('user_id');
            });
        }

        if (! Schema::hasTable('push_notifications')) {
            Schema::create('push_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->text('body');
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('read_at');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('user_device_tokens');
    }
};
