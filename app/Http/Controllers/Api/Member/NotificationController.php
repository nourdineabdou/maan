<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\NotificationRecipientResource;
use App\Models\NotificationRecipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $recipients = $request->user()
            ->notificationRecipients()
            ->with('notification')
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => NotificationRecipientResource::collection($recipients),
            'meta' => $this->paginationMeta($recipients),
        ]);
    }

    public function show(Request $request, NotificationRecipient $recipient): JsonResponse
    {
        abort_unless($recipient->user_id === $request->user()->id, 403);

        $recipient->markAsRead();

        return response()->json(['data' => NotificationRecipientResource::make($recipient->load('notification'))]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->notificationRecipients()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['data' => ['status' => 'ok']]);
    }
}
