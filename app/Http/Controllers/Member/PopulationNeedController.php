<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PopulationNeedController extends Controller
{
    public function index(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        return view('profile.need-edit', [
            'membership' => $membership->load(['needs' => fn ($query) => $query->latest()->with('documents')]),
        ]);
    }

    public function store(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): RedirectResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $membership->needs()->create([
            'description' => $data['description'],
            'status' => 'submitted',
        ]);

        $this->notifyAdmins($notificationService, $user, $membership);

        return redirect()
            ->route('profile.need.edit')
            ->with('status', __('profile.flash_saved'));
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
