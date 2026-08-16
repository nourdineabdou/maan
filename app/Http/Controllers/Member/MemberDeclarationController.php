<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MembershipProblematic;
use App\Models\Problematic;
use App\Services\MembershipDraftService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vues de suivi (lecture + filtres) des déclarations du membre connecté —
 * distinctes des pages de création (profile.need.edit, profile.problematics.edit)
 * qui restent le point d'entrée pour déclarer/modifier. Miroir member-scopé
 * de Admin\AdminDeclarationController.
 */
class MemberDeclarationController extends Controller
{
    public function needs(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        $query = $membership->needs()->with('documents');
        $this->applyFilters($query, $request);

        return view('profile.declarations.needs', [
            'needs' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $this->filters($request),
        ]);
    }

    public function problematics(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        $query = MembershipProblematic::query()
            ->where('membership_id', $membership->id)
            ->with(['problematic', 'documents']);

        $this->applyFilters($query, $request);

        if ($problematicId = $request->string('problematic_id')->toString()) {
            $query->where('problematic_id', $problematicId);
        }

        return view('profile.declarations.problematics', [
            'declarations' => $query->latest()->get(),
            'problematicTypes' => Problematic::orderBy('display_order')->get(),
            'filters' => array_merge($this->filters($request), [
                'problematic_id' => $request->string('problematic_id')->toString(),
            ]),
        ]);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->string('date_from')->toString()) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->string('date_to')->toString()) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
    }

    private function filters(Request $request): array
    {
        return array_merge(
            ['status' => '', 'date_from' => '', 'date_to' => ''],
            $request->only(['status', 'date_from', 'date_to'])
        );
    }
}
