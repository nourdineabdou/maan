<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;
    use HasTranslatedAttributes;

    protected $fillable = [
        'type',
        'title',
        'message',
        'action_url',
        'data',
        'message_campaign_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'message' => 'array',
            'data' => 'array',
        ];
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MessageCampaign::class, 'message_campaign_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function getTranslatedTitleAttribute(): ?string
    {
        return $this->getTranslation('title');
    }

    public function getTranslatedMessageAttribute(): ?string
    {
        return $this->getTranslation('message');
    }
}
