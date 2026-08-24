<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('users.manage'), 403);

        return view('admin.users.index', [
            'administrators' => User::role('administrateur')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('users.manage'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'preferred_locale' => ['required', 'in:fr,ar'],
        ]);

        if (blank($data['phone'] ?? null) && blank($data['email'] ?? null)) {
            return back()
                ->withErrors(['phone' => __('admin_users.identifier_required')])
                ->withInput();
        }

        $administrator = User::create([
            ...$data,
            'is_active' => true,
        ]);
        $administrator->assignRole('administrateur');

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('admin_users.created'));
    }
}