<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\PersonalInfoRequest;
use App\Models\Region;
use App\Services\ProfilePhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonalInfoController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.personal-edit', [
            'profile' => $request->user()->profile,
            'regions' => Region::where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function update(PersonalInfoRequest $request): RedirectResponse
    {
        $request->user()->profile->update($request->validated());

        return redirect()
            ->route('profile.personal.edit')
            ->with('status', __('profile.flash_saved'));
    }

    public function updatePhoto(Request $request, ProfilePhotoService $photoService): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $photoService->update($request->user(), $request->file('photo'));

        return redirect()
            ->back()
            ->with('status', __('profile.flash_photo_updated'));
    }
}
