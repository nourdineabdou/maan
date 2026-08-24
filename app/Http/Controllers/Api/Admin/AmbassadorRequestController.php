<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Admin\RejectAmbassadorRequest;
use App\Http\Resources\AmbassadorRequestResource;
use App\Models\AmbassadorRequest;
use App\Services\AmbassadorApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmbassadorRequestController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('ambassadors.view'), 403);

        $query = AmbassadorRequest::query()
            ->with('user.profile')
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $ambassadorRequests = $query->paginate(20)->withQueryString();

        return response()->json([
            'data' => AmbassadorRequestResource::collection($ambassadorRequests),
            'meta' => $this->paginationMeta($ambassadorRequests),
        ]);
    }

    public function show(Request $request, AmbassadorRequest $ambassadorRequest): JsonResponse
    {
        abort_unless($request->user()->can('ambassadors.view'), 403);

        $ambassadorRequest->load(['user.profile.region', 'reviewer']);

        return response()->json([
            'data' => AmbassadorRequestResource::make($ambassadorRequest),
        ]);
    }

    public function approve(Request $request, AmbassadorRequest $ambassadorRequest, AmbassadorApprovalService $approvalService, NotificationService $notificationService): JsonResponse
    {
        abort_unless($request->user()->can('ambassadors.approve'), 403);

        $approvalService->approve($ambassadorRequest, $request->user());

        $notificationService->send(
            recipients: [$ambassadorRequest->user],
            title: [
                'fr' => __('ambassadors.approved_notification_title', [], 'fr'),
                'ar' => __('ambassadors.approved_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('ambassadors.approved_notification_body', [], 'fr'),
                'ar' => __('ambassadors.approved_notification_body', [], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: route('profile.ambassador'),
            data: ['type' => 'ambassador_request'],
        );

        return response()->json(['data' => AmbassadorRequestResource::make($ambassadorRequest->refresh())]);
    }

    public function reject(RejectAmbassadorRequest $request, AmbassadorRequest $ambassadorRequest, AmbassadorApprovalService $approvalService, NotificationService $notificationService): JsonResponse
    {
        $reason = $request->string('reason')->toString();

        $approvalService->reject($ambassadorRequest, $reason, $request->user());

        $notificationService->send(
            recipients: [$ambassadorRequest->user],
            title: [
                'fr' => __('ambassadors.rejected_notification_title', [], 'fr'),
                'ar' => __('ambassadors.rejected_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('ambassadors.rejected_notification_body', ['reason' => $reason], 'fr'),
                'ar' => __('ambassadors.rejected_notification_body', ['reason' => $reason], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: route('profile.ambassador'),
            data: ['type' => 'ambassador_request'],
        );

        return response()->json(['data' => AmbassadorRequestResource::make($ambassadorRequest->refresh())]);
    }
}
