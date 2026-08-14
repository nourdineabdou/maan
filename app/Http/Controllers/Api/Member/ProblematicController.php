<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\ProblematicResource;
use App\Models\Membership;
use App\Models\Problematic;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProblematicController extends ApiController
{
    public function index(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        return response()->json([
            'data' => [
                'selected' => ProblematicResource::collection($membership->problematics),
                'available' => ProblematicResource::collection(
                    Problematic::where('is_active', true)->orderBy('display_order')->get()
                ),
            ],
        ]);
    }

    public function update(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): JsonResponse
    {
        $user = $request->user();
        $membership = $draftService->draftFor($user);

        $selected = $request->input('problematics', []);

        $data = $request->validate([
            'problematics' => ['nullable', 'array'],
            'problematics.*' => ['exists:problematics,id'],
            'details' => ['nullable', 'array'],
            'details.*.description' => ['nullable', 'string', 'max:2000'],
            'details.*.requested_solution' => ['nullable', 'string', 'max:2000'],
            'details.*.locality' => ['nullable', 'string', 'max:255'],
            'details.*.priority' => ['nullable', 'in:low,normal,high,urgent'],
        ]);

        $syncData = [];

        foreach ($selected as $problematicId) {
            $details = $data['details'][$problematicId] ?? [];

            $problematic = Problematic::find($problematicId);

            if ($problematic && $problematic->code === 'other' && empty($details['description'])) {
                return response()->json([
                    'message' => __('profile.other_description_required'),
                    'errors' => ['details' => [__('profile.other_description_required')]],
                ], 422);
            }

            $syncData[$problematicId] = [
                'description' => $details['description'] ?? null,
                'requested_solution' => $details['requested_solution'] ?? null,
                'locality' => $details['locality'] ?? null,
                'priority' => $details['priority'] ?? 'normal',
            ];
        }

        $membership->problematics()->sync($syncData);

        if (! empty($selected)) {
            $this->notifyAdmins($notificationService, $user, $membership);
        }

        return response()->json([
            'data' => ProblematicResource::collection($membership->refresh()->problematics),
        ]);
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
                'fr' => __('profile.problematics_notification_title', [], 'fr'),
                'ar' => __('profile.problematics_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('profile.problematics_notification_body', ['name' => $user->display_name], 'fr'),
                'ar' => __('profile.problematics_notification_body', ['name' => $user->display_name], 'ar'),
            ],
            sender: $user,
            actionUrl: route('admin.memberships.show', $membership),
        );
    }
}
