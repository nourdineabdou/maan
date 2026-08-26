<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasPushSubscriptions;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'preferred_locale',
        'is_active',
        'phone_verified_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function latestMembership(): HasOne
    {
        return $this->hasOne(Membership::class)->latestOfMany();
    }

    public function ambassadorRequests(): HasMany
    {
        return $this->hasMany(AmbassadorRequest::class);
    }

    public function latestAmbassadorRequest(): HasOne
    {
        return $this->hasOne(AmbassadorRequest::class)->latestOfMany();
    }

    public function isAmbassador(): bool
    {
        return $this->hasRole('ambassadeur');
    }

    /**
     * Libellé de rôle affiché sur la carte de membre (accordé au genre pour
     * les ambassadeurs — "AMBASSADRICE" pour les femmes).
     */
    public function roleLabel(): string
    {
        if (! $this->isAmbassador()) {
            return __('card.member_label');
        }

        return $this->profile?->gender === 'female'
            ? __('card.ambassador_label_female')
            : __('card.ambassador_label');
    }

    public function notificationRecipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function expoPushTokens(): HasMany
    {
        return $this->hasMany(ExpoPushToken::class);
    }

    /**
     * Routage du canal ExpoPushChannel (app mobile), suivant la même
     * convention que routeNotificationForWebPush() du trait vendor.
     *
     * @return array<int, string>
     */
    public function routeNotificationForExpoPush(): array
    {
        return $this->expoPushTokens()->pluck('token')->all();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notificationRecipients()->whereNull('read_at')->count();
    }

    public function memberMessages(): HasMany
    {
        return $this->hasMany(MemberMessage::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->profile?->full_name
            ?? $this->name
            ?? $this->phone
            ?? $this->email;
    }
}