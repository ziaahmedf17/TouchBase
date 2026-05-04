<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            if ($user->account_status !== 'active') {
                return redirect()->route('account.pending');
            }
            if ($user->is_suspended) {
                return redirect()->route('account.payment_required');
            }
            if ($user->isOnTrial() && $user->plan_expires_at?->isPast()) {
                return redirect()->route('account.trial_expired');
            }
        }
        return $next($request);
    }
}
