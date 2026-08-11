<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\ProfessionalInfoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfessionalInfoController extends Controller
{
    /**
     * Contrairement aux informations personnelles et aux documents, les
     * informations professionnelles restent modifiables même après
     * validation de l'adhésion : la situation d'un membre (emploi,
     * secteur...) évolue dans le temps et n'a pas d'impact sur l'intégrité
     * du dossier déjà validé.
     */
    public function edit(Request $request): View
    {
        return view('profile.professional-edit', [
            'profile' => $request->user()->profile,
        ]);
    }

    public function update(ProfessionalInfoRequest $request): RedirectResponse
    {
        $request->user()->profile->update($request->validated());

        return redirect()
            ->route('profile.professional.edit')
            ->with('status', __('profile.flash_saved'));
    }
}
