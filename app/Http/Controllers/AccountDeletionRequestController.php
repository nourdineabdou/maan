<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Page publique (non authentifiée) requise par les stores : un moyen de
 * demander la suppression de son compte et de ses données, accessible même
 * sans pouvoir se connecter (app désinstallée, accès perdu...).
 */
class AccountDeletionRequestController extends Controller
{
    public function create(): View
    {
        return view('account-deletion.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'member_number' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        AccountDeletionRequest::create($data);

        return redirect()
            ->route('account-deletion.create')
            ->with('status', __('account_deletion.flash_submitted'));
    }
}
