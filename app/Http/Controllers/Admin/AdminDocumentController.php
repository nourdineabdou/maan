<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberDocument;
use App\Models\Membership;
use App\Models\MembershipNeed;
use App\Models\MembershipProblematic;
use App\Models\Problematic;
use Illuminate\Database\Eloquent\Collection;
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

        return $this->zipDocuments(
            $membership->documents,
            'documents-'.$membership->registration_number,
            fn (MemberDocument $document) => preg_replace('/[\\\\\/:*?"<>|]+/', '-', __('documents.type_'.$document->document_type)),
        );
    }

    /**
     * Toutes les pièces jointes d'UNE déclaration précise (un besoin ou une
     * problématique peut avoir plusieurs fichiers) — nommé par référence
     * d'adhésion + déclaration pour rester identifiable une fois téléchargé.
     */
    public function downloadNeedZip(Request $request, Membership $membership, MembershipNeed $need): BinaryFileResponse
    {
        abort_unless($request->user()->can('documents.view'), 403);
        abort_unless($need->membership_id === $membership->id, 404);

        return $this->zipDocuments(
            $need->documents,
            $membership->registration_number.'-besoin-'.$need->id,
        );
    }

    public function downloadProblematicZip(Request $request, Membership $membership, Problematic $problematic): BinaryFileResponse
    {
        abort_unless($request->user()->can('documents.view'), 403);

        $pivot = MembershipProblematic::where('membership_id', $membership->id)
            ->where('problematic_id', $problematic->id)
            ->firstOrFail();

        return $this->zipDocuments(
            $pivot->documents,
            $membership->registration_number.'-problematique-'.$problematic->code,
        );
    }

    /**
     * @param  Collection<int, MemberDocument>  $documents
     * @param  (callable(MemberDocument): string)|null  $entryNamer  Nom de base de chaque entrée (sans extension) ; par défaut le nom original du fichier.
     */
    private function zipDocuments(Collection $documents, string $baseName, ?callable $entryNamer = null): BinaryFileResponse
    {
        abort_if($documents->isEmpty(), 404);

        $zipDirectory = storage_path('app/tmp');

        if (! is_dir($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        $zipPath = $zipDirectory.'/'.$baseName.'-'.time().'.zip';

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $usedNames = [];

        foreach ($documents as $document) {
            if (! Storage::disk('public')->exists($document->file_path)) {
                continue;
            }

            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
            $label = $entryNamer
                ? $entryNamer($document)
                : pathinfo($document->original_name, PATHINFO_FILENAME);

            $entryName = $label.'.'.$extension;
            $usedNames[$label] = ($usedNames[$label] ?? 0) + 1;

            if ($usedNames[$label] > 1) {
                $entryName = $label.'-'.$usedNames[$label].'.'.$extension;
            }

            $zip->addFile(Storage::disk('public')->path($document->file_path), $entryName);
        }

        $zip->close();

        return response()->download($zipPath, $baseName.'.zip')
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
