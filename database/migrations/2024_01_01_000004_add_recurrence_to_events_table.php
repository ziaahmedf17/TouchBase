<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('recurrence')->default('none')->after('is_annual');
            $table->dropColumn('is_annual');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_annual')->default(false)->after('recurrence');
            $table->dropColumn('recurrence');
        });
    }
};
