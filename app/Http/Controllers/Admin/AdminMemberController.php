<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMemberController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('members.view'), 403);

        $query = User::query()
            ->role('membre')
            ->with(['profile.region', 'latestMembership']);

        if ($regionId = $request->string('region')->toString()) {
            $query->whereHas('profile', fn ($q) => $q->where('region_id', $regionId));
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($profileQuery) use ($search) {
                        $profileQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('nni', 'like', "%{$search}%");
                    });
            });
        }

        return view('admin.members.index', [
            'members' => $query->latest()->paginate(20)->withQueryString(),
            'regions' => Region::orderBy('display_order')->get(),
            'filters' => array_merge(
                ['region' => '', 'q' => ''],
                $request->only(['region', 'q'])
            ),
        ]);
    }
}
