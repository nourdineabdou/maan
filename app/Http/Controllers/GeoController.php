<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Moughataa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function moughataas(Request $request): JsonResponse
    {
        $regionId = $request->integer('region_id');

        $moughataas = Moughataa::where('region_id', $regionId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'name']);

        return response()->json(
            $moughataas->map(fn ($m) => ['id' => $m->id, 'name' => $m->getTranslation('name')])
        );
    }

    public function communes(Request $request): JsonResponse
    {
        $moughataaId = $request->integer('moughataa_id');

        $communes = Commune::where('moughataa_id', $moughataaId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'name']);

        return response()->json(
            $communes->map(fn ($c) => ['id' => $c->id, 'name' => $c->getTranslation('name')])
        );
    }
}
