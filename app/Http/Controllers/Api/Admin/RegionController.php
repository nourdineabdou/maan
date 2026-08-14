<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return response()->json([
            'data' => RegionResource::collection(
                Region::withCount('moughataas')->orderBy('display_order')->get()
            ),
        ]);
    }

    public function show(Request $request, Region $region): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return response()->json(['data' => RegionResource::make($region)]);
    }

    public function update(Request $request, Region $region): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $region->update([
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['data' => RegionResource::make($region->fresh())]);
    }
}
