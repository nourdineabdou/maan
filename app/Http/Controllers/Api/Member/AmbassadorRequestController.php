<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AmbassadorRequestResource;
use App\Models\User;
use App\Services\AmbassadorApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmbassadorRequestController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $latest = $user->latestAmbassadorRequest;

        return response()->json([
            'data' => $latest ? AmbassadorRequestResource::make($latest) : null,
            'meta' => ['is_eligible' => (bool) $user->latestMembership?->isApproved()],
        ]);
    }

    public function request(Request $request, AmbassadorApprovalService $approvalService, NotificationService $notificationService): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->latestMembership?->isApproved(), 403);

        $existing = $user->latestAmbassadorRequest;
        abort_if($existing && ! $existing->canBeSubmitted(), 403);

        $ambassadorRequest = $approvalService->requestFor($user);

        $admins = User::role('administrateur')->get();

        if ($admins->isNotEmpty()) {
            $notificationService->send(
                recipients: $admins,
                title: [
                    'fr' => __('ambassadors.submission_notification_title', [], 'fr'),
                    'ar' => __('ambassadors.submission_notification_title', [], 'ar'),
                ],
                message: [
                    'fr' => __('ambassadors.submission_notification_body', ['name' => $user->display_name], 'fr'),
                    'ar' => __('ambassadors.submission_notification_body', ['name' => $user->display_name], 'ar'),
                ],
                sender: $user,
                actionUrl: route('admin.ambassadors.show', $ambassadorRequest),
                data: ['type' => 'admin_ambassador_request', 'id' => $ambassadorRequest->id],
            );
        }

        return response()->json(['data' => AmbassadorRequestResource::make($ambassadorRequest)]);
    }
}
