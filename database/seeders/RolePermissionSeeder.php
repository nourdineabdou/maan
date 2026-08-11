<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permissions du cahier des charges (section 25), par ressource.
     */
    private const PERMISSIONS = [
        'members.view',
        'members.create',
        'members.update',
        'members.delete',
        'documents.view',
        'documents.verify',
        'memberships.approve',
        'memberships.reject',
        'memberships.request_completion',
        'memberships.suspend',
        'cards.print',
        'members.export',
        'statistics.view',
        'notifications.send',
        'announcements.manage',
        'support_messages.manage',
        'regions.manage',
        'problematics.manage',
        'users.manage',
        'roles.manage',
        'settings.manage',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $administrateur = Role::firstOrCreate(['name' => 'administrateur', 'guard_name' => 'web']);
        $administrateur->syncPermissions(self::PERMISSIONS);

        // Le membre n'a aucune permission Spatie : son accès est limité par
        // ownership (ses propres données), pas par un système de permissions.
        Role::firstOrCreate(['name' => 'membre', 'guard_name' => 'web']);
    }
}
