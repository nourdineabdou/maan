<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MemberDocumentResource;
use App\Models\MemberDocument;
use App\Models\Membership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('documents.view'), 403);

        $documentFilter = function ($query) use ($request) {
            if ($type = $request->string('type')->toString()) {
                $query->where('document_type', $type);
            }

            if ($request->filled('verified')) {
                $query->where('is_verified', $request->boolean('verified'));
            }
        };

        $memberships = Membership::query()
            ->with(['user.profile', 'documents' => $documentFilter])
            ->whereHas('documents', $documentFilter)
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return response()->json([
            'data' => $memberships->map(fn (Membership $membership) => [
                'membership_id' => $membership->id,
                'registration_number' => $membership->registration_number,
                'user' => [
                    'id' => $membership->user->id,
                    'name' => $membership->user->display_name,
                ],
                'documents' => MemberDocumentResource::collection($membership->documents),
            ]),
            'meta' => $this->paginationMeta($memberships),
        ]);
    }

    public function download(Request $request, MemberDocument $document): StreamedResponse
    {
        abort_unless($request->user()->can('documents.view'), 403);

        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    public function verify(Request $request, MemberDocument $document): JsonResponse
    {
        abort_unless($request->user()->can('documents.verify'), 403);

        $document->update([
            'is_verified' => true,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return response()->json(['data' => MemberDocumentResource::make($document->fresh())]);
    }
}
