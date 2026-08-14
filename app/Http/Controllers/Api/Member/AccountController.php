<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends ApiController
{
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'string', 'min:4'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['data' => ['status' => 'updated']]);
    }

    public function updateLocale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:'.implode(',', config('localization.supported_locales', ['fr']))],
        ]);

        $request->user()->update(['preferred_locale' => $data['locale']]);

        return response()->json(['data' => ['status' => 'updated', 'locale' => $data['locale']]]);
    }
}
