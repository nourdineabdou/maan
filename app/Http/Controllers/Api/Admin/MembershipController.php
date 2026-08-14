<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Admin\RejectMembershipRequest;
use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Services\MembershipApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('members.view'), 403);

        $query = Membership::query()
            ->with(['user.profile.region'])
            ->whereNotNull('submitted_at')
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($regionId = $request->string('region')->toString()) {
            $query->whereHas('user.profile', fn ($q) => $q->where('region_id', $regionId));
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                    ->orWhere('member_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user.profile', function ($profileQuery) use ($search) {
                        $profileQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('nni', 'like', "%{$search}%");
                    });
            });
        }

        $memberships = $query->paginate(20)->withQueryString();

        return response()->json([
            'data' => MembershipResource::collection($memberships),
            'meta' => $this->paginationMeta($memberships),
        ]);
    }

    public function show(Request $request, Membership $membership): JsonResponse
    {
        abort_unless($request->user()->can('members.view'), 403);

        return response()->json([
            'data' => MembershipResource::make($membership->load([
                'user.profile.region',
                'user.profile.moughataaRef',
                'user.profile.communeRef',
                'documents',
                'problematics',
                'statusHistories.changedBy',
                'reviewer',
            ])),
        ]);
    }

    public function approve(Request $request, Membership $membership, MembershipApprovalService $approvalService, NotificationService $notificationService): JsonResponse
    {
        abort_unless($request->user()->can('memberships.approve'), 403);

        $approvalService->approve($membership, $request->user());

        $notificationService->send(
            recipients: [$membership->user],
            title: [
                'fr' => __('memberships.approved_notification_title', [], 'fr'),
                'ar' => __('memberships.approved_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('memberships.approved_notification_body', ['number' => $membership->member_number], 'fr'),
                'ar' => __('memberships.approved_notification_body', ['number' => $membership->member_number], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: route('profile.membership'),
        );

        return response()->json(['data' => MembershipResource::make($membership->refresh())]);
    }

    public function reject(RejectMembershipRequest $request, Membership $membership, MembershipApprovalService $approvalService, NotificationService $notificationService): JsonResponse
    {
        $reason = $request->string('reason')->toString();

        $approvalService->reject($membership, $reason, $request->user());

        $notificationService->send(
            recipients: [$membership->user],
            title: [
                'fr' => __('memberships.rejected_notification_title', [], 'fr'),
                'ar' => __('memberships.rejected_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('memberships.rejected_notification_body', ['reason' => $reason], 'fr'),
                'ar' => __('memberships.rejected_notification_body', ['reason' => $reason], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: route('profile.membership'),
        );

        return response()->json(['data' => MembershipResource::make($membership->refresh())]);
    }
}
