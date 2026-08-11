<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use App\Models\Moughataa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCommuneController extends Controller
{
    public function index(Request $request, Moughataa $moughataa): View
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        return view('admin.regions.communes', [
            'moughataa' => $moughataa->load('region'),
            'communes' => $moughataa->communes()->orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request, Moughataa $moughataa): RedirectResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
        ]);

        $moughataa->communes()->create([
            'code' => $data['code'],
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'display_order' => $moughataa->communes()->count() + 1,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.moughataas.communes', $moughataa)
            ->with('status', __('regions.flash_commune_created'));
    }

    public function update(Request $request, Commune $commune): RedirectResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $data = $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $commune->update([
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.moughataas.communes', $commune->moughataa_id)
            ->with('status', __('regions.flash_updated'));
    }

    public function destroy(Request $request, Commune $commune): RedirectResponse
    {
        abort_unless($request->user()->can('regions.manage'), 403);

        $moughataaId = $commune->moughataa_id;
        $commune->delete();

        return redirect()
            ->route('admin.moughataas.communes', $moughataaId)
            ->with('status', __('regions.flash_commune_deleted'));
    }
}
