<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commune extends Model
{
    use HasFactory;
    use HasTranslatedAttributes;

    protected $fillable = [
        'moughataa_id',
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

    public function moughataa(): BelongsTo
    {
        return $this->belongsTo(Moughataa::class);
    }

    public function getTranslatedNameAttribute(): ?string
    {
        return $this->getTranslation('name');
    }
}
