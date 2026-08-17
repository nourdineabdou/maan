<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MembershipResource;
use App\Models\Membership;
use App\Models\MembershipNeed;
use App\Models\MembershipProblematic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('administrateur')) {
            return response()->json([
                'data' => [
                    'role' => 'administrateur',
                    'total_members' => Membership::count(),
                    'pending_count' => Membership::where('status', 'pending')->count(),
                    'approved_count' => Membership::where('status', 'approved')->count(),
                    'rejected_count' => Membership::where('status', 'rejected')->count(),
                    'need_counts' => $this->statusCounts(MembershipNeed::query()),
                    'problematic_counts' => $this->statusCounts(MembershipProblematic::query()),
                ],
            ]);
        }

        $membership = $user->latestMembership;

        return response()->json([
            'data' => [
                'role' => 'membre',
                'membership' => MembershipResource::make($membership),
                'need_counts' => $this->statusCounts(MembershipNeed::query()->where('membership_id', $membership?->id)),
                'problematic_counts' => $this->statusCounts(MembershipProblematic::query()->where('membership_id', $membership?->id)),
            ],
        ]);
    }

    /**
     * @return array{submitted: int, in_progress: int, resolved: int}
     */
    private function statusCounts(Builder $query): array
    {
        $counts = (clone $query)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'submitted' => $counts['submitted'] ?? 0,
            'in_progress' => $counts['in_progress'] ?? 0,
            'resolved' => $counts['resolved'] ?? 0,
        ];
    }
}
