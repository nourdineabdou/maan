<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Le modérateur a accès à tout ce que couvre l'administrateur, à
     * l'exception de la gestion des comptes (section "Gestion utilisateur" :
     * création d'administrateurs/modérateurs). Défini comme la liste
     * complète des permissions moins cette exclusion, pour rester aligné
     * automatiquement si de nouvelles permissions sont ajoutées.
     */
    private const MODERATOR_EXCLUDED_PERMISSIONS = [
        'users.manage',
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
        $moderateur->syncPermissions(array_diff(self::PERMISSIONS, self::MODERATOR_EXCLUDED_PERMISSIONS));
    }
}
