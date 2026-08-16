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
            'missingFields' => $draftService->missingFieldsFor($request->user()),
        ]);
    }

    public function submit(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): RedirectResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        abort_unless($membership->canBeSubmitted(), 403);

        $missing = $draftService->missingFieldsFor($user);

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
                data: ['type' => 'admin_membership', 'id' => $membership->id],
            );
        }

        return redirect()
            ->route('profile.membership')
            ->with('status', __('profile.flash_submitted'));
    }
}
