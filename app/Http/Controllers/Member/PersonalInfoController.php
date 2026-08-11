<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\PersonalInfoRequest;
use App\Models\Region;
use App\Services\MembershipDraftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function updatePhoto(Request $request, MembershipDraftService $draftService): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $user = $request->user();
        $profile = $user->profile;
        $file = $request->file('photo');

        if ($profile->photo_path) {
            Storage::disk('public')->delete($profile->photo_path);
        }

        $profile->update([
            'photo_path' => $file->store('profiles', 'public'),
        ]);

        // Cette photo de profil vaut aussi comme document "photo du membre"
        // requis pour l'adhésion : on évite ainsi de demander la même photo
        // deux fois si le membre n'a pas encore envoyé ce document.
        $membership = $draftService->draftFor($user);

        if (! $membership->documents()->where('document_type', 'member_photo')->exists()) {
            $membership->documents()->create([
                'document_type' => 'member_photo',
                'file_path' => $file->store('documents', 'public'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return redirect()
            ->back()
            ->with('status', __('profile.flash_photo_updated'));
    }
}
