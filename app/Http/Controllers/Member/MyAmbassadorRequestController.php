<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AmbassadorApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyAmbassadorRequestController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('profile.ambassador', [
            'ambassadorRequest' => $user->latestAmbassadorRequest,
            'isEligible' => (bool) $user->latestMembership?->isApproved(),
        ]);
    }

    public function store(Request $request, AmbassadorApprovalService $approvalService, NotificationService $notificationService): RedirectResponse
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

        return redirect()
            ->route('profile.ambassador')
            ->with('status', __('ambassadors.flash_submitted'));
    }
}
