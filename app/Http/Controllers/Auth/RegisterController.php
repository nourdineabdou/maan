<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\MemberProfile;
use App\Models\MembershipStatusHistory;
use App\Models\Region;
use App\Models\User;
use App\Services\MembershipDraftService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register', [
            'regions' => Region::where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    /**
     * L'inscription réunit en une seule page tout ce qui est indispensable
     * pour ouvrir un dossier d'adhésion (photo, NNI, wilaya, moughataa, CNI
     * recto/verso) : le compte créé, la demande est donc envoyée à
     * l'administration immédiatement, sans étape supplémentaire. Le reste
     * (informations professionnelles, diplôme/CV, problématiques...) reste
     * modifiable ensuite depuis l'espace membre.
     */
    public function register(
        RegisterRequest $request,
        MembershipDraftService $draftService,
        NotificationService $notificationService,
    ): RedirectResponse {
        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'preferred_locale' => $request->input('preferred_locale'),
            'is_active' => true,
        ]);

        $user->assignRole('membre');

        [$firstName, $lastName] = $this->splitName($request->input('name'));

        $photo = $request->file('photo');

        MemberProfile::create([
            'user_id' => $user->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => $request->input('gender'),
            'nni' => $request->input('nni'),
            'region_id' => $request->input('region_id'),
            'moughataa_id' => $request->input('moughataa_id'),
            'photo_path' => $photo->store('profiles', 'public'),
        ]);

        $membership = $draftService->draftFor($user);

        $documents = [
            'member_photo' => $photo,
            'identity_card_front' => $request->file('identity_card_front'),
            'identity_card_back' => $request->file('identity_card_back'),
        ];

        foreach ($documents as $documentType => $file) {
            $membership->documents()->create([
                'document_type' => $documentType,
                'file_path' => $file->store('documents', 'public'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        $membership->update(['submitted_at' => now()]);

        MembershipStatusHistory::create([
            'membership_id' => $membership->id,
            'new_status' => 'pending',
            'changed_by' => $user->id,
        ]);

        $admins = User::role('administrateur')->get();

        if ($admins->isNotEmpty()) {
            $notificationService->send(
                recipients: $admins,
                title: [
                    'fr' => __('profile.submission_notification_title', [], 'fr'),
                    'ar' => __('profile.submission_notification_title', [], 'ar'),
                ],
                message: [
                    'fr' => __('profile.submission_notification_body', ['name' => $user->display_name], 'fr'),
                    'ar' => __('profile.submission_notification_body', ['name' => $user->display_name], 'ar'),
                ],
                sender: $user,
                actionUrl: route('admin.memberships.show', $membership),
            );
        }

        Auth::login($user);

        session(['locale' => $user->preferred_locale]);

        return redirect()
            ->route('dashboard')
            ->with('status', __('profile.flash_submitted'));
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? $name, $parts[1] ?? ''];
    }
}
