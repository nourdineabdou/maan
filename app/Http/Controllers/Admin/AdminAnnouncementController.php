<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        return view('admin.announcements.index', [
            'announcements' => Announcement::with(['creator', 'images'])->latest()->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        return view('admin.announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        $data = $this->validated($request);

        $announcement = Announcement::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        $this->storeImages($request, $announcement);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', __('announcements.flash_created'));
    }

    public function edit(Request $request, Announcement $announcement): View
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        return view('admin.announcements.edit', [
            'announcement' => $announcement->load('images'),
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        $announcement->update($this->validated($request));

        $this->storeImages($request, $announcement);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', __('announcements.flash_updated'));
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        foreach ($announcement->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }

        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', __('announcements.flash_deleted'));
    }

    public function destroyImage(Request $request, Announcement $announcement, AnnouncementImage $image): RedirectResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);
        abort_unless($image->announcement_id === $announcement->id, 404);

        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return redirect()
            ->route('admin.announcements.edit', $announcement)
            ->with('status', __('announcements.flash_image_deleted'));
    }

    private function storeImages(Request $request, Announcement $announcement): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $nextOrder = $announcement->images()->max('display_order') + 1;

        foreach ($request->file('images') as $file) {
            $announcement->images()->create([
                'file_path' => $file->store('announcements', 'public'),
                'original_name' => $file->getClientOriginalName(),
                'display_order' => $nextOrder++,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_fr' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'message_fr' => ['required', 'string', 'max:2000'],
            'message_ar' => ['required', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        return [
            'title' => ['fr' => $data['title_fr'], 'ar' => $data['title_ar']],
            'message' => ['fr' => $data['message_fr'], 'ar' => $data['message_ar']],
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
