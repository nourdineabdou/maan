<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class MemberDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_id',
        'document_type',
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_required',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_note',
        'related_type',
        'related_id',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_required' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected $appends = [
        'file_url',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function relatedKind(): ?string
    {
        return match (true) {
            $this->related instanceof MembershipProblematic => 'problematic',
            $this->related instanceof MembershipNeed => 'need',
            default => null,
        };
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}