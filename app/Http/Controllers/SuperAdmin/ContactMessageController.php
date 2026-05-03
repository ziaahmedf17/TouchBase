<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        return view('superadmin.contacts.index', compact('messages'));
    }

    public function show(ContactMessage $contact)
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        return view('superadmin.contacts.show', compact('contact'));
    }

    public function destroy(ContactMessage $contact)
    {
        $contact->delete();
        return redirect()->route('superadmin.contacts.index')
            ->with('success', 'Inquiry deleted.');
    }
}
