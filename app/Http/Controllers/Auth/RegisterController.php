<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Region;
use App\Services\MemberRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
    public function register(RegisterRequest $request, MemberRegistrationService $registrationService): RedirectResponse
    {
        $user = $registrationService->register($request);

        Auth::login($user);

        session(['locale' => $user->preferred_locale]);

        return redirect()
            ->route('dashboard')
            ->with('status', __('profile.flash_submitted'));
    }
}
