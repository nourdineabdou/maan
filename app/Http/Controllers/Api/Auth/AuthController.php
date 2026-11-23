<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\MemberRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request, MemberRegistrationService $registrationService): JsonResponse
    {
        $user = $registrationService->register($request);

        $user->forceFill(['registration_platform' => $this->clientPlatform($request)])->save();

        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return response()->json([
            'data' => [
                'user' => UserResource::make($user->load('profile')),
                'token' => $token,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $identifier = $request->string('identifier')->trim()->toString();

        $field = str_contains($identifier, '@') ? 'email' : 'phone';

        $user = User::where($field, $identifier)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return response()->json([
            'data' => [
                'user' => UserResource::make($user->load('profile')),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['data' => ['status' => 'logged_out']]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => UserResource::make(
                $request->user()->load(['profile.region', 'profile.moughataaRef', 'profile.communeRef'])
            ),
        ]);
    }

    /**
     * Plateforme de l'app mobile : en-tête X-Client-Platform envoyé par
     * l'app (builds récents), sinon déduite du user-agent pour les builds
     * plus anciens (iOS : CFNetwork/Darwin, Android : okhttp).
     */
    private function clientPlatform(Request $request): ?string
    {
        $header = strtolower((string) $request->header('X-Client-Platform'));

        if (in_array($header, ['ios', 'android'], true)) {
            return $header;
        }

        $userAgent = (string) $request->userAgent();

        return match (true) {
            str_contains($userAgent, 'Darwin') || str_contains($userAgent, 'CFNetwork') || str_contains($userAgent, 'iPhone') => 'ios',
            str_contains($userAgent, 'okhttp') || str_contains($userAgent, 'Android') => 'android',
            default => null,
        };
    }

    /**
     * Nom du token dérivé du user-agent, pour pouvoir distinguer/révoquer
     * les sessions par appareil depuis un futur écran "mes appareils".
     */
    private function tokenName(Request $request): string
    {
        return 'mobile:'.substr((string) $request->userAgent(), 0, 100);
    }
}
