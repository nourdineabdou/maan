<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permissions accordées au modérateur : juste de quoi instruire et
     * valider/rejeter les adhésions des autres membres (pas la gestion des
     * comptes, des rôles, des paramètres, etc. — ça reste réservé à
     * l'administrateur).
     */
    private const MODERATOR_PERMISSIONS = [
        'members.view',
        'documents.view',
        'documents.verify',
        'memberships.approve',
        'memberships.reject',
        'support_messages.manage',
        'ambassadors.view',
        'ambassadors.approve',
        'ambassadors.reject',
    ];

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
        'memberships.audit',
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
        'ambassadors.view',
        'ambassadors.approve',
        'ambassadors.reject',
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

        // Idem pour l'ambassadeur : il reste un membre (mêmes accès), le rôle
        // ne sert qu'à distinguer le libellé de sa carte et le workflow de
        // demande/validation du statut ambassadeur.
        Role::firstOrCreate(['name' => 'ambassadeur', 'guard_name' => 'web']);

        // Un membre à qui l'admin a délégué l'instruction des adhésions :
        // accède à la zone /admin (cf. middleware role:administrateur|moderateur)
        // mais seulement aux sections couvertes par ses permissions.
        $moderateur = Role::firstOrCreate(['name' => 'moderateur', 'guard_name' => 'web']);
        $moderateur->syncPermissions(self::MODERATOR_PERMISSIONS);
    }
}
