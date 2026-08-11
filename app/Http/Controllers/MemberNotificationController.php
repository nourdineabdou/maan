<?php

namespace App\Http\Controllers;

use App\Models\NotificationRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberNotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'recipients' => $request->user()
                ->notificationRecipients()
                ->with('notification')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(Request $request, NotificationRecipient $recipient): View
    {
        abort_unless($recipient->user_id === $request->user()->id, 403);

        $recipient->markAsRead();

        return view('notifications.show', [
            'recipient' => $recipient->load('notification'),
        ]);
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->notificationRecipients()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()
            ->route('notifications.index')
            ->with('status', __('notifications.flash_all_read'));
    }
}
