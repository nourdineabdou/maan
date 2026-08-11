<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MemberProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'nni',
        'gender',
        'birth_date',
        'region_id',
        'moughataa_id',
        'commune_id',
        'moughataa',
        'commune',
        'locality',
        'address',
        'photo_path',
        'employment_status',
        'employment_sector',
        'function',
        'position',
        'employer',
        'other_employment_details',
        'profile_completed',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'profile_completed' => 'boolean',
        ];
    }

    protected $appends = [
        'full_name',
        'photo_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function moughataaRef(): BelongsTo
    {
        return $this->belongsTo(Moughataa::class, 'moughataa_id');
    }

    public function communeRef(): BelongsTo
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->photo_path);
    }
}