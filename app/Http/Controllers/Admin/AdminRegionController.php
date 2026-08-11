<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRegionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return view('admin.regions.index', [
            'regions' => Region::withCount('moughataas')->orderBy('display_order')->get(),
        ]);
    }

    public function edit(Request $request, Region $region): View
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return view('admin.regions.edit', ['region' => $region]);
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $region->update([
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.regions.index')->with('status', __('regions.flash_updated'));
    }
}
