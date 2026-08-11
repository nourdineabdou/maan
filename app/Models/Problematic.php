<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Problematic extends Model
{
    use HasFactory;
    use HasTranslatedAttributes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'requires_justification',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'requires_justification' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(
            Membership::class,
            'membership_problematics'
        )
            ->using(MembershipProblematic::class)
            ->withPivot([
                'description',
                'requested_solution',
                'locality',
                'priority',
            ])
            ->withTimestamps();
    }

    public function getTranslatedNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }
}