<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;
    use HasTranslatedAttributes;

    protected $fillable = [
        'title',
        'message',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'message' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(AnnouncementImage::class)->orderBy('display_order');
    }
}
