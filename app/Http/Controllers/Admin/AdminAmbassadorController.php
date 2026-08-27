<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectAmbassadorRequest;
use App\Models\AmbassadorRequest;
use App\Services\AmbassadorApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAmbassadorController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('ambassadors.view'), 403);

        $query = AmbassadorRequest::query()
            ->with('user.profile')
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.ambassadors.index', [
            'ambassadorRequests' => $query->paginate(20)->withQueryString(),
            'filters' => ['status' => $status],
        ]);
    }

    public function show(Request $request, AmbassadorRequest $ambassadorRequest): View
    {
        abort_unless($request->user()->can('ambassadors.view'), 403);

        $ambassadorRequest->load(['user.profile.region', 'reviewer']);

        return view('admin.ambassadors.show', [
            'ambassadorRequest' => $ambassadorRequest,
        ]);
    }

    public function approve(Request $request, AmbassadorRequest $ambassadorRequest, AmbassadorApprovalService $approvalService, NotificationService $notificationService): RedirectResponse
    {
        abort_unless($request->user()->can('ambassadors.approve'), 403);
        abort_if($ambassadorRequest->user_id === $request->user()->id, 403, __('ambassadors.cannot_review_own'));

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

        return redirect()
            ->route('admin.ambassadors.show', $ambassadorRequest)
            ->with('status', __('ambassadors.approved'));
    }

    public function reject(RejectAmbassadorRequest $request, AmbassadorRequest $ambassadorRequest, AmbassadorApprovalService $approvalService, NotificationService $notificationService): RedirectResponse
    {
        abort_if($ambassadorRequest->user_id === $request->user()->id, 403, __('ambassadors.cannot_review_own'));

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

        return redirect()
            ->route('admin.ambassadors.show', $ambassadorRequest)
            ->with('status', __('ambassadors.rejected'));
    }
}
