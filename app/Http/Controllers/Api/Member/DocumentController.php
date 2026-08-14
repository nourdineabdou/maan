<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Member\DocumentUploadRequest;
use App\Http\Resources\MemberDocumentResource;
use App\Models\MemberDocument;
use App\Services\MembershipDraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends ApiController
{
    public function index(Request $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        return response()->json(['data' => MemberDocumentResource::collection($membership->documents)]);
    }

    public function store(DocumentUploadRequest $request, MembershipDraftService $draftService): JsonResponse
    {
        $membership = $draftService->draftFor($request->user());

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = $membership->documents()->create([
            'document_type' => $request->input('document_type'),
            'title' => $request->input('title'),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // Cf. Member\MemberDocumentController::store (Web).
        $profile = $request->user()->profile;

        if (
            $request->input('document_type') === 'member_photo'
            && ! $profile->photo_path
            && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'], true)
        ) {
            $profile->update(['photo_path' => $file->store('profiles', 'public')]);
        }

        return response()->json(['data' => MemberDocumentResource::make($document)], 201);
    }

    public function destroy(Request $request, MemberDocument $document): JsonResponse
    {
        abort_unless($document->membership->user_id === $request->user()->id, 403);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['data' => ['status' => 'deleted']]);
    }
}
