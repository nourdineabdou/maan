<?php

namespace App\Services;

use App\Models\AmbassadorRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AmbassadorApprovalService
{
    /**
     * Crée ou réutilise la demande "devenir ambassadeur" du membre : une
     * seule ligne par utilisateur, comme pour Membership/MembershipDraftService.
     */
    public function requestFor(User $user): AmbassadorRequest
    {
        $request = $user->latestAmbassadorRequest;

        if ($request && ! $request->canBeSubmitted()) {
            return $request;
        }

        if ($request) {
            $request->update([
                'status' => 'pending',
                'submitted_at' => now(),
                'rejection_reason' => null,
                'rejected_at' => null,
            ]);

            return $request->refresh();
        }

        return AmbassadorRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
    }

    public function approve(AmbassadorRequest $request, ?User $reviewer = null): AmbassadorRequest
    {
        return DB::transaction(function () use ($request, $reviewer) {
            $request->update([
                'status' => 'approved',
                'approved_at' => now(),
                'reviewed_by' => $reviewer?->id,
            ]);

            if (! $request->user->hasRole('ambassadeur')) {
                $request->user->assignRole('ambassadeur');
            }

            return $request->refresh();
        });
    }

    /**
     * Rejette une demande, y compris une demande déjà approuvée (révocation
     * du statut ambassadeur) : le rôle doit toujours refléter le dernier statut.
     */
    public function reject(AmbassadorRequest $request, string $reason, ?User $reviewer = null): AmbassadorRequest
    {
        return DB::transaction(function () use ($request, $reason, $reviewer) {
            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'rejected_at' => now(),
                'reviewed_by' => $reviewer?->id,
            ]);

            if ($request->user->hasRole('ambassadeur')) {
                $request->user->removeRole('ambassadeur');
            }

            return $request->refresh();
        });
    }
}
