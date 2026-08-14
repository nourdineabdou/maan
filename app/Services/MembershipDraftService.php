<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\User;

class MembershipDraftService
{
    public function __construct(
        private readonly RegistrationNumberGeneratorService $registrationNumberGenerator,
    ) {
    }

    /**
     * Retourne le dossier d'adhésion en cours du membre, ou en crée un
     * nouveau (statut "pending", non soumis) s'il n'en a pas encore.
     */
    public function draftFor(User $user): Membership
    {
        $membership = $user->latestMembership;

        if ($membership) {
            return $membership;
        }

        return Membership::create([
            'user_id' => $user->id,
            'registration_number' => $this->registrationNumberGenerator->generate(),
            'status' => 'pending',
        ]);
    }

    /**
     * Champs et documents obligatoires avant de pouvoir soumettre une
     * demande d'adhésion. Il s'agit des mêmes informations que celles
     * demandées à l'inscription (nom, genre, NNI, wilaya, moughataa, photo,
     * CNI recto/verso) : cette vérification ne sert donc en pratique qu'au
     * moment de la re-soumission après un rejet. Le reste (informations
     * professionnelles, adresse, diplôme, CV, problématiques...) reste
     * facultatif et peut être complété à tout moment depuis le compte.
     *
     * @return array<int, string>
     */
    public function missingFieldsFor(User $user): array
    {
        $profile = $user->profile;
        $missing = [];

        if (! $profile?->first_name || ! $profile?->last_name) {
            $missing[] = __('profile.field_name');
        }

        if (! $profile?->gender || $profile->gender === 'unspecified') {
            $missing[] = __('memberships.field_gender');
        }

        if (! $profile?->nni) {
            $missing[] = __('memberships.field_nni');
        }

        if (! $profile?->region_id) {
            $missing[] = __('memberships.field_region');
        }

        if (! $profile?->moughataa_id) {
            $missing[] = __('memberships.field_moughataa');
        }

        if (! $profile?->photo_path) {
            $missing[] = __('profile.label_photo');
        }

        $documentTypes = $user->latestMembership?->documents->pluck('document_type') ?? collect();

        foreach (['identity_card_front', 'identity_card_back', 'member_photo'] as $requiredType) {
            if (! $documentTypes->contains($requiredType)) {
                $missing[] = __('documents.type_'.$requiredType);
            }
        }

        return $missing;
    }
}
