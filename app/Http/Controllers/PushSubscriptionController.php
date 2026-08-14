<?php

namespace App\Http\Controllers;

use App\Models\ExpoPushToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->user()->updatePushSubscription(
            endpoint: $request->input('endpoint'),
            publicKey: $request->input('keys.p256dh'),
            authToken: $request->input('keys.auth'),
            contentEncoding: 'aesgcm',
        );

        return response()->json(['status' => 'subscribed']);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->user()->deletePushSubscription($request->input('endpoint'));

        return response()->json(['status' => 'unsubscribed']);
    }

    /**
     * Enregistre (ou réattribue) un jeton Expo push pour l'app mobile —
     * équivalent de subscribe() ci-dessus, pour le canal ExpoPushChannel.
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Un même appareil peut avoir été enregistré par un autre compte
        // auparavant (déconnexion/reconnexion) : on le réattribue plutôt que
        // de violer la contrainte d'unicité sur le jeton.
        ExpoPushToken::where('token', $data['token'])
            ->where('user_id', '!=', $request->user()->id)
            ->delete();

        $request->user()->expoPushTokens()->updateOrCreate(
            ['token' => $data['token']],
            ['device_name' => $data['device_name'] ?? null],
        );

        return response()->json(['status' => 'registered']);
    }

    public function unregisterDevice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $request->user()->expoPushTokens()->where('token', $data['token'])->delete();

        return response()->json(['status' => 'unregistered']);
    }
}
