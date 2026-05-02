<?php

namespace App\Providers;

use App\Http\Middleware\AccountActive;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');

        // Register middleware aliases
        $this->app['router']->aliasMiddleware('role',           RoleMiddleware::class);
        $this->app['router']->aliasMiddleware('permission',     PermissionMiddleware::class);
        $this->app['router']->aliasMiddleware('account_active', AccountActive::class);

        Gate::before(function (User $user) {
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return true;
            }
        });

        Gate::after(function (User $user, string $ability) {
            $user->loadMissing('roles.permissions');
            return $user->hasPermission($ability);
        });
    }
}
