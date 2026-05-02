<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SuperAdmin\AdminController as SuperAdminController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PaymentAccountController;
use App\Http\Controllers\SuperAdmin\PaymentController;
use App\Http\Controllers\SuperAdmin\ActivityController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\TicketController as SuperAdminTicketController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Guest-only routes ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',              [LoginController::class,    'showLogin'])->name('login');
    Route::post('/login',             [LoginController::class,    'login']);
    Route::get('/register',           [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register',          [RegisterController::class, 'storeStep1']);
    Route::get('/register/payment',   [RegisterController::class, 'showPayment'])->name('register.payment');
    Route::post('/register/payment',  [RegisterController::class, 'storePayment'])->name('register.payment.store');
});

// ── Authenticated routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Logout & password change (no account_active needed)
    Route::post('/logout',           [LoginController::class,  'logout'])->name('logout');
    Route::get('/password/change',   [PasswordController::class, 'showForm'])->name('password.change');
    Route::put('/password/change',   [PasswordController::class, 'update'])->name('password.update');

    // Pending approval page — accessible before account is active
    Route::get('/pending',            [RegisterController::class, 'showPending'])->name('account.pending');
    Route::post('/pending/resubmit',  [RegisterController::class, 'resubmitPayment'])->name('account.resubmit');
    Route::post('/pending/contact',   [RegisterController::class, 'updateContact'])->name('account.contact');

    // Suspended admin renewal — accessible while suspended
    Route::get('/payment-required',        [RegisterController::class, 'showPaymentRequired'])->name('account.payment_required');
    Route::post('/payment-required/renew', [RegisterController::class, 'resubmitRenewal'])->name('account.renewal');

    // ── CRM routes (require active account for admins) ────────────
    Route::middleware('account_active')->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/update-alerts', [DashboardController::class, 'updateAlerts'])->name('alerts.update');

        // Clients
        Route::resource('clients', ClientController::class);

        // Events (nested under client)
        Route::prefix('clients/{client}/events')->name('clients.events.')->group(function () {
            Route::get('/create',        [EventController::class, 'create'])->name('create');
            Route::post('/',             [EventController::class, 'store'])->name('store');
            Route::get('/{event}/edit',  [EventController::class, 'edit'])->name('edit');
            Route::put('/{event}',       [EventController::class, 'update'])->name('update');
            Route::delete('/{event}',    [EventController::class, 'destroy'])->name('destroy');
        });

        // Notifications
        Route::get('/notifications',                           [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read',      [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all',                 [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
        Route::get('/notifications/unread-count',              [NotificationController::class, 'unreadCount'])->name('notifications.count');
        Route::delete('/notifications/{notification}',         [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Interactions
        Route::post('/interactions',                 [InteractionController::class, 'store'])->name('interactions.store');
        Route::put('/interactions/{interaction}',    [InteractionController::class, 'update'])->name('interactions.update');
        Route::delete('/interactions/{interaction}', [InteractionController::class, 'destroy'])->name('interactions.destroy');

        // Calendar
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

        // ── Admin panel ──────────────────────────────────────────
        Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::resource('users',       UserController::class);
            Route::resource('roles',       RoleController::class);
            Route::resource('permissions', PermissionController::class);
            Route::get('tickets',          [TicketController::class, 'index'])->name('tickets.index');
            Route::get('tickets/create',   [TicketController::class, 'create'])->name('tickets.create');
            Route::post('tickets',         [TicketController::class, 'store'])->name('tickets.store');
            Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        });

    }); // end account_active

    // ── Super Admin ───────────────────────────────────────────────
    Route::middleware('role:super_admin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

        // Admins
        Route::resource('admins', SuperAdminController::class);
        Route::get('admins/{admin}/clients',    [SuperAdminController::class, 'clients'])->name('admins.clients');
        Route::post('admins/{admin}/suspend',   [SuperAdminController::class, 'suspend'])->name('admins.suspend');
        Route::post('admins/{admin}/unsuspend', [SuperAdminController::class, 'unsuspend'])->name('admins.unsuspend');
        Route::post('admins/{admin}/set-plan',  [SuperAdminController::class, 'setPlan'])->name('admins.setPlan');

        // Support tickets
        Route::get('tickets',          [SuperAdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('tickets/{ticket}', [SuperAdminTicketController::class, 'show'])->name('tickets.show');
        Route::put('tickets/{ticket}', [SuperAdminTicketController::class, 'update'])->name('tickets.update');

        // Payment submissions
        Route::get('payments/screenshot/{filename}', [PaymentController::class, 'screenshot'])->name('payments.screenshot');
        Route::get('payments',               [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{admin}',       [PaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{admin}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('payments/{admin}/reject',  [PaymentController::class, 'reject'])->name('payments.reject');

        // Bank accounts
        Route::resource('payment-accounts', PaymentAccountController::class);

        // Plans (price management)
        Route::get('plans',        [PlanController::class, 'index'])->name('plans.index');
        Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');

        // Activity log
        Route::get('activity', [ActivityController::class, 'index'])->name('activity.index');
    });

});
