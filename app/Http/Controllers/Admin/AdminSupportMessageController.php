<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ResolvesDeclarationRelation;
use App\Http\Controllers\Controller;
use App\Models\MemberMessage;
use App\Models\Membership;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSupportMessageController extends Controller
{
    use ResolvesDeclarationRelation;

    /**
     * Contrairement aux fils habituels (ouverts par le membre), l'admin
     * prend ici l'initiative d'écrire en premier à un membre précis, depuis
     * sa fiche d'adhésion.
     */
    public function store(Request $request, Membership $membership, NotificationService $notificationService): RedirectResponse
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'related_type' => ['nullable', 'in:problematic,need'],
            'related_id' => ['required_with:related_type', 'integer'],
        ]);

        $relation = $this->resolveDeclarationRelation(
            $membership,
            $data['related_type'] ?? null,
            isset($data['related_id']) ? (int) $data['related_id'] : null,
        );

        $message = MemberMessage::create([
            'user_id' => $membership->user_id,
            'created_by' => $request->user()->id,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => 'open',
            ...$relation,
        ]);

        $notificationService->send(
            recipients: [$membership->user],
            title: [
                'fr' => __('support.new_thread_notification_title', [], 'fr'),
                'ar' => __('support.new_thread_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('support.new_thread_notification_body', ['subject' => $message->subject], 'fr'),
                'ar' => __('support.new_thread_notification_body', ['subject' => $message->subject], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: route('support.show', $message),
            data: ['type' => 'support', 'id' => $message->id],
        );

        return redirect()
            ->route('admin.support.show', $message)
            ->with('status', __('support.flash_sent'));
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        $query = MemberMessage::query()->with('user.profile');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('admin.support.index', [
            'messages' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => array_merge(['status' => ''], $request->only('status')),
        ]);
    }

    public function show(Request $request, MemberMessage $message): View
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        return view('admin.support.show', [
            'message' => $message->load(['user.profile', 'replies.author']),
        ]);
    }

    public function reply(Request $request, MemberMessage $message, NotificationService $notificationService): RedirectResponse
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message->replies()->create([
            'author_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $message->update(['status' => 'answered']);

        $notificationService->send(
            recipients: [$message->user],
            title: [
                'fr' => __('support.reply_notification_title', [], 'fr'),
                'ar' => __('support.reply_notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('support.reply_notification_body', ['subject' => $message->subject], 'fr'),
                'ar' => __('support.reply_notification_body', ['subject' => $message->subject], 'ar'),
            ],
            sender: $request->user(),
            actionUrl: route('support.show', $message),
            data: ['type' => 'support', 'id' => $message->id],
        );

        return redirect()
            ->route('admin.support.show', $message)
            ->with('status', __('support.flash_reply_sent'));
    }

    public function close(Request $request, MemberMessage $message): RedirectResponse
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        $message->update(['status' => 'closed']);

        return redirect()
            ->route('admin.support.show', $message)
            ->with('status', __('support.flash_closed'));
    }
}
