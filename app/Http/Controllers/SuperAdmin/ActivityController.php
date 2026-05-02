<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $action = $request->input('action');

        $query = ActivityLog::with('causer')->latest('created_at');

        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        if ($action) {
            $query->where('action', $action);
        }

        $logs    = $query->paginate(30)->withQueryString();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('superadmin.activity.index', compact('logs', 'search', 'action', 'actions'));
    }
}
