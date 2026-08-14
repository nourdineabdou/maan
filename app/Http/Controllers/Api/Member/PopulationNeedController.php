<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopulationNeedController extends ApiController
{
    public function show(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        return response()->json(['data' => MembershipResource::make($membership)]);
    }

    public function update(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): JsonResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        $data = $request->validate([
            'population_needs' => ['nullable', 'string', 'max:5000'],
        ]);

        $membership->update($data);

        if (! empty($data['population_needs'])) {
            $this->notifyAdmins($notificationService, $user, $membership);
        }

        return response()->json(['data' => MembershipResource::make($membership->refresh())]);
    }

    private function notifyAdmins(NotificationService $notificationService, User $user, Membership $membership): void
    {
        $admins = User::role('administrateur')->get();

        if ($admins->isEmpty()) {
            return;
        }

        $notificationService->send(
            recipients: $admins,
            title: [
                'fr' => __('profile.need_notification_title', [], 'fr'),
                'ar' => __('profile.need_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('profile.need_notification_body', ['name' => $user->display_name], 'fr'),
                'ar' => __('profile.need_notification_body', ['name' => $user->display_name], 'ar'),
            ],
            sender: $user,
            actionUrl: route('admin.memberships.show', $membership),
        );
    }
}
