<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Announcement;
use App\Models\User;
use App\Services\PublicStatisticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(PublicStatisticsService $statistics): View
    {
        return view('auth.login', [
            'stats' => $statistics->summary(),
            'announcement' => Announcement::where('is_active', true)->with('images')->latest()->first(),
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $identifier = $request->string('identifier')->trim()->toString();

        $field = str_contains($identifier, '@') ? 'email' : 'phone';

        $user = User::where($field, $identifier)->first();

        if (! $user || ! Auth::attempt([$field => $identifier, 'password' => $request->input('password')], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
