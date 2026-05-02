<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // monthly | yearly | lifetime
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');           // 30 | 365 | 0 (lifetime)
            $table->timestamps();
        });

        DB::table('plans')->insert([
            ['slug' => 'monthly',  'name' => 'Monthly',  'price' => 999,   'duration_days' => 30,  'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'yearly',   'name' => 'Yearly',   'price' => 9999,  'duration_days' => 365, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'lifetime', 'name' => 'Lifetime', 'price' => 24999, 'duration_days' => 0,   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
