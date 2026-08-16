<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipNeed;
use App\Models\MembershipProblematic;
use App\Models\Problematic;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vues d'ensemble, tous membres confondus, des besoins et problématiques
 * déclarés — distinctes de admin.memberships.show (qui ne montre que les
 * déclarations d'un seul membre) et de AdminProblematicController (qui gère
 * le catalogue des types de problématiques, pas les déclarations elles-mêmes).
 */
class AdminDeclarationController extends Controller
{
    public function needs(Request $request): View
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $query = MembershipNeed::query()->with(['membership.user.profile', 'documents']);

        $this->applyFilters($query, $request);

        return view('admin.declarations.needs', [
            'needs' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $this->filters($request),
        ]);
    }

    public function problematics(Request $request): View
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $query = MembershipProblematic::query()->with(['membership.user.profile', 'problematic', 'documents']);

        $this->applyFilters($query, $request);

        if ($problematicId = $request->string('problematic_id')->toString()) {
            $query->where('problematic_id', $problematicId);
        }

        return view('admin.declarations.problematics', [
            'declarations' => $query->latest()->paginate(20)->withQueryString(),
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
