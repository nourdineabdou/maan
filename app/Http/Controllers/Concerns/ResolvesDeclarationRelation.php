<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Membership;
use App\Models\MembershipNeed;
use App\Models\MembershipProblematic;

trait ResolvesDeclarationRelation
{
    /**
     * Retrouve la déclaration (problématique ou besoin) référencée — par un
     * fil de discussion ou un document justificatif — en s'assurant qu'elle
     * appartient bien au membership fourni. Retourne un tableau prêt à être
     * fusionné dans MemberMessage::create()/MemberDocument::create(), vide
     * si aucune référence n'est demandée.
     *
     * @return array{related_type?: string, related_id?: int}
     */
    private function resolveDeclarationRelation(Membership $membership, ?string $type, ?int $id): array
    {
        if (! $type) {
            return [];
        }

        $related = match ($type) {
            'problematic' => MembershipProblematic::where('membership_id', $membership->id)->find($id),
            'need' => MembershipNeed::where('membership_id', $membership->id)->find($id),
            default => null,
        };

        abort_unless($related, 404);

        return [
            'related_type' => $related::class,
            'related_id' => $related->id,
        ];
    }
}
