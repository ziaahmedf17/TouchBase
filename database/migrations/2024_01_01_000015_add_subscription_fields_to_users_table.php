<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan_type')->nullable()->after('payment_submitted_at');       // monthly|yearly|lifetime
            $table->timestamp('plan_started_at')->nullable()->after('plan_type');
            $table->timestamp('plan_expires_at')->nullable()->after('plan_started_at');   // null = lifetime / not set
            $table->boolean('is_suspended')->default(false)->after('plan_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan_type', 'plan_started_at', 'plan_expires_at', 'is_suspended']);
        });
    }
};
