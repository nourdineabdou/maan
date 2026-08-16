<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MembershipNeed extends Model
{
    protected $fillable = [
        'membership_id',
        'description',
        'status',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(MemberDocument::class, 'related');
    }
}
