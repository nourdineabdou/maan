<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Remise à zéro des données de test avant le lancement.
 *
 * GARDE : les comptes "administrateur" (avec leur profil) et les
 * référentiels (wilayas, moughataas, communes, problématiques, rôles et
 * permissions).
 *
 * SUPPRIME : tous les autres comptes (membres, ambassadeurs, modérateurs)
 * et leurs données, toutes les adhésions, documents, déclarations,
 * messages, notifications, campagnes, annonces, demandes d'ambassadeur et
 * de suppression de compte, les fichiers associés sur le disque, et remet
 * à zéro les compteurs de matricules / numéros d'inscription.
 *
 * Volontairement absent de DatabaseSeeder. Usage (faire une sauvegarde de
 * la base avant !) :
 *   php artisan db:seed --class=ResetTestDataSeeder --force
 */
class ResetTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminIds = User::role('administrateur')->pluck('id')->all();

        if ($adminIds === []) {
            $this->command->error('Aucun compte administrateur trouvé : opération annulée pour ne pas perdre l\'accès au back-office.');

            return;
        }

        $userType = (new User)->getMorphClass();
        $usersToDelete = DB::table('users')->whereNotIn('id', $adminIds);

        $this->command->warn('Comptes administrateur conservés :');
        foreach (User::whereIn('id', $adminIds)->get(['id', 'name', 'email', 'phone']) as $admin) {
            $this->command->line("  #{$admin->id} {$admin->name} ({$admin->email} / {$admin->phone})");
        }

        $this->command->warn('Sera supprimé définitivement :');
        $this->command->table(['Données', 'Nombre'], [
            ['Comptes (membres, ambassadeurs, modérateurs)', (clone $usersToDelete)->count()],
            ['Adhésions', DB::table('memberships')->count()],
            ['Documents', DB::table('member_documents')->count()],
            ['Messages de support', DB::table('member_messages')->count()],
            ['Notifications', DB::table('notifications')->count()],
            ['Annonces', DB::table('announcements')->count()],
            ['Demandes d\'ambassadeur', DB::table('ambassador_requests')->count()],
            ['Demandes de suppression de compte', DB::table('account_deletion_requests')->count()],
        ]);

        if (! $this->command->confirm('Confirmez-vous la suppression DÉFINITIVE de ces données ? (pensez à sauvegarder la base avant)', false)) {
            $this->command->info('Opération annulée, rien n\'a été supprimé.');

            return;
        }

        // Chemins des fichiers à effacer après le commit (sinon, en cas
        // d'échec de la transaction, on perdrait des fichiers encore référencés).
        $files = array_merge(
            DB::table('member_documents')->pluck('file_path')->all(),
            DB::table('member_profiles')->whereNotIn('user_id', $adminIds)->whereNotNull('photo_path')->pluck('photo_path')->all(),
            DB::table('announcement_images')->pluck('file_path')->all(),
        );

        DB::transaction(function () use ($adminIds, $userType) {
            DB::table('notification_recipients')->delete();
            DB::table('notifications')->delete();
            DB::table('message_campaigns')->delete();

            DB::table('announcement_images')->delete();
            DB::table('announcements')->delete();

            DB::table('member_message_replies')->delete();
            DB::table('member_messages')->delete();

            // Cascade : documents, historiques de statut, problématiques et besoins déclarés.
            DB::table('memberships')->delete();

            DB::table('ambassador_requests')->delete();
            DB::table('account_deletion_requests')->delete();

            // Tables polymorphes / sans clé étrangère vers users.
            DB::table('personal_access_tokens')->where('tokenable_type', $userType)->whereNotIn('tokenable_id', $adminIds)->delete();
            DB::table(config('webpush.table_name', 'push_subscriptions'))->where('subscribable_type', $userType)->whereNotIn('subscribable_id', $adminIds)->delete();
            DB::table('model_has_roles')->where('model_type', $userType)->whereNotIn('model_id', $adminIds)->delete();
            DB::table('model_has_permissions')->where('model_type', $userType)->whereNotIn('model_id', $adminIds)->delete();
            DB::table('sessions')->whereNotNull('user_id')->whereNotIn('user_id', $adminIds)->delete();
            DB::table('password_reset_tokens')->whereNotIn('email', DB::table('users')->whereIn('id', $adminIds)->whereNotNull('email')->pluck('email'))->delete();

            // Cascade : profils, jetons push Expo, destinataires restants.
            DB::table('users')->whereNotIn('id', $adminIds)->delete();

            DB::table('matricule_sequences')->delete();
            DB::table('registration_sequences')->delete();
        });

        $files = array_values(array_unique(array_filter($files)));

        if ($files !== []) {
            Storage::disk('public')->delete($files);
        }

        $this->command->info('Données de test supprimées ('.count($files).' fichier(s) effacé(s)). Comptes administrateur conservés : '.count($adminIds).'.');
    }
}
