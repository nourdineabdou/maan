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
}
