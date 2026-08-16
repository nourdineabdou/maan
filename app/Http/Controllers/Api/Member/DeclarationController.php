<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MembershipNeedResource;
use App\Http\Resources\MembershipProblematicResource;
use App\Models\MembershipProblematic;
use App\Services\MembershipDraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeclarationController extends ApiController
{
    public function needs(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        $query = $membership->needs()->with('documents');
        $this->applyFilters($query, $request);

        $needs = $query->latest()->paginate(10)->withQueryString();

        return response()->json([
            'data' => MembershipNeedResource::collection($needs),
            'meta' => $this->paginationMeta($needs),
        ]);
    }

    public function problematics(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        $query = MembershipProblematic::query()
            ->where('membership_id', $membership->id)
            ->with(['problematic', 'documents']);

        $this->applyFilters($query, $request);

        if ($problematicId = $request->string('problematic_id')->toString()) {
            $query->where('problematic_id', $problematicId);
        }

        return response()->json([
            'data' => MembershipProblematicResource::collection($query->latest()->get()),
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
