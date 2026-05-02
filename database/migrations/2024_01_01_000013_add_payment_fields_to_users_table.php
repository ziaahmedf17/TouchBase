<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_status')->default('payment_submitted')->after('business_description');
            $table->string('payment_screenshot')->nullable()->after('account_status');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_screenshot');
        });

        // Existing admins and super admins were created before this payment
        // system existed — mark them as already active.
        DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->whereIn('roles.slug', ['admin', 'super_admin'])
            ->update(['users.account_status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_status', 'payment_screenshot', 'payment_submitted_at']);
        });
    }
};
