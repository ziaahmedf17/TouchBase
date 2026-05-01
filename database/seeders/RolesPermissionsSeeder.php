<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ───────────────────────────────────────────
        $permissions = [
            // Clients
            ['name' => 'View Clients',   'slug' => 'clients.view',   'group' => 'clients',       'description' => 'View the client list and profiles'],
            ['name' => 'Add Clients',    'slug' => 'clients.create', 'group' => 'clients',       'description' => 'Create new clients'],
            ['name' => 'Edit Clients',   'slug' => 'clients.edit',   'group' => 'clients',       'description' => 'Update existing client details'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'group' => 'clients',       'description' => 'Remove clients permanently'],

            // Events
            ['name' => 'View Events',    'slug' => 'events.view',    'group' => 'events',        'description' => 'View client events'],
            ['name' => 'Add Events',     'slug' => 'events.create',  'group' => 'events',        'description' => 'Add new events to clients'],
            ['name' => 'Edit Events',    'slug' => 'events.edit',    'group' => 'events',        'description' => 'Update existing events'],
            ['name' => 'Delete Events',  'slug' => 'events.delete',  'group' => 'events',        'description' => 'Remove events'],

            // Notifications
            ['name' => 'View Notifications',   'slug' => 'notifications.view',   'group' => 'notifications', 'description' => 'View notification alerts'],
            ['name' => 'Manage Notifications', 'slug' => 'notifications.manage', 'group' => 'notifications', 'description' => 'Mark read / delete notifications'],

            // Interactions
            ['name' => 'Log Interactions',    'slug' => 'interactions.create', 'group' => 'interactions', 'description' => 'Log client contact interactions'],
            ['name' => 'Delete Interactions', 'slug' => 'interactions.delete', 'group' => 'interactions', 'description' => 'Remove interaction records'],

            // Admin
            ['name' => 'Manage Roles',       'slug' => 'admin.roles',       'group' => 'admin', 'description' => 'Create, edit, delete roles'],
            ['name' => 'Manage Permissions', 'slug' => 'admin.permissions', 'group' => 'admin', 'description' => 'Create, edit, delete permissions'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        // ── Roles ─────────────────────────────────────────────────
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Full access to everything']
        );
        $admin->permissions()->sync(Permission::pluck('id'));

        $manager = Role::firstOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Manager', 'description' => 'Manage clients, events, and notifications']
        );
        $manager->permissions()->sync(
            Permission::whereIn('slug', [
                'clients.view', 'clients.create', 'clients.edit',
                'events.view', 'events.create', 'events.edit',
                'notifications.view', 'notifications.manage',
                'interactions.create',
            ])->pluck('id')
        );

        $user = Role::firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'User', 'description' => 'View-only access']
        );
        $user->permissions()->sync(
            Permission::whereIn('slug', [
                'clients.view', 'events.view', 'notifications.view',
            ])->pluck('id')
        );
    }
}
