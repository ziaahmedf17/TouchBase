<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $groups = Permission::distinct()->orderBy('group')->pluck('group');
        return view('admin.permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:permissions,slug|regex:/^[a-z0-9\.\-]+$/',
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create($data);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$permission->name}\" created.");
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->orderBy('group')->pluck('group');
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => "required|string|max:100|unique:permissions,slug,{$permission->id}|regex:/^[a-z0-9\.\-]+$/",
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update($data);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$permission->name}\" updated.");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$permission->name}\" deleted.");
    }
}
