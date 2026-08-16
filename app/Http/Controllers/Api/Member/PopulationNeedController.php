<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipNeedResource;
use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopulationNeedController extends ApiController
{
    public function index(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        return response()->json([
            'data' => MembershipNeedResource::collection($membership->needs()->latest()->with('documents')->get()),
        ]);
    }

    public function store(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): JsonResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $need = $membership->needs()->create([
            'description' => $data['description'],
            'status' => 'submitted',
        ]);

        $this->notifyAdmins($notificationService, $user, $membership);

        return response()->json(['data' => MembershipNeedResource::make($need)], 201);
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
            data: ['type' => 'admin_membership', 'id' => $membership->id],
        );
    }
}
