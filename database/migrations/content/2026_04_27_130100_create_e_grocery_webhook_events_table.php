<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('e_grocery_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->string('event_type', 120)->index();
            $table->timestamp('event_time')->index();
            $table->string('status', 32)->default('pending')->index();
            $table->string('target_url');
            $table->json('payload');
            $table->json('headers')->nullable();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('next_retry_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_retry_at'], 'e_grocery_webhook_status_retry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_grocery_webhook_events');
    }
};
