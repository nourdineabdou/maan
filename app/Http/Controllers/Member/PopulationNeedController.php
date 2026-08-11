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
    public function edit(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        return view('profile.need-edit', [
            'membership' => $membership->load('documents'),
        ]);
    }

    public function update(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): RedirectResponse
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
        );
    }
}
