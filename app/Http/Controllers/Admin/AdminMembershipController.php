<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectMembershipRequest;
use App\Models\Membership;
use App\Models\MembershipNeed;
use App\Models\Problematic;
use App\Services\MembershipApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMembershipController extends Controller
{
    public function index(Request $request): View
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

        return view('admin.memberships.index', [
            'memberships' => $query->paginate(20)->withQueryString(),
            'regions' => \App\Models\Region::orderBy('display_order')->get(),
            'filters' => array_merge(
                ['status' => '', 'region' => '', 'q' => ''],
                $request->only(['status', 'region', 'q'])
            ),
        ]);
    }

    public function show(Request $request, Membership $membership): View
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

        // Cf. Member\MemberProblematicController::edit() : eager-loading une
        // relation définie sur le modèle pivot ne peut pas passer par with().
        $membership->problematics->each(fn ($problematic) => $problematic->pivot->load('documents'));

        return view('admin.memberships.show', [
            'membership' => $membership,
        ]);
    }

    public function approve(Request $request, Membership $membership, MembershipApprovalService $approvalService, NotificationService $notificationService): RedirectResponse
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

        return redirect()
            ->route('admin.memberships.show', $membership)
            ->with('status', __('memberships.flash_approved', ['number' => $membership->member_number]));
    }

    public function reject(RejectMembershipRequest $request, Membership $membership, MembershipApprovalService $approvalService, NotificationService $notificationService): RedirectResponse
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

        return redirect()
            ->route('admin.memberships.show', $membership)
            ->with('status', __('memberships.flash_rejected'));
    }

    public function updateProblematicStatus(Request $request, Membership $membership, Problematic $problematic, NotificationService $notificationService): RedirectResponse
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

        return back()->with('status', __('memberships.flash_status_updated'));
    }

    public function updateNeedStatus(Request $request, Membership $membership, MembershipNeed $need, NotificationService $notificationService): RedirectResponse
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

        return back()->with('status', __('memberships.flash_status_updated'));
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
