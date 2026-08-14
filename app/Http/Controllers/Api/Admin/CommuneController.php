<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CommuneResource;
use App\Models\Commune;
use App\Models\Moughataa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommuneController extends ApiController
{
    public function index(Request $request, Moughataa $moughataa): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return response()->json([
            'data' => CommuneResource::collection($moughataa->communes()->orderBy('display_order')->get()),
        ]);
    }

    public function store(Request $request, Moughataa $moughataa): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
        ]);

        $commune = $moughataa->communes()->create([
            'code' => $data['code'],
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'display_order' => $moughataa->communes()->count() + 1,
            'is_active' => true,
        ]);

        return response()->json(['data' => CommuneResource::make($commune)], 201);
    }

    public function update(Request $request, Commune $commune): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $commune->update([
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['data' => CommuneResource::make($commune->fresh())]);
    }

    public function destroy(Request $request, Commune $commune): JsonResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $commune->delete();

        return response()->json(['data' => ['status' => 'deleted']]);
    }
}
