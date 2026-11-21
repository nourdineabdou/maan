<?php

namespace App\Services;

use App\Models\MemberDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Suppression définitive d'un compte membre et de ses données, initiée par
 * le membre lui-même depuis l'app (exigence App Store 5.1.1(v)).
 *
 * Les lignes liées (profil, adhésions, documents, déclarations, messages,
 * notifications, jetons push) partent en cascade via les clés étrangères ;
 * reste à effacer les fichiers stockés sur disque et les jetons/abonnements
 * polymorphes, qui n'ont pas de clé étrangère.
 */
class AccountDeletionService
{
    public function delete(User $user): void
    {
        $paths = $this->storedFilePaths($user);

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->pushSubscriptions()->delete();
            $user->delete();
        });

        // Après le commit seulement : si la transaction échoue, on ne perd
        // pas les fichiers d'un compte qui existe toujours.
        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }

    /**
     * @return array<int, string>
     */
    private function storedFilePaths(User $user): array
    {
        $paths = MemberDocument::query()
            ->whereIn('membership_id', $user->memberships()->select('id'))
            ->pluck('file_path')
            ->all();

        if ($photo = $user->profile?->photo_path) {
            $paths[] = $photo;
        }

        return array_values(array_filter(array_unique($paths)));
    }
}
