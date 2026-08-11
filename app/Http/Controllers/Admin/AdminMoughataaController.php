<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moughataa;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMoughataaController extends Controller
{
    public function index(Request $request, Region $region): View
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return view('admin.regions.moughataas', [
            'region' => $region,
            'moughataas' => $region->moughataas()->withCount('communes')->orderBy('display_order')->get(),
        ]);
    }

    public function edit(Request $request, Moughataa $moughataa): View
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return view('admin.regions.moughataa-edit', ['moughataa' => $moughataa->load('region')]);
    }

    public function update(Request $request, Moughataa $moughataa): RedirectResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $moughataa->update([
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.regions.moughataas', $moughataa->region_id)
            ->with('status', __('regions.flash_updated'));
    }
}
