<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\MembershipDraftService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileOverviewController extends Controller
{
    public function show(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        return view('profile.overview', [
            'user' => $request->user(),
            'profile' => $request->user()->profile,
            'membership' => $membership->load(['problematics', 'documents']),
        ]);
    }
}
