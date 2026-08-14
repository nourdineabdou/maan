<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MembershipResource;
use App\Models\Membership;
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
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'role' => 'membre',
                'membership' => MembershipResource::make($user->latestMembership),
            ],
        ]);
    }
}
