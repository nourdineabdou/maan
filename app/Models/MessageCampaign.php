<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageCampaign extends Model
{
    use HasFactory;
    use HasTranslatedAttributes;

    protected $fillable = [
        'created_by',
        'title',
        'message',
        'target_type',
        'target_filters',
        'channel',
        'status',
        'scheduled_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'message' => 'array',
            'target_filters' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
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
