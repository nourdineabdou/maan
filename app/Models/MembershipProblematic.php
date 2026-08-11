<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MembershipProblematic extends Pivot
{
    protected $fillable = [
        'membership_id',
        'problematic_id',
        'description',
        'requested_solution',
        'locality',
        'priority',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function problematic(): BelongsTo
    {
        return $this->belongsTo(Problematic::class);
    }
}
