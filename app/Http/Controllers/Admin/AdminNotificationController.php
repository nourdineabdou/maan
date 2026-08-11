<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageCampaign;
use App\Models\Region;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('notifications.send'), 403);

        return view('admin.notifications.index', [
            'notifications' => MessageCampaign::with(['creator', 'notifications.recipients'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('notifications.send'), 403);

        return view('admin.notifications.create', [
            'regions' => Region::orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request, NotificationService $notificationService): RedirectResponse
    {
        abort_unless($request->user()->can('notifications.send'), 403);

        $data = $request->validate([
            'title_fr' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'message_fr' => ['required', 'string'],
            'message_ar' => ['required', 'string'],
            'target_type' => ['required', 'in:all,region,employment_status,membership_status'],
            'target_value' => ['nullable', 'string', 'required_unless:target_type,all'],
        ]);

        $recipients = $data['target_type'] === 'all'
            ? \App\Models\User::role('membre')->get()
            : $notificationService->resolveTargets($data['target_type'], $data['target_value'] ?? null);

        $campaign = MessageCampaign::create([
            'created_by' => $request->user()->id,
            'title' => ['fr' => $data['title_fr'], 'ar' => $data['title_ar']],
            'message' => ['fr' => $data['message_fr'], 'ar' => $data['message_ar']],
            'target_type' => $data['target_type'] === 'all' ? 'all' : $data['target_type'],
            'target_filters' => $data['target_type'] === 'all' ? null : ['value' => $data['target_value']],
            'channel' => 'internal',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $notificationService->send(
            recipients: $recipients,
            title: ['fr' => $data['title_fr'], 'ar' => $data['title_ar']],
            message: ['fr' => $data['message_fr'], 'ar' => $data['message_ar']],
            sender: $request->user(),
            campaign: $campaign,
        );

        return redirect()
            ->route('admin.notifications.index')
            ->with('status', __('notifications.flash_sent', ['count' => $recipients->count()]));
    }
}
