<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDeclarationRelation;
use App\Models\MemberMessage;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberMessageController extends Controller
{
    use ResolvesDeclarationRelation;

    public function index(Request $request): View
    {
        return view('support.index', [
            'messages' => $request->user()
                ->memberMessages()
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        return view('support.create', [
            'relatedType' => $request->query('related_type'),
            'relatedId' => $request->query('related_id'),
        ]);
    }

    public function store(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'related_type' => ['nullable', 'in:problematic,need'],
            'related_id' => ['required_with:related_type', 'integer'],
        ]);

        $relation = $this->resolveDeclarationRelation(
            $draftService->draftFor($request->user()),
            $data['related_type'] ?? null,
            isset($data['related_id']) ? (int) $data['related_id'] : null,
        );

        $message = $request->user()->memberMessages()->create([
            'created_by' => $request->user()->id,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => 'open',
            ...$relation,
        ]);

        $this->notifyAdmins($notificationService, $message, $request->user()->display_name);

        return redirect()
            ->route('support.show', $message)
            ->with('status', __('support.flash_sent'));
    }

    public function show(Request $request, MemberMessage $message): View
    {
        abort_unless($message->user_id === $request->user()->id, 403);

        return view('support.show', [
            'message' => $message->load('replies.author'),
        ]);
    }

    public function reply(Request $request, MemberMessage $message, NotificationService $notificationService): RedirectResponse
    {
        abort_unless($message->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message->replies()->create([
            'author_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        $message->update(['status' => 'open']);

        $this->notifyAdmins($notificationService, $message, $request->user()->display_name);

        return redirect()
            ->route('support.show', $message)
            ->with('status', __('support.flash_reply_sent'));
    }

    private function notifyAdmins(NotificationService $notificationService, MemberMessage $message, string $memberName): void
    {
        $admins = User::role('administrateur')->get();

        if ($admins->isEmpty()) {
            return;
        }

        $notificationService->send(
            recipients: $admins,
            title: [
                'fr' => __('support.notification_title', [], 'fr'),
                'ar' => __('support.notification_title', [], 'ar'),
            ],
            message: [
                'fr' => __('support.notification_body', ['name' => $memberName, 'subject' => $message->subject], 'fr'),
                'ar' => __('support.notification_body', ['name' => $memberName, 'subject' => $message->subject], 'ar'),
            ],
            sender: $message->user,
            actionUrl: route('admin.support.show', $message),
            data: ['type' => 'admin_support', 'id' => $message->id],
        );
    }
}
