<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('members.view'), 403);

        $query = User::query()
            ->role('membre')
            ->with(['profile.region', 'latestMembership']);

        if ($regionId = $request->string('region')->toString()) {
            $query->whereHas('profile', fn ($q) => $q->where('region_id', $regionId));
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($profileQuery) use ($search) {
                        $profileQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('nni', 'like', "%{$search}%");
                    });
            });
        }

        $members = $query->latest()->paginate(20)->withQueryString();

        return response()->json([
            'data' => UserResource::collection($members),
            'meta' => $this->paginationMeta($members),
        ]);
    }
}
