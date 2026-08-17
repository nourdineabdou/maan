<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipNeed;
use App\Models\MembershipProblematic;
use App\Services\PublicStatisticsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, PublicStatisticsService $statistics): View
    {
        $user = $request->user();

        if ($user->hasRole('administrateur')) {
            return view('dashboard.admin', [
                'totalMembers' => Membership::count(),
                'pendingCount' => Membership::where('status', 'pending')->count(),
                'approvedCount' => Membership::where('status', 'approved')->count(),
                'rejectedCount' => Membership::where('status', 'rejected')->count(),
                'stats' => $statistics->summary(),
                'needCounts' => $this->statusCounts(MembershipNeed::query()),
                'problematicCounts' => $this->statusCounts(MembershipProblematic::query()),
            ]);
        }

        $membership = $user->latestMembership;

        return view('dashboard.member', [
            'membership' => $membership,
            'needCounts' => $this->statusCounts(MembershipNeed::query()->where('membership_id', $membership?->id)),
            'problematicCounts' => $this->statusCounts(MembershipProblematic::query()->where('membership_id', $membership?->id)),
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
