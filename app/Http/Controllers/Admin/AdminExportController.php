<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MembersExport;
use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdminExportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('members.export'), 403);

        return view('admin.exports.index', [
            'regions' => Region::orderBy('display_order')->get(),
        ]);
    }

    public function excel(Request $request)
    {
        abort_unless($request->user()->can('members.export'), 403);

        $filters = $request->only(['status', 'region']);

        return Excel::download(new MembersExport($filters), 'membres-'.now()->format('Y-m-d').'.xlsx');
    }

    public function pdf(Request $request)
    {
        abort_unless($request->user()->can('members.export'), 403);

        $query = Membership::query()->with(['user.profile.region']);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($regionId = $request->string('region')->toString()) {
            $query->whereHas('user.profile', fn ($q) => $q->where('region_id', $regionId));
        }

        $memberships = $query->latest()->get();

        $pdf = Pdf::loadView('admin.exports.pdf', ['memberships' => $memberships])
            ->setPaper('a4', 'landscape');

        return $pdf->download('membres-'.now()->format('Y-m-d').'.pdf');
    }
}
