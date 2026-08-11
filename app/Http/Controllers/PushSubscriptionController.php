<?php

namespace App\Http\Controllers;

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
}
