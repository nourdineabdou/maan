<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Member\PersonalInfoRequest;
use App\Http\Requests\Member\ProfessionalInfoRequest;
use App\Http\Resources\MemberProfileResource;
use App\Services\ProfilePhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->profile()->with(['region', 'moughataaRef', 'communeRef'])->first();

        return response()->json(['data' => MemberProfileResource::make($profile)]);
    }

    public function updatePersonal(PersonalInfoRequest $request): JsonResponse
    {
        $request->user()->profile->update($request->validated());

        return response()->json([
            'data' => MemberProfileResource::make(
                $request->user()->profile()->with(['region', 'moughataaRef', 'communeRef'])->first()
            ),
        ]);
    }

    public function updateProfessional(ProfessionalInfoRequest $request): JsonResponse
    {
        $request->user()->profile->update($request->validated());

        return response()->json(['data' => MemberProfileResource::make($request->user()->profile)]);
    }

    public function updatePhoto(Request $request, ProfilePhotoService $photoService): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $profile = $photoService->update($request->user(), $request->file('photo'));

        return response()->json(['data' => MemberProfileResource::make($profile)]);
    }
}
