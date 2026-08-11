<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'subject',
        'body',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MemberMessageReply::class)->orderBy('created_at');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Vrai si l'administration a pris l'initiative de ce fil de discussion
     * (plutôt que le membre, cas normal jusqu'ici).
     */
    public function wasStartedByAdmin(): bool
    {
        return $this->created_by !== null && $this->created_by !== $this->user_id;
    }
}
