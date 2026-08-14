<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MessageCampaignResource;
use App\Models\MessageCampaign;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('notifications.send'), 403);

        $campaigns = MessageCampaign::with(['creator', 'notifications.recipients'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => MessageCampaignResource::collection($campaigns),
            'meta' => $this->paginationMeta($campaigns),
        ]);
    }

    public function store(Request $request, NotificationService $notificationService): JsonResponse
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
            ? User::role('membre')->get()
            : $notificationService->resolveTargets($data['target_type'], $data['target_value'] ?? null);

        $campaign = MessageCampaign::create([
            'created_by' => $request->user()->id,
            'title' => ['fr' => $data['title_fr'], 'ar' => $data['title_ar']],
            'message' => ['fr' => $data['message_fr'], 'ar' => $data['message_ar']],
            'target_type' => $data['target_type'],
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

        return response()->json([
            'data' => MessageCampaignResource::make($campaign),
            'meta' => ['recipients_count' => $recipients->count()],
        ], 201);
    }
}
