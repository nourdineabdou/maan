<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\RegionResource;
use App\Models\Announcement;
use App\Models\Membership;
use App\Models\Region;
use Illuminate\Http\JsonResponse;

class PublicController extends ApiController
{
    public function regions(): JsonResponse
    {
        return response()->json([
            'data' => RegionResource::collection(
                Region::where('is_active', true)->orderBy('display_order')->get()
            ),
        ]);
    }

    public function activeAnnouncement(): JsonResponse
    {
        $announcement = Announcement::where('is_active', true)->with('images')->latest()->first();

        return response()->json(['data' => $announcement ? AnnouncementResource::make($announcement) : null]);
    }

    /**
     * Vérification publique d'une carte de membre à partir du QR code.
     * N'expose volontairement que les informations non sensibles (cf.
     * MembershipVerificationController Web) : pas de NNI, pas de documents,
     * pas d'adresse, pas de notes internes.
     */
    public function verifyMembership(string $token): JsonResponse
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

        return response()->json([
            'data' => [
                'status' => $status,
                'member_number' => $membership?->member_number,
                'full_name' => $membership?->user?->profile?->full_name,
                'member_since' => $membership?->approved_at,
            ],
        ]);
    }
}
