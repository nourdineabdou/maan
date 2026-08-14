<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'preferred_locale' => $this->preferred_locale,
            'is_active' => $this->is_active,
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'profile' => MemberProfileResource::make($this->whenLoaded('profile')),
            'latest_membership' => MembershipResource::make($this->whenLoaded('latestMembership')),
            'unread_notifications_count' => $this->when(
                $this->relationLoaded('notificationRecipients'),
                fn () => $this->unreadNotificationsCount(),
            ),
            'created_at' => $this->created_at,
        ];
    }
}
