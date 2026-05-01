<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
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

// Calendar
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
