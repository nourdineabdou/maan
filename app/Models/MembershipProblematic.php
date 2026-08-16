<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MembershipProblematic extends Pivot
{
    /**
     * Sans ce nom explicite, la classe pivot devine "membership_problematic"
     * (singulier) au lieu de "membership_problematics" quand elle est
     * requêtée directement (ex. hors du ->using() de Membership::problematics()) —
     * AsPivot::getTable() utilise Str::singular() plutôt que Str::plural().
     */
    protected $table = 'membership_problematics';

    protected $fillable = [
        'membership_id',
        'problematic_id',
        'description',
        'requested_solution',
        'locality',
        'priority',
        'status',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function problematic(): BelongsTo
    {
        return $this->belongsTo(Problematic::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(MemberDocument::class, 'related');
    }
}
