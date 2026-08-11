<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->hasRole('administrateur')) {
            return view('dashboard.admin', [
                'totalMembers' => Membership::count(),
                'pendingCount' => Membership::where('status', 'pending')->count(),
                'approvedCount' => Membership::where('status', 'approved')->count(),
                'rejectedCount' => Membership::where('status', 'rejected')->count(),
            ]);
        }

        return view('dashboard.member', [
            'membership' => $user->latestMembership,
        ]);
    }
}
