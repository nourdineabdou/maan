<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'registration_number',
        'member_number',
        'status',
        'member_message',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'reviewed_by',
        'rejection_reason',
        'completion_request',
        'administrator_notes',
        'qr_token',
        'card_generated_at',
        'card_is_active',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'card_generated_at' => 'datetime',
            'card_is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function problematics(): BelongsToMany
    {
        return $this->belongsToMany(
            Problematic::class,
            'membership_problematics'
        )
            ->using(MembershipProblematic::class)
            ->withPivot([
                'id',
                'description',
                'requested_solution',
                'locality',
                'priority',
                'status',
            ])
            ->withTimestamps();
    }

    public function needs(): HasMany
    {
        return $this->hasMany(MembershipNeed::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MemberDocument::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(MembershipStatusHistory::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Dossier créé mais jamais envoyé pour validation.
     */
    public function isDraft(): bool
    {
        return $this->submitted_at === null;
    }

    public function canBeSubmitted(): bool
    {
        return $this->isDraft() || $this->isRejected();
    }
}