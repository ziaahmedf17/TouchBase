<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('message');
            $table->boolean('is_read')->default(false);
            // The calendar date this notification was triggered for (deduplication key)
            $table->date('triggered_date');
            $table->timestamps();

            // Prevent duplicate notifications for the same event on the same trigger date
            $table->unique(['event_id', 'triggered_date'], 'unique_event_trigger');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
