<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\MembershipStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipApprovalService
{
    public function __construct(
        private readonly MatriculeGeneratorService $matriculeGenerator,
    ) {
    }

    /**
     * Valide une adhésion : attribue le matricule, active la carte et trace
     * le changement de statut. Enveloppé dans une transaction pour rester
     * cohérent même en cas d'erreur en cours de route.
     */
    public function approve(Membership $membership, ?User $reviewer = null): Membership
    {
        return DB::transaction(function () use ($membership, $reviewer) {
            $oldStatus = $membership->status;

            $membership->update([
                'status' => 'approved',
                'member_number' => $membership->member_number ?? $this->matriculeGenerator->generate(),
                'approved_at' => now(),
                'reviewed_by' => $reviewer?->id,
                'qr_token' => $membership->qr_token ?? Str::random(40),
                'card_generated_at' => now(),
                'card_is_active' => true,
            ]);

            MembershipStatusHistory::create([
                'membership_id' => $membership->id,
                'old_status' => $oldStatus,
                'new_status' => 'approved',
                'changed_by' => $reviewer?->id,
            ]);

            return $membership->refresh();
        });
    }

    /**
     * Rejette une adhésion avec un motif obligatoire.
     */
    public function reject(Membership $membership, string $reason, ?User $reviewer = null): Membership
    {
        return DB::transaction(function () use ($membership, $reason, $reviewer) {
            $oldStatus = $membership->status;

            $membership->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'rejected_at' => now(),
                'reviewed_by' => $reviewer?->id,
            ]);

            MembershipStatusHistory::create([
                'membership_id' => $membership->id,
                'old_status' => $oldStatus,
                'new_status' => 'rejected',
                'comment' => $reason,
                'changed_by' => $reviewer?->id,
            ]);

            return $membership->refresh();
        });
    }
}
