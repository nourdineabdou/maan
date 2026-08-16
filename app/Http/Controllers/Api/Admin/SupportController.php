<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\ResolvesDeclarationRelation;
use App\Http\Resources\MemberMessageResource;
use App\Models\MemberMessage;
use App\Models\Membership;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends ApiController
{
    use ResolvesDeclarationRelation;

    /**
     * Contrairement aux fils habituels (ouverts par le membre), l'admin
     * prend ici l'initiative d'écrire en premier à un membre précis, depuis
     * sa fiche d'adhésion.
     */
    public function store(Request $request, Membership $membership, NotificationService $notificationService): JsonResponse
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

        return response()->json(['data' => MemberMessageResource::make($message)], 201);
    }

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        $query = MemberMessage::query()->with('user.profile');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $messages = $query->latest()->paginate(20)->withQueryString();

        return response()->json([
            'data' => MemberMessageResource::collection($messages),
            'meta' => $this->paginationMeta($messages),
        ]);
    }

    public function show(Request $request, MemberMessage $message): JsonResponse
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        return response()->json(['data' => MemberMessageResource::make($message->load(['user.profile', 'replies.author']))]);
    }

    public function reply(Request $request, MemberMessage $message, NotificationService $notificationService): JsonResponse
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

        return response()->json(['data' => MemberMessageResource::make($message->load('replies.author'))]);
    }

    public function close(Request $request, MemberMessage $message): JsonResponse
    {
        abort_unless($request->user()->can('support_messages.manage'), 403);

        $message->update(['status' => 'closed']);

        return response()->json(['data' => MemberMessageResource::make($message)]);
    }
}
