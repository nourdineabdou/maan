<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberDocument;
use App\Models\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class AdminDocumentController extends Controller
{
    public function index(Request $request): View
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

        // Regroupées par membre : chaque membre n'a que ses propres
        // documents, plutôt qu'un mélange de tout le monde dans une seule
        // liste plate.
        $memberships = Membership::query()
            ->with(['user.profile', 'documents' => $documentFilter])
            ->whereHas('documents', $documentFilter)
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.index', [
            'memberships' => $memberships,
            'filters' => array_merge(
                ['type' => '', 'verified' => ''],
                $request->only(['type', 'verified'])
            ),
        ]);
    }

    public function download(Request $request, MemberDocument $document): StreamedResponse
    {
        abort_unless($request->user()->can('documents.view'), 403);

        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    public function downloadZip(Request $request, Membership $membership): BinaryFileResponse
    {
        abort_unless($request->user()->can('documents.view'), 403);

        $documents = $membership->documents;

        abort_if($documents->isEmpty(), 404);

        $zipDirectory = storage_path('app/tmp');

        if (! is_dir($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        $zipPath = $zipDirectory.'/documents-'.$membership->registration_number.'-'.time().'.zip';

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $usedNames = [];

        foreach ($documents as $document) {
            if (! Storage::disk('public')->exists($document->file_path)) {
                continue;
            }

            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
            $label = preg_replace('/[\\\\\/:*?"<>|]+/', '-', __('documents.type_'.$document->document_type));

            $entryName = $label.'.'.$extension;
            $usedNames[$label] = ($usedNames[$label] ?? 0) + 1;

            if ($usedNames[$label] > 1) {
                $entryName = $label.'-'.$usedNames[$label].'.'.$extension;
            }

            $zip->addFile(Storage::disk('public')->path($document->file_path), $entryName);
        }

        $zip->close();

        return response()->download($zipPath, 'documents-'.$membership->registration_number.'.zip')
            ->deleteFileAfterSend();
    }

    public function verify(Request $request, MemberDocument $document): RedirectResponse
    {
        abort_unless($request->user()->can('documents.verify'), 403);

        $document->update([
            'is_verified' => true,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return back()->with('status', __('documents.flash_verified'));
    }
}
