<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\View\View;

class MembershipVerificationController extends Controller
{
    /**
     * Page publique de vérification d'une carte de membre à partir du QR
     * code. N'expose volontairement que les informations non sensibles :
     * pas de NNI, pas de documents, pas d'adresse, pas de notes internes.
     */
    public function show(string $token): View
    {
        $membership = Membership::with('user.profile')
            ->where('qr_token', $token)
            ->first();

        $status = match (true) {
            ! $membership => 'not_found',
            ! $membership->card_is_active => 'suspended',
            $membership->status !== 'approved' => 'invalid',
            default => 'valid',
        };

        return view('membership.verify', [
            'membership' => $membership,
            'status' => $status,
        ]);
    }
}
