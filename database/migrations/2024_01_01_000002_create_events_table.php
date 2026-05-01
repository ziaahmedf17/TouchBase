<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            // Types: birthday, anniversary, visit, custom
            $table->string('type');
            $table->string('label')->nullable(); // custom label for 'custom' type
            $table->date('event_date');
            $table->boolean('is_annual')->default(false); // repeats every year
            $table->json('reminder_days'); // e.g. [1, 3, 7]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
