<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\ResolvesDeclarationRelation;
use App\Http\Resources\MemberMessageResource;
use App\Models\MemberMessage;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends ApiController
{
    use ResolvesDeclarationRelation;

    public function index(Request $request): JsonResponse
    {
        $messages = $request->user()->memberMessages()->latest()->paginate(15);

        return response()->json([
            'data' => MemberMessageResource::collection($messages),
            'meta' => $this->paginationMeta($messages),
        ]);
    }

    public function store(Request $request, MembershipDraftService $draftService, NotificationService $notificationService): JsonResponse
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

        return response()->json(['data' => MemberMessageResource::make($message)], 201);
    }

    public function show(Request $request, MemberMessage $message): JsonResponse
    {
        abort_unless($message->user_id === $request->user()->id, 403);

        return response()->json(['data' => MemberMessageResource::make($message->load('replies.author'))]);
    }

    public function reply(Request $request, MemberMessage $message, NotificationService $notificationService): JsonResponse
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

        return response()->json(['data' => MemberMessageResource::make($message->load('replies.author'))]);
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
