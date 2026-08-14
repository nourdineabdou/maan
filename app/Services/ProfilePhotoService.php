<?php

namespace App\Services;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Met à jour la photo de profil d'un membre. Cette photo vaut aussi comme
 * document "photo du membre" requis pour l'adhésion : on la copie comme
 * document tant qu'il n'a pas déjà été fourni, pour éviter de demander la
 * même photo deux fois. Partagée entre les couches Web et API.
 */
class ProfilePhotoService
{
    public function __construct(
        private readonly MembershipDraftService $draftService,
    ) {
    }

    public function update(User $user, UploadedFile $file): MemberProfile
    {
        $profile = $user->profile;

        if ($profile->photo_path) {
            Storage::disk('public')->delete($profile->photo_path);
        }

        $profile->update([
            'photo_path' => $file->store('profiles', 'public'),
        ]);

        $membership = $this->draftService->draftFor($user);

        if (! $membership->documents()->where('document_type', 'member_photo')->exists()) {
            $membership->documents()->create([
                'document_type' => 'member_photo',
                'file_path' => $file->store('documents', 'public'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return $profile->fresh();
    }
}
