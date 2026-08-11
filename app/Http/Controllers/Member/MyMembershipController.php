<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use App\Models\MembershipStatusHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyMembershipController extends Controller
{
    public function show(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        return view('profile.membership', [
            'membership' => $membership->load('statusHistories'),
            'missingFields' => $this->missingFields($request->user()),
        ]);
    }

    public function submit(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): RedirectResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        abort_unless($membership->canBeSubmitted(), 403);

        $missing = $this->missingFields($user);

        if ($missing !== []) {
            return redirect()
                ->route('profile.membership')
                ->with('status', __('profile.submit_missing_fields'));
        }

        $wasRejected = $membership->isRejected();

        $membership->update([
            'status' => 'pending',
            'submitted_at' => now(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        MembershipStatusHistory::create([
            'membership_id' => $membership->id,
            'old_status' => $wasRejected ? 'rejected' : null,
            'new_status' => 'pending',
            'comment' => $wasRejected ? __('profile.resubmitted_comment') : null,
            'changed_by' => $user->id,
        ]);

        $admins = User::role('administrateur')->get();

        if ($admins->isNotEmpty()) {
            $notificationService->send(
                recipients: $admins,
                title: [
                    'fr' => __('profile.submission_notification_title', [], 'fr'),
                    'ar' => __('profile.submission_notification_title', [], 'ar'),
                ],
                message: [
                    'fr' => __('profile.submission_notification_body', ['name' => $user->display_name], 'fr'),
                    'ar' => __('profile.submission_notification_body', ['name' => $user->display_name], 'ar'),
                ],
                sender: $user,
                actionUrl: route('admin.memberships.show', $membership),
            );
        }

        return redirect()
            ->route('profile.membership')
            ->with('status', __('profile.flash_submitted'));
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
    private function missingFields(User $user): array
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
