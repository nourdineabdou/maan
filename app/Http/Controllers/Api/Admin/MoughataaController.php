<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MoughataaResource;
use App\Models\Moughataa;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MoughataaController extends ApiController
{
    public function index(Request $request, Region $region): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return response()->json([
            'data' => MoughataaResource::collection(
                $region->moughataas()->withCount('communes')->orderBy('display_order')->get()
            ),
        ]);
    }

    public function show(Request $request, Moughataa $moughataa): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return response()->json(['data' => MoughataaResource::make($moughataa->load('region'))]);
    }

    public function update(Request $request, Moughataa $moughataa): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $moughataa->update([
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['data' => MoughataaResource::make($moughataa->fresh())]);
    }
}
