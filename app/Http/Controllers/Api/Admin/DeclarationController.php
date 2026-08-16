<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipNeedResource;
use App\Http\Resources\MembershipProblematicResource;
use App\Models\MembershipNeed;
use App\Models\MembershipProblematic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeclarationController extends ApiController
{
    public function needs(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $query = MembershipNeed::query()->with('membership.user.profile');

        $this->applyFilters($query, $request);

        $needs = $query->latest()->paginate(20)->withQueryString();

        return response()->json([
            'data' => MembershipNeedResource::collection($needs),
            'meta' => $this->paginationMeta($needs),
        ]);
    }

    public function problematics(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $query = MembershipProblematic::query()->with(['membership.user.profile', 'problematic']);

        $this->applyFilters($query, $request);

        if ($problematicId = $request->string('problematic_id')->toString()) {
            $query->where('problematic_id', $problematicId);
        }

        $declarations = $query->latest()->paginate(20)->withQueryString();

        return response()->json([
            'data' => MembershipProblematicResource::collection($declarations),
            'meta' => $this->paginationMeta($declarations),
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->string('date_from')->toString()) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->string('date_to')->toString()) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
    }
}
