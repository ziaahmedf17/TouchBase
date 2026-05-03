<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        Announcement::where('is_active', true)->update(['is_active' => false]);

        Announcement::create([
            'message'    => $request->message,
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Announcement published to all admins.');
    }

    public function destroy()
    {
        Announcement::where('is_active', true)->update(['is_active' => false]);

        return back()->with('success', 'Announcement cleared.');
    }
}
