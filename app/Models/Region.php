<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;
    use HasTranslatedAttributes;

    protected $fillable = [
        'code',
        'name',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function memberProfiles(): HasMany
    {
        return $this->hasMany(MemberProfile::class);
    }

    public function moughataas(): HasMany
    {
        return $this->hasMany(Moughataa::class);
    }

    public function getTranslatedNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }
}