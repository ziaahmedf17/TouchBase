<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->where('tenant_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('slug', ['super_admin', 'admin'])->orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => 'required|exists:roles,id',
        ]);

        $role = Role::find($data['role']);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => $data['password'],
            'tenant_id' => Auth::id(),
        ]);

        $user->assignRole($role);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created.");
    }

    public function edit(User $user)
    {
        $this->authorizeUser($user);
        $roles = Role::whereNotIn('slug', ['super_admin', 'admin'])->orderBy('name')->get();
        $currentRole = $user->roles->first();
        return view('admin.users.edit', compact('user', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUser($user);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role'     => 'required|exists:roles,id',
        ]);

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            ...($data['password'] ? ['password' => $data['password']] : []),
        ]);

        $user->roles()->sync([Role::find($data['role'])->id]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated.");
    }

    public function destroy(User $user)
    {
        $this->authorizeUser($user);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" deleted.");
    }

    private function authorizeUser(User $user): void
    {
        if ($user->tenant_id !== Auth::id()) {
            abort(403);
        }
    }
}
