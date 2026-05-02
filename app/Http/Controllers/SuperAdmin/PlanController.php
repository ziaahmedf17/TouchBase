<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('superadmin.plans.index', compact('plans'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'price' => 'required|numeric|min:0|max:9999999',
        ]);
        $old = 'Rs. ' . number_format($plan->price, 0);
        $plan->update(['price' => $request->price]);

        ActivityLog::record(
            'price_updated',
            "Updated {$plan->name} plan price from {$old} to {$plan->formattedPrice()}.",
            $plan->id,
            'plan'
        );

        return back()->with('success', "{$plan->name} plan price updated to {$plan->formattedPrice()}.");
    }
}
