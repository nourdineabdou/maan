<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        $announcements = Announcement::with(['creator', 'images'])->latest()->paginate(20);

        return response()->json([
            'data' => AnnouncementResource::collection($announcements),
            'meta' => $this->paginationMeta($announcements),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        $announcement = Announcement::create([
            ...$this->validated($request),
            'created_by' => $request->user()->id,
        ]);

        $this->storeImages($request, $announcement);

        return response()->json(['data' => AnnouncementResource::make($announcement->load('images'))], 201);
    }

    public function show(Request $request, Announcement $announcement): JsonResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        return response()->json(['data' => AnnouncementResource::make($announcement->load('images'))]);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        $announcement->update($this->validated($request));

        $this->storeImages($request, $announcement);

        return response()->json(['data' => AnnouncementResource::make($announcement->fresh('images'))]);
    }

    public function destroy(Request $request, Announcement $announcement): JsonResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);

        foreach ($announcement->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }

        $announcement->delete();

        return response()->json(['data' => ['status' => 'deleted']]);
    }

    public function destroyImage(Request $request, Announcement $announcement, AnnouncementImage $image): JsonResponse
    {
        abort_unless($request->user()->can('announcements.manage'), 403);
        abort_unless($image->announcement_id === $announcement->id, 404);

        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return response()->json(['data' => ['status' => 'deleted']]);
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
