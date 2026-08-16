<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MemberMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'subject',
        'body',
        'status',
        'related_type',
        'related_id',
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

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Résumé lisible de la déclaration référencée, affiché comme badge
     * "Concernant : ..." dans les vues du fil de discussion.
     */
    public function relatedLabel(): ?string
    {
        return match (true) {
            $this->related instanceof MembershipProblematic => $this->related->problematic?->getTranslation('name'),
            $this->related instanceof MembershipNeed => str($this->related->description)->limit(60)->toString(),
            default => null,
        };
    }

    public function relatedKind(): ?string
    {
        return match (true) {
            $this->related instanceof MembershipProblematic => 'problematic',
            $this->related instanceof MembershipNeed => 'need',
            default => null,
        };
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
