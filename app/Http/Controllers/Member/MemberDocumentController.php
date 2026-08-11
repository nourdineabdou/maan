<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\DocumentUploadRequest;
use App\Models\MemberDocument;
use App\Services\MembershipDraftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberDocumentController extends Controller
{
    public function index(Request $request, MembershipDraftService $draftService): View
    {
        $membership = $draftService->draftFor($request->user());

        return view('profile.documents', [
            'membership' => $membership->load('documents'),
        ]);
    }

    public function store(DocumentUploadRequest $request, MembershipDraftService $draftService): RedirectResponse
    {
        $membership = $draftService->draftFor($request->user());

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $membership->documents()->create([
            'document_type' => $request->input('document_type'),
            'title' => $request->input('title'),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // La photo de profil (avatar, affichée partout dans l'appli) et le
        // document "photo du membre" sont deux choses distinctes mais
        // représentent la même photo pour le membre. On renseigne l'avatar
        // automatiquement à partir de ce document tant qu'il n'a pas déjà
        // été défini, pour éviter de bloquer la soumission sur une photo
        // "manquante" alors qu'elle vient d'être envoyée ici.
        $profile = $request->user()->profile;

        if (
            $request->input('document_type') === 'member_photo'
            && ! $profile->photo_path
            && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png'], true)
        ) {
            $profile->update(['photo_path' => $file->store('profiles', 'public')]);
        }

        return redirect()
            ->back()
            ->with('status', __('profile.flash_document_uploaded'));
    }

    public function destroy(Request $request, MemberDocument $document): RedirectResponse
    {
        abort_unless($document->membership->user_id === $request->user()->id, 403);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()
            ->back()
            ->with('status', __('profile.flash_document_deleted'));
    }
}
