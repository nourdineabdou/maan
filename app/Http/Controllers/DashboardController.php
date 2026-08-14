<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Services\PublicStatisticsService;
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
            ]);
        }

        return view('dashboard.member', [
            'membership' => $user->latestMembership,
        ]);
    }
}
