<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Services\PublicStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends ApiController
{
    public function index(Request $request, PublicStatisticsService $statistics): JsonResponse
    {
        abort_unless($request->user()->can('statistics.view'), 403);

        return response()->json(['data' => $statistics->summary()]);
    }
}
