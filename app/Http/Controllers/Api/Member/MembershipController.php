<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipResource;
use App\Models\MembershipStatusHistory;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends ApiController
{
    public function show(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        return response()->json([
            'data' => MembershipResource::make($membership->load(['problematics', 'documents', 'statusHistories'])),
            'meta' => ['missing_fields' => $draftService->missingFieldsFor($request->user())],
        ]);
    }

    public function submit(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): JsonResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        abort_unless($membership->canBeSubmitted(), 403);

        $missing = $draftService->missingFieldsFor($user);

        if ($missing !== []) {
            return response()->json([
                'message' => __('profile.submit_missing_fields'),
                'meta' => ['missing_fields' => $missing],
            ], 422);
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

        return response()->json(['data' => MembershipResource::make($membership->refresh())]);
    }
}
