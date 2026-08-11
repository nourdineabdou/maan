<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problematic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProblematicController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        return view('admin.problematics.index', [
            'problematics' => Problematic::orderBy('display_order')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        return view('admin.problematics.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $data = $this->validateData($request);

        Problematic::create([
            'code' => $data['code'],
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'description' => ['fr' => $data['description_fr'] ?? '', 'ar' => $data['description_ar'] ?? ''],
            'icon' => $data['icon'] ?? null,
            'requires_justification' => $request->boolean('requires_justification'),
            'is_active' => true,
            'display_order' => Problematic::max('display_order') + 1,
        ]);

        return redirect()->route('admin.problematics.index')->with('status', __('problematics.flash_created'));
    }

    public function edit(Request $request, Problematic $problematic): View
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        return view('admin.problematics.edit', ['problematic' => $problematic]);
    }

    public function update(Request $request, Problematic $problematic): RedirectResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $data = $this->validateData($request, $problematic);

        $problematic->update([
            'code' => $data['code'],
            'name' => ['fr' => $data['name_fr'], 'ar' => $data['name_ar']],
            'description' => ['fr' => $data['description_fr'] ?? '', 'ar' => $data['description_ar'] ?? ''],
            'icon' => $data['icon'] ?? null,
            'requires_justification' => $request->boolean('requires_justification'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.problematics.index')->with('status', __('problematics.flash_updated'));
    }

    public function destroy(Request $request, Problematic $problematic): RedirectResponse
    {
        abort_unless($request->user()->can('problematics.manage'), 403);

        $problematic->delete();

        return redirect()->route('admin.problematics.index')->with('status', __('problematics.flash_deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Problematic $ignore = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:60', 'unique:problematics,code'.($ignore ? ",{$ignore->id}" : '')],
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description_fr' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:60'],
            'requires_justification' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
