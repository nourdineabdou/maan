<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\ProblematicResource;
use App\Models\Problematic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProblematicController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        return response()->json([
            'data' => ProblematicResource::collection(Problematic::orderBy('display_order')->get()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $data = $this->validateData($request);

        $problematic = Problematic::create([
            'code' => $data['code'],
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'description' => ['fr' => $data['description_fr'] ?? '', 'ar' => $data['description_ar'] ?? ''],
            'icon' => $data['icon'] ?? null,
            'requires_justification' => $request->boolean('requires_justification'),
            'is_active' => true,
            'display_order' => Problematic::max('display_order') + 1,
        ]);

        return response()->json(['data' => ProblematicResource::make($problematic)], 201);
    }

    public function show(Request $request, Problematic $problematic): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        return response()->json(['data' => ProblematicResource::make($problematic)]);
    }

    public function update(Request $request, Problematic $problematic): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $data = $this->validateData($request, $problematic);

        $problematic->update([
            'code' => $data['code'],
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'description' => ['fr' => $data['description_fr'] ?? '', 'ar' => $data['description_ar'] ?? ''],
            'icon' => $data['icon'] ?? null,
            'requires_justification' => $request->boolean('requires_justification'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['data' => ProblematicResource::make($problematic->fresh())]);
    }

    public function destroy(Request $request, Problematic $problematic): JsonResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $problematic->delete();

        return response()->json(['data' => ['status' => 'deleted']]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Problematic $ignore = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:60', 'unique:problematics,code'.($ignore ? ",{$ignore->id}" : '')],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description_fr' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:60'],
            'requires_justification' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
