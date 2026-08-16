<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Admin\RejectMembershipRequest;
use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Models\MembershipNeed;
use App\Models\Problematic;
use App\Services\MembershipApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('members.view'), 403);

        $query = Membership::query()
            ->with(['user.profile.region', 'needs'])
            ->whereNotNull('submitted_at')
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($regionId = $request->string('region')->toString()) {
            $query->whereHas('user.profile', fn ($q) => $q->where('region_id', $regionId));
        }

        if ($request->boolean('has_population_need')) {
            $query->whereHas('needs');
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

        $membership->load([
            'user.profile.region',
            'user.profile.moughataaRef',
            'user.profile.communeRef',
            'documents',
            'problematics',
            'needs' => fn ($query) => $query->latest()->with('documents'),
            'statusHistories.changedBy',
            'reviewer',
        ]);

        // Cf. Web\AdminMembershipController::show() : eager-loading une
        // relation définie sur le modèle pivot ne peut pas passer par with().
        $membership->problematics->each(fn ($problematic) => $problematic->pivot->load('documents'));

        return response()->json([
            'data' => MembershipResource::make($membership),
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
            data: ['type' => 'membership'],
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
            data: ['type' => 'membership'],
        );

        return response()->json(['data' => MembershipResource::make($membership->refresh())]);
    }

    public function updateProblematicStatus(Request $request, Membership $membership, Problematic $problematic, NotificationService $notificationService): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);
        abort_unless($membership->problematics()->where('problematic_id', $problematic->id)->exists(), 404);

        $data = $request->validate([
            'status' => ['required', 'in:submitted,in_progress,resolved'],
        ]);

        $membership->problematics()->updateExistingPivot($problematic->id, ['status' => $data['status']]);

        $this->notifyStatusChange(
            $notificationService,
            $request,
            $membership,
            $problematic->getTranslation('name'),
            $data['status'],
            route('profile.problematics.edit'),
            ['type' => 'problematics'],
        );

        return response()->json([
            'data' => MembershipResource::make($membership->refresh()->load('problematics')),
        ]);
    }

    public function updateNeedStatus(Request $request, Membership $membership, MembershipNeed $need, NotificationService $notificationService): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);
        abort_unless($need->membership_id === $membership->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:submitted,in_progress,resolved'],
        ]);

        $need->update(['status' => $data['status']]);

        $this->notifyStatusChange(
            $notificationService,
            $request,
            $membership,
            Str::limit($need->description, 60),
            $data['status'],
            route('profile.need.edit'),
            ['type' => 'population_need'],
        );

        return response()->json([
            'data' => MembershipResource::make($membership->refresh()->load('needs')),
        ]);
    }

    private function notifyStatusChange(NotificationService $notificationService, Request $request, Membership $membership, string $label, string $status, string $actionUrl, ?array $data = null): void
    {
        $notificationService->send(
            recipients: [$membership->user],
            title: [
                'fr' => __('memberships.declaration_status_notification_title', [], 'fr'),
                'ar' => __('memberships.declaration_status_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('memberships.declaration_status_notification_body', ['label' => $label, 'status' => __('forms.status_'.$status, [], 'fr')], 'fr'),
                'ar' => __('memberships.declaration_status_notification_body', ['label' => $label, 'status' => __('forms.status_'.$status, [], 'ar')], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: $actionUrl,
            data: $data,
        );
    }
}
