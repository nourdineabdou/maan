<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Services\AccountDeletionService;
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

    /**
     * Suppression définitive du compte par le membre lui-même. Le mot de
     * passe est redemandé pour éviter une suppression accidentelle ou
     * depuis un appareil laissé déverrouillé.
     */
    public function destroy(Request $request, AccountDeletionService $deletionService): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Les comptes d'administration ne sont pas créés depuis l'app et
        // leurs réponses aux membres partiraient en cascade : ils restent
        // gérés côté back-office.
        abort_if(
            $user->hasAnyRole(['administrateur', 'moderateur']),
            403,
            __('account_deletion.staff_forbidden')
        );

        $deletionService->delete($user);

        return response()->json(['data' => ['status' => 'deleted']]);
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
